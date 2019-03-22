<?php

namespace Perfumerlabs\Perfumer;

use Perfumerlabs\Perfumer\Data\ClassData;
use Perfumerlabs\Perfumer\Data\MethodData;
use Perfumerlabs\Perfumer\Data\StepData;
use Perfumerlabs\Perfumer\Data\TestCaseData;

class Annotation
{
    /**
     * @var \ReflectionClass
     */
    private $reflection_class;

    /**
     * @var \ReflectionMethod
     */
    private $reflection_method;

    /**
     * @var ClassData
     */
    private $class_data;

    /**
     * @var MethodData
     */
    private $method_data;

    /**
     * @var TestCaseData
     */
    private $test_case_data;

    /**
     * @var StepData
     */
    private $step_data;

    public function onCreate(): void
    {
    }

    /**
     * @return \ReflectionClass|null
     */
    public function getReflectionClass(): ?\ReflectionClass
    {
        return $this->reflection_class;
    }

    /**
     * @param \ReflectionClass $reflection_class
     */
    public function setReflectionClass(\ReflectionClass $reflection_class): void
    {
        $this->reflection_class = $reflection_class;
    }

    /**
     * @return \ReflectionMethod|null
     */
    public function getReflectionMethod(): ?\ReflectionMethod
    {
        return $this->reflection_method;
    }

    /**
     * @param \ReflectionMethod $reflection_method
     */
    public function setReflectionMethod(\ReflectionMethod $reflection_method): void
    {
        $this->reflection_method = $reflection_method;
    }

    /**
     * @return ClassData
     */
    public function getClassData(): ?ClassData
    {
        return $this->class_data;
    }

    /**
     * @param ClassData $class_data
     */
    public function setClassData(ClassData $class_data): void
    {
        $this->class_data = $class_data;
    }

    /**
     * @return MethodData
     */
    public function getMethodData(): ?MethodData
    {
        return $this->method_data;
    }

    /**
     * @param MethodData $method_data
     */
    public function setMethodData(MethodData $method_data): void
    {
        $this->method_data = $method_data;
    }

    /**
     * @return TestCaseData
     */
    public function getTestCaseData(): ?TestCaseData
    {
        return $this->test_case_data;
    }

    /**
     * @param TestCaseData $test_case_data
     */
    public function setTestCaseData(TestCaseData $test_case_data): void
    {
        $this->test_case_data = $test_case_data;
    }

    /**
     * @return StepData
     */
    public function getStepData(): ?StepData
    {
        return $this->step_data;
    }

    /**
     * @param StepData $step_data
     */
    public function setStepData(StepData $step_data): void
    {
        $this->step_data = $step_data;
    }
}
