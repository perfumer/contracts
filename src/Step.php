<?php

namespace Perfumer\Contracts;

use Perfumer\Contracts\Decorator\ClassGeneratorDecorator;
use Perfumer\Contracts\Decorator\MethodGeneratorDecorator;
use Perfumer\Contracts\Decorator\TestCaseGeneratorDecorator;
use Perfumer\Contracts\Exception\DecoratorException;
use Perfumer\Contracts\Generator\ClassGenerator;
use Perfumer\Contracts\Generator\MethodGenerator;
use Perfumer\Contracts\Generator\StepGenerator;
use Perfumer\Contracts\Generator\TestCaseGenerator;
use Perfumer\Contracts\Variable\ArgumentVariable;
use Perfumer\Contracts\Variable\ReturnedVariable;
use Zend\Code\Generator\MethodGenerator as BaseMethodGenerator;

abstract class Step extends Annotation implements ClassGeneratorDecorator, MethodGeneratorDecorator, TestCaseGeneratorDecorator
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
     * @var mixed
     */
    public $if;

    /**
     * @var mixed
     */
    public $unless;

    /**
     * @var bool
     */
    public $validate = false;

    /**
     * @return null|StepGenerator|StepGenerator[]
     * @throws DecoratorException
     */
    public function getGenerator()
    {
        $step_generator = new StepGenerator();
        $step_generator->setMethod($this->method);
        $step_generator->setValidationCondition(true);

        if ($this->if || $this->unless) {
            $condition = $this->if ?: $this->unless;

            $body_argument = $condition instanceof ArgumentVariable ? $condition->getArgumentVariableExpression() : '$' . $condition;

            if ($this->unless) {
                $body_argument = '!' . $body_argument;
            }

            $step_generator->setExtraCondition($body_argument);
        }

        foreach ($this->arguments as $argument) {
            $value = $argument instanceof ArgumentVariable ? $argument->getArgumentVariableExpression() : '$' . $argument;

            $step_generator->addArgument($value);
        }

        if ($this->return) {
            if (is_array($this->return)) {
                $vars = array_map(function ($v) {
                    return $v instanceof ReturnedVariable ? $v->getReturnedVariableExpression() : '$' . $v;
                }, $this->return);

                $expression = 'list(' . implode(', ', $vars) . ')';
            } else {
                $expression = $this->return instanceof ReturnedVariable ? $this->return->getReturnedVariableExpression() : '$' . $this->return;
            }

            $step_generator->setReturnExpression($expression);
        }

        if ($this->validate) {
            $step_generator->setReturnExpression('$_valid = (bool) ' . $step_generator->getReturnExpression());
        }

        return $step_generator;
    }

    /**
     * @param ClassGenerator $generator
     */
    public function decorateClassGenerator(ClassGenerator $generator): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof ClassGeneratorDecorator) {
                $argument->decorateClassGenerator($generator);
            }
        }

        if (is_array($this->return)) {
            foreach ($this->return as $return) {
                if ($return instanceof ClassGeneratorDecorator) {
                    $return->decorateClassGenerator($generator);
                }
            }
        } elseif ($this->return instanceof ClassGeneratorDecorator) {
            $this->return->decorateClassGenerator($generator);
        }

        if ($this->if instanceof ClassGeneratorDecorator) {
            $this->if->decorateClassGenerator($generator);
        }

        if ($this->unless instanceof ClassGeneratorDecorator) {
            $this->unless->decorateClassGenerator($generator);
        }
    }

    /**
     * @param MethodGenerator $generator
     * @throws DecoratorException
     */
    public function decorateMethodGenerator(MethodGenerator $generator): void
    {
        if ($this->validate) {
            $generator->setValidation(true);
        }

        foreach ($this->arguments as $argument) {
            if ($argument instanceof MethodGeneratorDecorator) {
                $argument->decorateMethodGenerator($generator);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if (is_string($item)) {
                if (isset($generator->getInitialVariables()[$item])) {
                    throw new DecoratorException(sprintf('%s.%s returns "%s" which is already in use.',
                        $this->name,
                        $this->method,
                        $item
                    ));
                }

                $value = $this->validate ? 'true' : 'null';
                $generator->addInitialVariable($item, $value);
            }

            if ($item instanceof MethodGeneratorDecorator) {
                $item->decorateMethodGenerator($generator);
            }
        }

        if ($this->if instanceof MethodGeneratorDecorator) {
            $this->if->decorateMethodGenerator($generator);
        }

        if ($this->unless instanceof MethodGeneratorDecorator) {
            $this->unless->decorateMethodGenerator($generator);
        }
    }

    /**
     * @param TestCaseGenerator $generator
     */
    public function decorateTestCaseGenerator(TestCaseGenerator $generator): void
    {
        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$generator->hasMethod($test_method)) {
            $method = new BaseMethodGenerator();
            $method->setFinal(true);
            $method->setVisibility('public');
            $method->setName($test_method);

            $body = '';

            foreach ($this->getReflectionMethod()->getParameters() as $parameter) {
                $body .= '$' . $parameter->getName() . ' = true;';
            }

            $method->setBody($body);

            $generator->addMethodFromGenerator($method);
        } else {
            $method = $generator->getMethod($test_method);
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

            if ($argument instanceof TestCaseGeneratorDecorator) {
                $argument->decorateTestCaseGenerator($generator);
            }
        }

        if (is_array($this->return)) {
            foreach ($this->return as $return) {
                if (is_string($return)) {
                    $body = $method->getBody() . '$' . $return . ' = true;';
                    $method->setBody($body);
                }

                if ($return instanceof TestCaseGeneratorDecorator) {
                    $return->decorateTestCaseGenerator($generator);
                }
            }
        } elseif (is_string($this->return)) {
            $body = $method->getBody() . '$' . $this->return . ' = true;';
            $method->setBody($body);
        } elseif ($this->return instanceof TestCaseGeneratorDecorator) {
            $this->return->decorateTestCaseGenerator($generator);
        }

        if ($this->if instanceof TestCaseGeneratorDecorator) {
            $this->if->decorateTestCaseGenerator($generator);
        }

        if ($this->unless instanceof TestCaseGeneratorDecorator) {
            $this->unless->decorateTestCaseGenerator($generator);
        }
    }
}
