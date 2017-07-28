<?php

namespace Barman;

use Barman\Exception\MutatorException;
use Barman\Generator\ClassGenerator;
use Barman\Generator\MethodGenerator;
use Barman\Generator\StepGenerator;
use Barman\Generator\TestCaseGenerator;
use Barman\Variable\ArgumentVariable;
use Barman\Variable\ReturnedVariable;
use Zend\Code\Generator\MethodGenerator as BaseMethodGenerator;

abstract class Step extends Annotation
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
     * @param \ReflectionClass $reflection_class
     */
    public function setReflectionClass(\ReflectionClass $reflection_class): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setReflectionClass($reflection_class);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setReflectionClass($reflection_class);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setReflectionClass($reflection_class);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setReflectionClass($reflection_class);
        }

        parent::setReflectionClass($reflection_class);
    }

    /**
     * @param \ReflectionMethod $reflection_method
     */
    public function setReflectionMethod(\ReflectionMethod $reflection_method): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setReflectionMethod($reflection_method);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setReflectionMethod($reflection_method);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setReflectionMethod($reflection_method);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setReflectionMethod($reflection_method);
        }

        parent::setReflectionMethod($reflection_method);
    }

    /**
     * @param ClassGenerator $class_generator
     */
    public function setClassGenerator(ClassGenerator $class_generator): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setClassGenerator($class_generator);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setClassGenerator($class_generator);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setClassGenerator($class_generator);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setClassGenerator($class_generator);
        }

        parent::setClassGenerator($class_generator);
    }

    /**
     * @param MethodGenerator $method_generator
     */
    public function setMethodGenerator(MethodGenerator $method_generator): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setMethodGenerator($method_generator);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setMethodGenerator($method_generator);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setMethodGenerator($method_generator);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setMethodGenerator($method_generator);
        }

        parent::setMethodGenerator($method_generator);
    }

    /**
     * @param TestCaseGenerator $test_case_generator
     */
    public function setTestCaseGenerator(TestCaseGenerator $test_case_generator): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setTestCaseGenerator($test_case_generator);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setTestCaseGenerator($test_case_generator);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setTestCaseGenerator($test_case_generator);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setTestCaseGenerator($test_case_generator);
        }

        parent::setTestCaseGenerator($test_case_generator);
    }

    /**
     * @param StepGenerator $step_generator
     */
    public function setStepGenerator(StepGenerator $step_generator): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setStepGenerator($step_generator);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setStepGenerator($step_generator);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setStepGenerator($step_generator);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setStepGenerator($step_generator);
        }

        parent::setStepGenerator($step_generator);
    }

    public function onMutate(): void
    {
        if ($this->validate) {
            $this->getMethodGenerator()->setValidation(true);
        }

        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->onMutate();
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if (is_string($item)) {
                if (isset($this->getMethodGenerator()->getInitialVariables()[$item])) {
                    throw new MutatorException(sprintf('%s.%s returns "%s" which is already in use.',
                        $this->name,
                        $this->method,
                        $item
                    ));
                }

                $value = $this->validate ? 'true' : 'null';
                $this->getMethodGenerator()->addInitialVariable($item, $value);
            }

            if ($item instanceof Annotation) {
                $item->onMutate();
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->onMutate();
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->onMutate();
        }

        $this->mutateTestCaseGenerator();

        $step_generator = $this->getStepGenerator();
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
    }

    private function mutateTestCaseGenerator(): void
    {
        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$this->getTestCaseGenerator()->hasMethod($test_method)) {
            $method = new BaseMethodGenerator();
            $method->setFinal(true);
            $method->setVisibility('public');
            $method->setName($test_method);

            $body = '';

            foreach ($this->getReflectionMethod()->getParameters() as $parameter) {
                $body .= '$' . $parameter->getName() . ' = true;';
            }

            $method->setBody($body);

            $this->getTestCaseGenerator()->addMethodFromGenerator($method);
        } else {
            $method = $this->getTestCaseGenerator()->getMethod($test_method);
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
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if (is_string($item)) {
                $body = $method->getBody() . '$' . $item . ' = true;';
                $method->setBody($body);
            }
        }
    }
}
