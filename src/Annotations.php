<?php

namespace Perfumer\Contracts\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationReader;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Argument;
use Perfumer\Contracts\ClassBuilder;
use Perfumer\Contracts\Collection;
use Perfumer\Contracts\ContractsException;
use Perfumer\Contracts\Decorator;
use Perfumer\Contracts\MethodBuilder;
use Perfumer\Contracts\Service;
use Perfumer\Contracts\Step;
use Perfumer\Contracts\StepBuilder;
use Perfumer\Contracts\Variable;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

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
     * @var mixed
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
     */
    public function decorate(array &$annotations): void
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Collection) {
                foreach ($annotation->steps as $step) {
                    $this->decorateStep($step);
                }
            } elseif ($annotation instanceof Step) {
                $this->decorateStep($annotation);
            }
        }
    }

    /**
     * @param Step $step
     */
    private function decorateStep(Step $step)
    {
        if ($step instanceof Call) {
            $step->aliases[$this->name] = $this->variable;
        }

        foreach ($step->arguments as $i => $argument) {
            if (is_string($argument) && $argument === $this->name) {
                $step->arguments[$i] = $this->variable;
            }
        }

        if (is_array($step->return)) {
            foreach ($step->return as $i => $return) {
                if (is_string($return) && $return === $this->name) {
                    $step->return[$i] = $this->variable;
                }
            }
        } elseif (is_string($step->return) && $step->return === $this->name) {
            $step->return = $this->variable;
        }

        if (is_string($step->if) && $step->if === $this->name) {
            $step->if = $this->variable;
        }

        if (is_string($step->unless) && $step->unless === $this->name) {
            $step->unless = $this->variable;
        }
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Call extends Step
{
    /**
     * @var array
     */
    public $aliases = [];

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder|null $method_builder
     * @throws ContractsException
     */
    public function apply(ClassBuilder $class_builder, MethodBuilder $method_builder = null): void
    {
        $contexts = $class_builder->getContexts();
        $reflection = $class_builder->getContract();

        $context_class = '\\' . $reflection->getNamespaceName() . '\\' . $reflection->getShortName() . 'Context';

        if (!isset($contexts['default']) && class_exists($context_class, false)) {
            $class_builder->addContext('default', $context_class);
        }

        parent::apply($class_builder, $method_builder);
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

        if (!isset($contexts[$this->name]) && !isset($injections[$this->name])) {
            throw new ContractsException(sprintf('%s\\%s -> %s -> %s context or injected is not registered',
                $class_builder->getNamespaceName(),
                $class_builder->getName(),
                $method_builder->getName(),
                $this->name
            ));
        }

        $annotation_arguments = $this->arguments;

        if (isset($contexts[$this->name]) || isset($injections[$this->name])) {
            $is_context = isset($contexts[$this->name]);

            if ($is_context) {
                $reflection_context = new \ReflectionClass($contexts[$this->name]);
            } else {
                $reflection_context = new \ReflectionClass($injections[$this->name]);
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
                        $class_builder->getNamespaceName(),
                        $class_builder->getName(),
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
                    $class_builder->getNamespaceName(),
                    $class_builder->getName(),
                    $method_builder->getName(),
                    $this->name
                ));
            }
        }

        foreach ($this->arguments as $i => $argument) {
            if (is_string($argument) && isset($this->aliases[$argument])) {
                $this->arguments[$i] = $this->aliases[$argument];
                $this->arguments[$i]->apply($class_builder, $method_builder);
            }
        }

        $builder = parent::getBuilder($class_builder, $method_builder);

        $name = str_replace('_', '', ucwords($this->name, '_.'));

        if (isset($contexts[$this->name])) {
            $builder->setCallExpression("\$this->get{$name}Context()->");
        } else {
            $builder->setCallExpression("\$this->get{$name}()->");
        }

        return $builder;
    }
}

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Context implements Annotation, Variable
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
        if ($this->class !== null) {
            if (!class_exists($this->class) && $this->name !== 'default') {
                throw new ContractsException(sprintf('%s\\%s -> %s: context class not found.',
                    $class_builder->getNamespaceName(),
                    $class_builder->getName(),
                    $this->name
                ));
            }

            if (isset($class_builder->getContexts()[$this->name]) || isset($class_builder->getInjections()[$this->name])) {
                throw new ContractsException(sprintf('%s\\%s -> %s: context or injected is already defined.',
                    $class_builder->getNamespaceName(),
                    $class_builder->getName(),
                    $this->name
                ));
            }

            if ($this->name !== 'default') {
                $class_builder->addContext($this->name, $this->class);
            }
        }
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
        return $this->name;
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
        $method->setAbstract(true);
        $method->setVisibility('protected');

        foreach ($this->arguments as $item) {
            $name = $item instanceof Variable ? $item->asHeader() : $item;

            $argument = new ParameterGenerator();
            $argument->setName($name);

            $method->setParameter($argument);
        }

        $class_builder->addMethodFromGenerator($method);
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        $builder = parent::getBuilder($class_builder, $method_builder);

        $builder->setCallExpression("\$this->");

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

        $method_builder->addInitialVariable('_return', 'null');

        if (!isset($method_builder->getAppendedCode()['_return'])) {
            $method_builder->addAppendedCode('_return', 'return $_return;');
        }
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
     */
    public function decorate(array &$annotations): void
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Step && $annotation->return === $this->unless) {
                $annotation->validate = true;
            }
        }
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
        if ($this->type !== null) {
            if (isset($class_builder->getContexts()[$this->name]) || isset($class_builder->getInjections()[$this->name])) {
                throw new ContractsException(sprintf('%s\\%s -> %s context or injected is already defined.',
                    $class_builder->getNamespaceName(),
                    $class_builder->getName(),
                    $this->name
                ));
            }

            $class_builder->addInjection($this->name, $this->type);
        }
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
        return $this->name;
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
        $method_builder->addInitialVariable('_return', 'null');

        if (!isset($method_builder->getAppendedCode()['_return'])) {
            $method_builder->addAppendedCode('_return', 'return $_return;');
        }
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
        return $this->name;
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
        if (!$class_builder->hasProperty($this->name)) {
            $class_builder->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }
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
        if (!$class_builder->hasProperty($this->name)) {
            $class_builder->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }

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
class Test
{
}
