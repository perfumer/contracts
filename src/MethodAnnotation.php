<?php

namespace Perfumerlabs\Perfumer;

use Perfumerlabs\Perfumer\Data\ClassData;
use Perfumerlabs\Perfumer\Data\MethodData;
use Perfumerlabs\Perfumer\Data\StepData;
use Perfumerlabs\Perfumer\Data\TestCaseData;
use Zend\Code\Generator\MethodGenerator;

class MethodAnnotation extends Annotation
{
    /**
     * @var ClassData
     */
    private $_class_data;

    /**
     * @var MethodData
     */
    private $_method_data;

    /**
     * @var TestCaseData
     */
    private $_test_case_data;

    /**
     * @var StepData
     */
    private $_step_data;

    /**
     * @var bool
     */
    private $_is_returning = false;

    public function getClassData(): ?ClassData
    {
        return $this->_class_data;
    }

    public function setClassData(ClassData $class_data): void
    {
        $this->_class_data = $class_data;
    }

    public function getMethodData(): ?MethodData
    {
        return $this->_method_data;
    }

    public function setMethodData(MethodData $method_data): void
    {
        $this->_method_data = $method_data;
    }

    public function getTestCaseData(): ?TestCaseData
    {
        return $this->_test_case_data;
    }

    public function setTestCaseData(TestCaseData $test_case_data): void
    {
        $this->_test_case_data = $test_case_data;
    }

    public function getStepData(): ?StepData
    {
        return $this->_step_data;
    }

    public function setStepData(StepData $step_data): void
    {
        $this->_step_data = $step_data;
    }

    public function isReturning(): bool
    {
        return $this->_is_returning;
    }

    public function setIsReturning(bool $is_returning): void
    {
        $this->_is_returning = $is_returning;
    }

    protected function addDeclarationsToTestCaseData(array $vars): void
    {
        $method = $this->addLocalVariablesTestToTestCaseData();

        foreach ($vars as $var) {
            $body = $method->getBody() . '$' . $var . ' = true;';
            $method->setBody($body);
        }
    }

    protected function addAssertionsToTestCaseData(array $vars): void
    {
        $method = $this->addLocalVariablesTestToTestCaseData();

        foreach ($vars as $var) {
            $body = $method->getBody() . '$this->assertNotEmpty($' . $var . ');';
            $method->setBody($body);
        }
    }

    protected function addLocalVariablesTestToTestCaseData(): MethodGenerator
    {
        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$this->getTestCaseData()->getGenerator()->hasMethod($test_method)) {
            $method = new MethodGenerator();
            $method->setFinal(true);
            $method->setVisibility('public');
            $method->setName($test_method);

            $body = '';

            foreach ($this->getReflectionMethod()->getParameters() as $parameter) {
                $body .= '$' . $parameter->getName() . ' = true;';
            }

            $method->setBody($body);

            $this->getTestCaseData()->getGenerator()->addMethodFromGenerator($method);
        } else {
            $method = $this->getTestCaseData()->getGenerator()->getMethod($test_method);
        }

        return $method;
    }
}
