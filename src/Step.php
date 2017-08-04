<?php

namespace Barman;

use Barman\Exception\MutatorException;
use Barman\Keeper\ClassKeeper;
use Barman\Keeper\MethodKeeper;
use Barman\Keeper\StepKeeper;
use Barman\Keeper\TestCaseKeeper;
use Barman\Variable\ArgumentVariable;
use Barman\Variable\ReturnedVariable;
use Zend\Code\Generator\MethodGenerator;

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
     * @param ClassKeeper $class_keeper
     */
    public function setClassKeeper(ClassKeeper $class_keeper): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setClassKeeper($class_keeper);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setClassKeeper($class_keeper);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setClassKeeper($class_keeper);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setClassKeeper($class_keeper);
        }

        parent::setClassKeeper($class_keeper);
    }

    /**
     * @param MethodKeeper $method_keeper
     */
    public function setMethodKeeper(MethodKeeper $method_keeper): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setMethodKeeper($method_keeper);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setMethodKeeper($method_keeper);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setMethodKeeper($method_keeper);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setMethodKeeper($method_keeper);
        }

        parent::setMethodKeeper($method_keeper);
    }

    /**
     * @param TestCaseKeeper $test_case_keeper
     */
    public function setTestCaseKeeper(TestCaseKeeper $test_case_keeper): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setTestCaseKeeper($test_case_keeper);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setTestCaseKeeper($test_case_keeper);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setTestCaseKeeper($test_case_keeper);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setTestCaseKeeper($test_case_keeper);
        }

        parent::setTestCaseKeeper($test_case_keeper);
    }

    /**
     * @param StepKeeper $step_keeper
     */
    public function setStepKeeper(StepKeeper $step_keeper): void
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->setStepKeeper($step_keeper);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof Annotation) {
                $item->setStepKeeper($step_keeper);
            }
        }

        if ($this->if instanceof Annotation) {
            $this->if->setStepKeeper($step_keeper);
        }

        if ($this->unless instanceof Annotation) {
            $this->unless->setStepKeeper($step_keeper);
        }

        parent::setStepKeeper($step_keeper);
    }

    public function onMutate(): void
    {
        if ($this->validate) {
            $this->getMethodKeeper()->setValidation(true);
        }

        foreach ($this->arguments as $argument) {
            if ($argument instanceof Annotation) {
                $argument->onMutate();
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if (is_string($item)) {
                if (isset($this->getMethodKeeper()->getInitialVariables()[$item])) {
                    throw new MutatorException(sprintf('%s.%s returns "%s" which is already in use.',
                        $this->name,
                        $this->method,
                        $item
                    ));
                }

                $value = $this->validate ? 'true' : 'null';
                $this->getMethodKeeper()->addInitialVariable($item, $value);
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

        $this->mutateTestCaseKeeper();

        $step_generator = $this->getStepKeeper();
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

    private function mutateTestCaseKeeper(): void
    {
        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$this->getTestCaseKeeper()->getGenerator()->hasMethod($test_method)) {
            $method = new MethodGenerator();
            $method->setFinal(true);
            $method->setVisibility('public');
            $method->setName($test_method);

            $body = '';

            foreach ($this->getReflectionMethod()->getParameters() as $parameter) {
                $body .= '$' . $parameter->getName() . ' = true;';
            }

            $method->setBody($body);

            $this->getTestCaseKeeper()->getGenerator()->addMethodFromGenerator($method);
        } else {
            $method = $this->getTestCaseKeeper()->getGenerator()->getMethod($test_method);
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
