<?php

namespace Perfumer\Contracts;

use Perfumer\Contracts\Decorator\ClassDecorator;
use Perfumer\Contracts\Decorator\MethodDecorator;
use Perfumer\Contracts\Decorator\TestCaseDecorator;
use Perfumer\Contracts\Exception\DecoratorException;
use Perfumer\Contracts\Generator\ClassGenerator;
use Perfumer\Contracts\Generator\MethodGenerator;
use Perfumer\Contracts\Generator\StepGenerator;
use Perfumer\Contracts\Generator\TestCaseGenerator;
use Zend\Code\Generator\MethodGenerator as BaseMethodGenerator;

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
     * @return null|StepGenerator|StepGenerator[]
     * @throws DecoratorException
     */
    public function getGenerator()
    {
        $step_generator = new StepGenerator();
        $step_generator->setMethod($this->method);
        $step_generator->addPrependedCode('_step', $this->getPrependedCode());
        $step_generator->addAppendedCode('_step', $this->getAppendedCode());
        $step_generator->setValidationCondition(true);

        if ($this->if || $this->unless) {
            $condition = $this->if ?: $this->unless;

            $body_argument = $condition instanceof Variable ? $condition->asArgument() : '$' . $condition;

            if ($this->unless) {
                $body_argument = '!' . $body_argument;
            }

            $step_generator->setExtraCondition($body_argument);
        }

        foreach ($this->arguments as $argument) {
            $value = $argument instanceof Variable ? $argument->asArgument() : '$' . $argument;

            $step_generator->addArgument($value);
        }

        if ($this->return) {
            if (is_array($this->return)) {
                $vars = array_map(function ($v) {
                    return $v instanceof Variable ? $v->asReturn() : '$' . $v;
                }, $this->return);

                $expression = 'list(' . implode(', ', $vars) . ')';
            } else {
                $expression = $this->return instanceof Variable ? $this->return->asReturn() : '$' . $this->return;
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
    public function decorateClass(ClassGenerator $generator): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof ClassDecorator) {
                $argument->decorateClass($generator);
            }
        }

        if (is_array($this->return)) {
            foreach ($this->return as $return) {
                if ($return instanceof ClassDecorator) {
                    $return->decorateClass($generator);
                }
            }
        } elseif ($this->return instanceof ClassDecorator) {
            $this->return->decorateClass($generator);
        }

        if ($this->if instanceof ClassDecorator) {
            $this->if->decorateClass($generator);
        }

        if ($this->unless instanceof ClassDecorator) {
            $this->unless->decorateClass($generator);
        }
    }

    /**
     * @param MethodGenerator $generator
     * @throws DecoratorException
     */
    public function decorateMethod(MethodGenerator $generator): void
    {
        if ($this->validate) {
            $generator->setValidation(true);
        }

        foreach ($this->arguments as $argument) {
            if ($argument instanceof MethodDecorator) {
                $argument->decorateMethod($generator);
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

            if ($item instanceof MethodDecorator) {
                $item->decorateMethod($generator);
            }
        }

        if ($this->if instanceof MethodDecorator) {
            $this->if->decorateMethod($generator);
        }

        if ($this->unless instanceof MethodDecorator) {
            $this->unless->decorateMethod($generator);
        }
    }

    /**
     * @param TestCaseGenerator $generator
     */
    public function decorateTestCase(TestCaseGenerator $generator): void
    {
        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$generator->hasMethod($test_method)) {
            $method = new BaseMethodGenerator();
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

            if ($argument instanceof TestCaseDecorator) {
                $argument->decorateTestCase($generator);
            }
        }

        if (is_array($this->return)) {
            foreach ($this->return as $return) {
                if (is_string($return)) {
                    $body = $method->getBody() . '$' . $return . ' = true;';
                    $method->setBody($body);
                }

                if ($return instanceof TestCaseDecorator) {
                    $return->decorateTestCase($generator);
                }
            }
        } elseif (is_string($this->return)) {
            $body = $method->getBody() . '$' . $this->return . ' = true;';
            $method->setBody($body);
        } elseif ($this->return instanceof TestCaseDecorator) {
            $this->return->decorateTestCase($generator);
        }

        if ($this->if instanceof TestCaseDecorator) {
            $this->if->decorateTestCase($generator);
        }

        if ($this->unless instanceof TestCaseDecorator) {
            $this->unless->decorateTestCase($generator);
        }
    }
}
