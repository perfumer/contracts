<?php

namespace Barman;

use Barman\Generator\ClassGenerator;
use Barman\Generator\MethodGenerator;
use Barman\Generator\StepGenerator;
use Barman\Generator\TestCaseGenerator;

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
     * @var ClassGenerator
     */
    private $class_generator;

    /**
     * @var MethodGenerator
     */
    private $method_generator;

    /**
     * @var TestCaseGenerator
     */
    private $test_case_generator;

    /**
     * @var StepGenerator
     */
    private $step_generator;

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

    public function onDecorate(): void
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
     * @return ClassGenerator
     */
    public function getClassGenerator(): ?ClassGenerator
    {
        return $this->class_generator;
    }

    /**
     * @param ClassGenerator $class_generator
     */
    public function setClassGenerator(ClassGenerator $class_generator): void
    {
        $this->class_generator = $class_generator;
    }

    /**
     * @return MethodGenerator
     */
    public function getMethodGenerator(): ?MethodGenerator
    {
        return $this->method_generator;
    }

    /**
     * @param MethodGenerator $method_generator
     */
    public function setMethodGenerator(MethodGenerator $method_generator): void
    {
        $this->method_generator = $method_generator;
    }

    /**
     * @return TestCaseGenerator
     */
    public function getTestCaseGenerator(): ?TestCaseGenerator
    {
        return $this->test_case_generator;
    }

    /**
     * @param TestCaseGenerator $test_case_generator
     */
    public function setTestCaseGenerator(TestCaseGenerator $test_case_generator): void
    {
        $this->test_case_generator = $test_case_generator;
    }

    /**
     * @return StepGenerator
     */
    public function getStepGenerator(): ?StepGenerator
    {
        return $this->step_generator;
    }

    /**
     * @param StepGenerator $step_generator
     */
    public function setStepGenerator(StepGenerator $step_generator): void
    {
        $this->step_generator = $step_generator;
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
