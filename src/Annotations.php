<?php

namespace Perfumer\Component\Contracts\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationReader;
use Perfumer\Component\Contracts\Annotation;
use Perfumer\Component\Contracts\ClassBuilder;
use Perfumer\Component\Contracts\ContractsException;
use Perfumer\Component\Contracts\Decorator;
use Perfumer\Component\Contracts\MethodBuilder;
use Perfumer\Component\Contracts\Service;
use Perfumer\Component\Contracts\Step;
use Perfumer\Component\Contracts\StepBuilder;
use Perfumer\Component\Contracts\Variable;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Alias implements Annotation, Decorator
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var Variable
     */
    public $variable;

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
    }

    /**
     * @param array $annotations
     * @return array
     */
    public function decorate(array $annotations): array
    {
        foreach ($annotations as &$annotation) {
            if ($annotation instanceof Step) {
                foreach ($annotation->arguments as &$argument) {
                    if (is_string($argument) && $argument === $this->name) {
                        $argument = $this->variable;
                    }
                }

                if (is_array($annotation->return)) {
                    foreach ($annotation->return as &$return) {
                        if (is_string($return) && $return === $this->name) {
                            $return = $this->variable;
                        }
                    }
                } elseif (is_string($annotation->return) && $annotation->return === $this->name) {
                    $annotation->return = $this->variable;
                }

                if (is_string($annotation->if) && $annotation->if === $this->name) {
                    $annotation->if = $this->variable;
                }

                if (is_string($annotation->unless) && $annotation->unless === $this->name) {
                    $annotation->unless = $this->variable;
                }
            }
        }

        return $annotations;
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Call extends Step
{
    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     * @throws ContractsException
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        $contexts = $class_builder->getContexts();

        $class = '\\' . $class_builder->getNamespace() . '\\' . $class_builder->getClassName() . 'Context';

        if ($this->name === null && !$contexts->offsetGet('default') && class_exists($class, false)) {
            $context = new Context();
            $context->name = 'default';
            $context->class = '\\' . $class . 'Context';

            $class_builder->getContexts()->offsetSet('default', $context);
        }
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     * @throws ContractsException
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        if ($this->name === null) {
            $this->name = 'default';
        }

        $contexts = $class_builder->getContexts();
        $injections = $class_builder->getInjections();

        if (!$contexts->offsetExists($this->name) && !$injections->offsetExists($this->name)) {
            throw new ContractsException(sprintf('%s\\%s -> %s -> %s context or injected is not registered',
                $class_builder->getNamespace(),
                $class_builder->getClassName(),
                $method_builder->getName(),
                $this->name
            ));
        }

        $annotation_arguments = $this->arguments;

        if ($contexts->offsetExists($this->name) || $injections->offsetExists($this->name)) {
            $is_context = $contexts->offsetExists($this->name);

            if ($is_context) {
                $reflection_context = new \ReflectionClass($contexts->offsetGet($this->name)->class);
            } else {
                $reflection_context = new \ReflectionClass($injections->offsetGet($this->name)->class);
            }

            $method_found = false;

            foreach ($reflection_context->getMethods() as $method) {
                if ($method->getName() !== $this->method) {
                    continue;
                }

                $method_found = true;

                $reader = new AnnotationReader();
                $method_annotations = $reader->getMethodAnnotations($method);
                $tmp_arguments = [];

                foreach ($method->getParameters() as $parameter) {
                    $found = false;

                    foreach ($method_annotations as $method_annotation) {
                        if ($is_context && $method_annotation instanceof Inject && $parameter->getName() == $method_annotation->name) {
                            $tmp_arguments[] = $method_annotation->variable;
                            $found = true;
                        }
                    }

                    if (!$found) {
                        if ($this->arguments) {
                            if ($annotation_arguments) {
                                $tmp_arguments[] = array_shift($annotation_arguments);
                            }
                        } elseif (!$parameter->isOptional()) {
                            $tmp_arguments[] = $parameter->getName();
                        }
                    }
                }

                if (count($annotation_arguments) > 0) {
                    throw new ContractsException(sprintf('%s\\%s -> %s -> %s.%s has excessive arguments.',
                        $class_builder->getNamespace(),
                        $class_builder->getClassName(),
                        $method_builder->getName(),
                        $this->name,
                        $this->method
                    ));
                }

                if ($tmp_arguments) {
                    $this->arguments = $tmp_arguments;
                }
            }

            if ($method_found === false) {
                throw new ContractsException(sprintf('Method "%s" is not found in %s\\%s -> %s -> %s.',
                    $this->method,
                    $class_builder->getNamespace(),
                    $class_builder->getClassName(),
                    $method_builder->getName(),
                    $this->name
                ));
            }
        }

        $builder = parent::getBuilder($class_builder, $method_builder);

        $name = str_replace('_.', '', ucwords($this->name, '_.'));

        if ($contexts->offsetExists($this->name)) {
            $builder->setCallExpression("\$this->get{$name}Context()->");
        } else {
            $builder->setCallExpression("\$this->get{$name}->");
        }

        return $builder;
    }
}

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Context implements Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $class;

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     * @throws ContractsException
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        if ($class_builder->getContexts()->offsetExists($this->name) || $class_builder->getInjections()->offsetExists($this->name)) {
            throw new ContractsException(sprintf('%s\\%s -> %s context or injected is already defined.',
                $class_builder->getNamespace(),
                $class_builder->getClassName(),
                $this->name
            ));
        }

        $class_builder->getContexts()->offsetSet($this->name, $this->class);
    }

    /**
     * @return string
     */
    public function asArgument(): string
    {
        return '$this->get' . str_replace('_', '', ucwords($this->name, '_')) . 'Context()';
    }

    /**
     * @return string
     */
    public function asHeader(): string
    {
        return '$' . $this->name;
    }

    /**
     * @throws ContractsException
     */
    public function asReturn(): string
    {
        throw new ContractsException('@Context annotation can not be used for "return".');
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Custom extends Step
{
    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        parent::apply($class_builder, $method_builder);

        $method = new MethodBuilder();
        $method->setName($this->method);
        $method->setIsAbstract(true);
        $method->setAccess('protected');

        foreach ($this->arguments as $argument) {
            $value = $argument instanceof Variable ? $argument->asHeader() : $argument;
            $method->getArguments()->offsetSet($value, null);
        }

        $class_builder->getMethods()->append($method);
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        $builder = parent::getBuilder($class_builder, $method_builder);

        $builder->setCallExpression("\$this->{$this->method}->");

        return $builder;
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Error extends Call implements Decorator
{
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        parent::apply($class_builder, $method_builder);

        $method_builder->getInitialVariables()->offsetSet('_return', null);
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        $builder = parent::getBuilder($class_builder, $method_builder);

        $builder->setValidationCondition(false);
        $builder->setReturnExpression('$_return');

        return $builder;
    }

    /**
     * @param array $annotations
     * @return array
     */
    public function decorate(array $annotations): array
    {
        foreach ($annotations as &$annotation) {
            if ($annotation instanceof Step && $annotation->return === $this->unless) {
                $annotation->validate = true;
            }
        }

        return $annotations;
    }
}

/**
 * @Annotation
 * @Target("CLASS")
 */
class Extend implements Annotation
{
    public $class;

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        $class_builder->setParentClass($this->class);
    }
}

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Inject implements Variable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $variable;

    /**
     * @var string
     */
    public $type;

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     * @throws ContractsException
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        if ($class_builder->getContexts()->offsetExists($this->name) || $class_builder->getInjections()->offsetExists($this->name)) {
            throw new ContractsException(sprintf('%s\\%s -> %s context or injected is already defined.',
                $class_builder->getNamespace(),
                $class_builder->getClassName(),
                $this->name
            ));
        }

        $class_builder->getInjections()->offsetSet($this->name, $this->type);
    }

    /**
     * @return string
     */
    public function asArgument(): string
    {
        return '$this->_injected_' . $this->name;
    }

    /**
     * @return string
     */
    public function asHeader(): string
    {
        return '$' . $this->name;
    }

    /**
     * @throws ContractsException
     */
    public function asReturn(): string
    {
        throw new ContractsException('@Inject annotation can not be used for "return".');
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Output implements Variable
{
    /**
     * @throws ContractsException
     */
    public function asArgument(): string
    {
        throw new ContractsException('@Output annotation can not be used for "args".');
    }

    /**
     * @throws ContractsException
     */
    public function asHeader(): string
    {
        throw new ContractsException('@Output annotation can not be used for "args".');
    }

    /**
     * @return string
     */
    public function asReturn(): string
    {
        return '$_return';
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        $method_builder->getInitialVariables()->offsetSet('_return', null);
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Property implements Variable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @param array $values
     */
    public function __construct($values)
    {
        $this->name = $values['value'];
    }

    /**
     * @return string
     */
    public function asArgument(): string
    {
        return '$this->' . $this->name;
    }

    /**
     * @return string
     */
    public function asHeader(): string
    {
        return '$' . $this->name;
    }

    /**
     * @return string
     */
    public function asReturn(): string
    {
        return '$this->' . $this->name;
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        $class_builder->getProtectedProperties()->offsetSet($this->name, null);
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceObject extends Service
{
    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return '$' . $this->name . '->';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceParent extends Service
{
    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return 'parent::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceProperty extends Service
{
    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        $class_builder->getProtectedProperties()->append($this->name);

        parent::apply($class_builder, $method_builder);
    }

    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return '$this->' . $this->name . '->';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceSelf extends Service
{
    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return 'self::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceStatic extends Service
{
    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return $this->name . '::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceThis extends Service
{
    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return '$this->';
    }
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Skip implements Annotation
{
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
    }
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Test
{
}
