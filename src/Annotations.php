<?php

namespace Perfumer\Contracts\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationReader;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Collection;
use Perfumer\Contracts\Decorator\ClassDecorator;
use Perfumer\Contracts\Decorator\MethodAnnotationDecorator;
use Perfumer\Contracts\Decorator\MethodDecorator;
use Perfumer\Contracts\Exception\DecoratorException;
use Perfumer\Contracts\Generator\ClassGenerator;
use Perfumer\Contracts\Generator\MethodGenerator;
use Perfumer\Contracts\Generator\StepGenerator;
use Perfumer\Contracts\Service;
use Perfumer\Contracts\Step;
use Perfumer\Contracts\Variable;
use Zend\Code\Generator\MethodGenerator as BaseMethodGenerator;
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
     * @var bool
     */
    private $is_context_call = false;

    /**
     * @var bool
     */
    private $is_injection_call = false;

    /**
     * @param ClassGenerator $generator
     * @throws DecoratorException
     */
    public function decorateClass(ClassGenerator $generator): void
    {
        if ($this->name) {
            if (!isset($generator->getContexts()[$this->name]) && !isset($generator->getInjections()[$this->name])) {
                throw new DecoratorException(sprintf('"%s" context or injection is not registered',
                    $this->name
                ));
            }
        } else {
            $this->name = 'default';

            $reflection = $generator->getContract();

            $context_class = '\\' . $reflection->getNamespaceName() . '\\' . $reflection->getShortName() . 'Context';

            if (!isset($generator->getContexts()[$this->name]) && class_exists($context_class, false)) {
                $generator->addContext($this->name, $context_class);
            }
        }

        if (isset($generator->getContexts()[$this->name])) {
            $this->is_context_call = true;
        } else {
            $this->is_injection_call = true;
        }

        $annotation_arguments = $this->arguments;

        if (isset($generator->getContexts()[$this->name]) || isset($generator->getInjections()[$this->name])) {
            if ($this->is_context_call) {
                $reflection_context = new \ReflectionClass($generator->getContexts()[$this->name]);
            } else {
                $reflection_context = new \ReflectionClass($generator->getInjections()[$this->name]);
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
                        if ($this->is_context_call && $method_annotation instanceof Inject && $parameter->getName() == $method_annotation->name) {
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
            }
        }

        parent::decorateClass($generator);
    }

    /**
     * @return null|StepGenerator|StepGenerator[]
     * @throws DecoratorException
     */
    public function getGenerator()
    {
        $generator = parent::getGenerator();

        $name = str_replace('_', '', ucwords($this->name, '_.'));

        if ($this->is_context_call) {
            $generator->setCallExpression("\$this->get{$name}Context()->");
        } else {
            $generator->setCallExpression("\$this->get{$name}()->");
        }

        return $generator;
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
     * @param ClassGenerator $generator
     * @throws DecoratorException
     */
    public function decorateClass(ClassGenerator $generator): void
    {
        if ($this->class !== null) {
            if (!class_exists($this->class) && $this->name !== 'default') {
                throw new DecoratorException(sprintf('"%s" context class not found.',
                    $this->name
                ));
            }

            if (isset($generator->getContexts()[$this->name]) || isset($generator->getInjections()[$this->name])) {
                throw new DecoratorException(sprintf('"%s" context or injected is already defined.',
                    $this->name
                ));
            }

            if ($this->name !== 'default') {
                $generator->addContext($this->name, $this->class);
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
     * @param ClassGenerator $generator
     */
    public function decorateClass(ClassGenerator $generator): void
    {
        parent::decorateClass($generator);

        $method = new BaseMethodGenerator();
        $method->setName($this->method);
        $method->setAbstract(true);
        $method->setVisibility('protected');

        foreach ($this->arguments as $item) {
            $name = $item instanceof Variable ? $item->asHeader() : $item;

            $argument = new ParameterGenerator();
            $argument->setName($name);

            $method->setParameter($argument);
        }

        $generator->addMethodFromGenerator($method);
    }

    /**
     * @return null|StepGenerator|StepGenerator[]
     */
    public function getGenerator()
    {
        $generator = parent::getGenerator();

        $generator->setCallExpression("\$this->");

        return $generator;
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Error extends Call implements MethodAnnotationDecorator
{
    /**
     * @param MethodGenerator $generator
     */
    public function decorateMethod(MethodGenerator $generator): void
    {
        parent::decorateMethod($generator);

        $generator->addInitialVariable('_return', 'null');

        if (!isset($generator->getAppendedCode()['_return'])) {
            $generator->addAppendedCode('_return', 'return $_return;');
        }
    }

    /**
     * @return null|StepGenerator|StepGenerator[]
     */
    public function getGenerator()
    {
        $generator = parent::getGenerator();

        $generator->setValidationCondition(false);
        $generator->setReturnExpression('$_return');

        return $generator;
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
     * @param ClassGenerator $generator
     * @throws DecoratorException
     */
    public function decorateClass(ClassGenerator $generator): void
    {
        if ($this->type !== null) {
            if (isset($generator->getContexts()[$this->name]) || isset($generator->getInjections()[$this->name])) {
                throw new DecoratorException(sprintf('"%s" context or injected is already defined.',
                    $this->name
                ));
            }

            $generator->addInjection($this->name, $this->type);
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
     * @param MethodGenerator $generator
     */
    public function decorateMethod(MethodGenerator $generator): void
    {
        $generator->addInitialVariable('_return', 'null');

        if (!isset($generator->getAppendedCode()['_return'])) {
            $generator->addAppendedCode('_return', 'return $_return;');
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
     * @param ClassGenerator $generator
     */
    public function decorateClass(ClassGenerator $generator): void
    {
        if (!$generator->hasProperty($this->name)) {
            $generator->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
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
     * @param ClassGenerator $generator
     */
    public function decorateClass(ClassGenerator $generator): void
    {
        if (!$generator->hasProperty($this->name)) {
            $generator->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }

        parent::decorateClass($generator);
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
