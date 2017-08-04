<?php

namespace Barman;

use Barman\Keeper\ClassKeeper;
use Barman\Keeper\MethodKeeper;
use Barman\Keeper\StepKeeper;
use Barman\Keeper\TestCaseKeeper;

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
     * @var ClassKeeper
     */
    private $class_keeper;

    /**
     * @var MethodKeeper
     */
    private $method_keeper;

    /**
     * @var TestCaseKeeper
     */
    private $test_case_keeper;

    /**
     * @var StepKeeper
     */
    private $step_keeper;

    /**
     * @var bool
     */
    private $is_class_annotation = false;

    /**
     * @var bool
     */
    private $is_method_annotation = false;

    public function onCreate(): void
    {
    }

    public function onMutate(): void
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
     * @return ClassKeeper
     */
    public function getClassKeeper(): ?ClassKeeper
    {
        return $this->class_keeper;
    }

    /**
     * @param ClassKeeper $class_keeper
     */
    public function setClassKeeper(ClassKeeper $class_keeper): void
    {
        $this->class_keeper = $class_keeper;
    }

    /**
     * @return MethodKeeper
     */
    public function getMethodKeeper(): ?MethodKeeper
    {
        return $this->method_keeper;
    }

    /**
     * @param MethodKeeper $method_keeper
     */
    public function setMethodKeeper(MethodKeeper $method_keeper): void
    {
        $this->method_keeper = $method_keeper;
    }

    /**
     * @return TestCaseKeeper
     */
    public function getTestCaseKeeper(): ?TestCaseKeeper
    {
        return $this->test_case_keeper;
    }

    /**
     * @param TestCaseKeeper $test_case_keeper
     */
    public function setTestCaseKeeper(TestCaseKeeper $test_case_keeper): void
    {
        $this->test_case_keeper = $test_case_keeper;
    }

    /**
     * @return StepKeeper
     */
    public function getStepKeeper(): ?StepKeeper
    {
        return $this->step_keeper;
    }

    /**
     * @param StepKeeper $step_keeper
     */
    public function setStepKeeper(StepKeeper $step_keeper): void
    {
        $this->step_keeper = $step_keeper;
    }

    /**
     * @return bool
     */
    public function isClassAnnotation(): bool
    {
        return $this->is_class_annotation;
    }

    /**
     * @param bool $is_class_annotation
     */
    public function setIsClassAnnotation(bool $is_class_annotation): void
    {
        $this->is_class_annotation = $is_class_annotation;
    }

    /**
     * @return bool
     */
    public function isMethodAnnotation(): bool
    {
        return $this->is_method_annotation;
    }

    /**
     * @param bool $is_method_annotation
     */
    public function setIsMethodAnnotation(bool $is_method_annotation): void
    {
        $this->is_method_annotation = $is_method_annotation;
    }

    /**
     * @return bool
     */
    public function isInnerAnnotation(): bool
    {
        return !$this->is_class_annotation && !$this->is_method_annotation;
    }
}
