<?php

namespace Perfumer\Contracts;

use Perfumer\Contracts\Decorator\ClassDecorator;
use Perfumer\Contracts\Decorator\MethodDecorator;
use Perfumer\Contracts\Decorator\TestCaseDecorator;
use Perfumer\Contracts\Exception\DecoratorException;
use Zend\Code\Generator\MethodGenerator;

abstract class Step extends Annotation implements ClassDecorator, MethodDecorator, TestCaseDecorator
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $arguments = [];

    /**
     * @var mixed
     */
    public $return;

    /**
     * @var string
     */
    public $if;

    /**
     * @var string
     */
    public $unless;

    /**
     * @var bool
     */
    public $validate = false;

    /**
     * @return string
     */
    public function getPrependedCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getAppendedCode()
    {
        return '';
    }

    /**
     * @param ClassBuilder $class_builder
     * @param MethodBuilder $method_builder
     * @return null|StepBuilder|StepBuilder[]
     * @throws DecoratorException
     */
    public function getBuilder(ClassBuilder $class_builder, MethodBuilder $method_builder)
    {
        $step_builder = new StepBuilder();
        $step_builder->setMethod($this->method);
        $step_builder->addPrependedCode('_step', $this->getPrependedCode());
        $step_builder->addAppendedCode('_step', $this->getAppendedCode());
        $step_builder->setValidationCondition(true);

        if ($this->if || $this->unless) {
            $condition = $this->if ?: $this->unless;

            if (is_string($condition)) {
                $method_builder->addTestVariable($condition, true);
            }

            $body_argument = $condition instanceof Variable ? $condition->asArgument() : '$' . $condition;

            if ($this->unless) {
                $body_argument = '!' . $body_argument;
            }

            $step_builder->setExtraCondition($body_argument);

            if (is_string($condition)) {
                if (!isset($method_builder->getInitialVariables()[$condition])) {
                    $method_builder->addInitialVariable($condition, 'null');
                }
            }
        }

        foreach ($this->arguments as $argument) {
            if (is_string($argument)) {
                $method_builder->addTestVariable($argument, true);
            }

            $value = $argument instanceof Variable ? $argument->asArgument() : '$' . $argument;

            $step_builder->addArgument($value);
        }

        if ($this->return) {
            if (is_array($this->return)) {
                foreach ($this->return as $value) {
                    if (is_string($value)) {
                        $method_builder->addTestVariable($value, false);
                    }
                }

                $vars = array_map(function ($v) {
                    return $v instanceof Variable ? $v->asReturn() : '$' . $v;
                }, $this->return);

                $expression = 'list(' . implode(', ', $vars) . ')';
            } else {
                if (is_string($this->return)) {
                    $method_builder->addTestVariable($this->return, false);
                }

                $expression = $this->return instanceof Variable ? $this->return->asReturn() : '$' . $this->return;
            }

            $step_builder->setReturnExpression($expression);

            $return_values = is_array($this->return) ? $this->return : [$this->return];

            foreach ($return_values as $var) {
                if (!$var instanceof Variable) {
                    $value = $this->validate ? 'true' : 'null';

                    if (isset($method_builder->getInitialVariables()[$var])) {
                        throw new DecoratorException(sprintf('%s.%s returns "%s" which is already in use.',
                            $this->name,
                            $this->method,
                            $var
                        ));
                    }

                    $method_builder->addInitialVariable($var, $value);
                }
            }
        }

        if ($this->validate) {
            $method_builder->setValidation(true);

            $step_builder->setReturnExpression('$_valid = (bool) ' . $step_builder->getReturnExpression());
        }

        return $step_builder;
    }

    /**
     * @param ClassBuilder $builder
     */
    public function decorateClass(ClassBuilder $builder): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof ClassDecorator) {
                $argument->decorateClass($builder);
            }
        }

        if (is_array($this->return)) {
            foreach ($this->return as $return) {
                if ($return instanceof ClassDecorator) {
                    $return->decorateClass($builder);
                }
            }
        } elseif ($this->return instanceof ClassDecorator) {
            $this->return->decorateClass($builder);
        }

        if ($this->if instanceof ClassDecorator) {
            $this->if->decorateClass($builder);
        }

        if ($this->unless instanceof ClassDecorator) {
            $this->unless->decorateClass($builder);
        }
    }

    /**
     * @param MethodBuilder $builder
     */
    public function decorateMethod(MethodBuilder $builder): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof MethodDecorator) {
                $argument->decorateMethod($builder);
            }
        }

        if (is_array($this->return)) {
            foreach ($this->return as $return) {
                if ($return instanceof MethodDecorator) {
                    $return->decorateMethod($builder);
                }
            }
        } elseif ($this->return instanceof MethodDecorator) {
            $this->return->decorateMethod($builder);
        }

        if ($this->if instanceof MethodDecorator) {
            $this->if->decorateMethod($builder);
        }

        if ($this->unless instanceof MethodDecorator) {
            $this->unless->decorateMethod($builder);
        }
    }

    /**
     * @param TestCaseBuilder $builder
     */
    public function decorateTestCase(TestCaseBuilder $builder): void
    {
        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$builder->hasMethod($test_method)) {
            $method = new MethodGenerator();
            $method->setVisibility('public');
            $method->setName($test_method);

            $body = '';

            foreach ($this->getReflectionMethod()->getParameters() as $parameter) {
                $body .= '$' . $parameter->getName() . ' = true;';
            }

            $method->setBody($body);

            $builder->addMethodFromGenerator($method);
        } else {
            $method = $builder->getMethod($test_method);
        }

        if ($this->if && is_string($this->if)) {
            $body = $method->getBody() . '$this->assertNotEmpty($' . $this->if . ');';
            $method->setBody($body);
        }

        if ($this->unless && is_string($this->unless)) {
            $body = $method->getBody() . '$this->assertNotEmpty($' . $this->unless . ');';
            $method->setBody($body);
        }

        foreach ($this->arguments as $argument) {
            if (is_string($argument)) {
                $body = $method->getBody() . '$this->assertNotEmpty($' . $argument . ');';
                $method->setBody($body);
            }

            if ($argument instanceof TestCaseDecorator) {
                $argument->decorateTestCase($builder);
            }
        }

        if (is_array($this->return)) {
            foreach ($this->return as $return) {
                if (is_string($return)) {
                    $body = $method->getBody() . '$' . $return . ' = true;';
                    $method->setBody($body);
                }

                if ($return instanceof TestCaseDecorator) {
                    $return->decorateTestCase($builder);
                }
            }
        } elseif (is_string($this->return)) {
            $body = $method->getBody() . '$' . $this->return . ' = true;';
            $method->setBody($body);
        } elseif ($this->return instanceof TestCaseDecorator) {
            $this->return->decorateTestCase($builder);
        }

        if ($this->if instanceof TestCaseDecorator) {
            $this->if->decorateTestCase($builder);
        }

        if ($this->unless instanceof TestCaseDecorator) {
            $this->unless->decorateTestCase($builder);
        }
    }
}
