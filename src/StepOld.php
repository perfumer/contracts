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

abstract class Step extends AnnotationOld
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
            if ($argument instanceof AnnotationOld) {
                $argument->setReflectionClass($reflection_class);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof AnnotationOld) {
                $item->setReflectionClass($reflection_class);
            }
        }

        if ($this->if instanceof AnnotationOld) {
            $this->if->setReflectionClass($reflection_class);
        }

        if ($this->unless instanceof AnnotationOld) {
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
            if ($argument instanceof AnnotationOld) {
                $argument->setReflectionMethod($reflection_method);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof AnnotationOld) {
                $item->setReflectionMethod($reflection_method);
            }
        }

        if ($this->if instanceof AnnotationOld) {
            $this->if->setReflectionMethod($reflection_method);
        }

        if ($this->unless instanceof AnnotationOld) {
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
            if ($argument instanceof AnnotationOld) {
                $argument->setClassKeeper($class_keeper);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof AnnotationOld) {
                $item->setClassKeeper($class_keeper);
            }
        }

        if ($this->if instanceof AnnotationOld) {
            $this->if->setClassKeeper($class_keeper);
        }

        if ($this->unless instanceof AnnotationOld) {
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
            if ($argument instanceof AnnotationOld) {
                $argument->setMethodKeeper($method_keeper);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof AnnotationOld) {
                $item->setMethodKeeper($method_keeper);
            }
        }

        if ($this->if instanceof AnnotationOld) {
            $this->if->setMethodKeeper($method_keeper);
        }

        if ($this->unless instanceof AnnotationOld) {
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
            if ($argument instanceof AnnotationOld) {
                $argument->setTestCaseKeeper($test_case_keeper);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof AnnotationOld) {
                $item->setTestCaseKeeper($test_case_keeper);
            }
        }

        if ($this->if instanceof AnnotationOld) {
            $this->if->setTestCaseKeeper($test_case_keeper);
        }

        if ($this->unless instanceof AnnotationOld) {
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
            if ($argument instanceof AnnotationOld) {
                $argument->setStepKeeper($step_keeper);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $item) {
            if ($item instanceof AnnotationOld) {
                $item->setStepKeeper($step_keeper);
            }
        }

        if ($this->if instanceof AnnotationOld) {
            $this->if->setStepKeeper($step_keeper);
        }

        if ($this->unless instanceof AnnotationOld) {
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
            if ($argument instanceof AnnotationOld) {
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

            if ($item instanceof AnnotationOld) {
                $item->onMutate();
            }
        }

        if ($this->if instanceof AnnotationOld) {
            $this->if->onMutate();
        }

        if ($this->unless instanceof AnnotationOld) {
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

        if ($this->return && !$step_generator->getReturnExpression()) {
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
