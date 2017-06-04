<?php

namespace Perfumer\Contracts\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationReader;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\ClassBuilder;
use Perfumer\Contracts\Collection;
use Perfumer\Contracts\Decorator\ClassDecorator;
use Perfumer\Contracts\Decorator\MethodAnnotationDecorator;
use Perfumer\Contracts\Decorator\MethodDecorator;
use Perfumer\Contracts\Exception\DecoratorException;
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
class Alias extends Annotation implements MethodAnnotationDecorator
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
     * @param Annotation $annotation
     */
    public function decorateMethodAnnotation(Annotation $annotation): void
    {
        if ($annotation instanceof Collection) {
            foreach ($annotation->steps as $step) {
                $this->decorateStep($step);
            }
        } elseif ($annotation instanceof Step) {
            $this->decorateStep($annotation);
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
     * @param ClassBuilder $builder
     */
    public function decorateClass(ClassBuilder $builder): void
    {
        $contexts = $builder->getContexts();
        $reflection = $builder->getContract();

        $context_class = '\\' . $reflection->getNamespaceName() . '\\' . $reflection->getShortName() . 'Context';

        if (!isset($contexts['default']) && class_exists($context_class, false)) {
            $builder->addContext('default', $context_class);
        }

        parent::decorateClass($builder);
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     * @throws DecoratorException
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        if ($this->name === null) {
            $this->name = 'default';
        }

        $contexts = $class_builder->getContexts();
        $injections = $class_builder->getInjections();

        if (!isset($contexts[$this->name]) && !isset($injections[$this->name])) {
            throw new DecoratorException(sprintf('"%s" context or injection is not registered',
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
                    throw new DecoratorException(sprintf('%s.%s has excessive arguments.',
                        $this->name,
                        $this->method
                    ));
                }

                if ($tmp_arguments) {
                    $this->arguments = $tmp_arguments;
                }
            }

            if ($method_found === false) {
                throw new DecoratorException(sprintf('method "%s" is not found in "%s".',
                    $this->method,
                    $this->name
                ));
            }
        }

        foreach ($this->arguments as $i => $argument) {
            if (is_string($argument) && isset($this->aliases[$argument])) {
                $this->arguments[$i] = $this->aliases[$argument];

                $argument = $this->arguments[$i];

                if ($argument instanceof ClassDecorator) {
                    $argument->decorateClass($class_builder);
                }

                if ($argument instanceof MethodDecorator) {
                    $argument->decorateMethod($method_builder);
                }
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
class Context extends Annotation implements Variable, ClassDecorator
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
     * @param ClassBuilder $builder
     * @throws DecoratorException
     */
    public function decorateClass(ClassBuilder $builder): void
    {
        if ($this->class !== null) {
            if (!class_exists($this->class) && $this->name !== 'default') {
                throw new DecoratorException(sprintf('"%s" context class not found.',
                    $this->name
                ));
            }

            if (isset($builder->getContexts()[$this->name]) || isset($builder->getInjections()[$this->name])) {
                throw new DecoratorException(sprintf('"%s" context or injected is already defined.',
                    $this->name
                ));
            }

            if ($this->name !== 'default') {
                $builder->addContext($this->name, $this->class);
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
     * @throws DecoratorException
     */
    public function asReturn(): string
    {
        throw new DecoratorException('@Context annotation can not be used for "return".');
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Custom extends Step
{
    /**
     * @param ClassBuilder $builder
     */
    public function decorateClass(ClassBuilder $builder): void
    {
        parent::decorateClass($builder);

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

        $builder->addMethodFromGenerator($method);
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
class Error extends Call implements MethodAnnotationDecorator
{
    /**
     * @param MethodBuilder $builder
     */
    public function decorateMethod(MethodBuilder $builder): void
    {
        parent::decorateMethod($builder);

        $builder->addInitialVariable('_return', 'null');

        if (!isset($builder->getAppendedCode()['_return'])) {
            $builder->addAppendedCode('_return', 'return $_return;');
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
     * @param Annotation $annotation
     */
    public function decorateMethodAnnotation(Annotation $annotation): void
    {
        if ($annotation instanceof Step && $annotation->return === $this->unless) {
            $annotation->validate = true;
        }
    }
}

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Inject extends Annotation implements Variable, ClassDecorator
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
     * @param ClassBuilder $builder
     * @throws DecoratorException
     */
    public function decorateClass(ClassBuilder $builder): void
    {
        if ($this->type !== null) {
            if (isset($builder->getContexts()[$this->name]) || isset($builder->getInjections()[$this->name])) {
                throw new DecoratorException(sprintf('"%s" context or injected is already defined.',
                    $this->name
                ));
            }

            $builder->addInjection($this->name, $this->type);
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
     * @throws DecoratorException
     */
    public function asReturn(): string
    {
        throw new DecoratorException('@Inject annotation can not be used for "return".');
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Output extends Annotation implements Variable, MethodDecorator
{
    /**
     * @throws DecoratorException
     */
    public function asArgument(): string
    {
        throw new DecoratorException('@Output annotation can not be used for "args".');
    }

    /**
     * @throws DecoratorException
     */
    public function asHeader(): string
    {
        throw new DecoratorException('@Output annotation can not be used for "args".');
    }

    /**
     * @return string
     */
    public function asReturn(): string
    {
        return '$_return';
    }

    /**
     * @param MethodBuilder $builder
     */
    public function decorateMethod(MethodBuilder $builder): void
    {
        $builder->addInitialVariable('_return', 'null');

        if (!isset($builder->getAppendedCode()['_return'])) {
            $builder->addAppendedCode('_return', 'return $_return;');
        }
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Property extends Annotation implements Variable, ClassDecorator
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
     * @param ClassBuilder $builder
     */
    public function decorateClass(ClassBuilder $builder): void
    {
        if (!$builder->hasProperty($this->name)) {
            $builder->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
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
     * @param ClassBuilder $builder
     */
    public function decorateClass(ClassBuilder $builder): void
    {
        if (!$builder->hasProperty($this->name)) {
            $builder->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }

        parent::decorateClass($builder);
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
