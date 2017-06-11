<?php

namespace Perfumer\Contracts;

use Perfumer\Contracts\Generator\ClassGenerator;
use Perfumer\Contracts\Generator\TestCaseGenerator;

final class Bundle
{
    /**
     * @var array
     */
    private $class_generators = [];

    /**
     * @var array
     */
    private $test_case_generators = [];

    /**
     * @return ClassGenerator[]
     */
    public function getClassGenerators(): array
    {
        return $this->class_generators;
    }

    /**
     * @param array $class_generators
     */
    public function setClassGenerators(array $class_generators): void
    {
        $this->class_generators = $class_generators;
    }

    /**
     * @param ClassGenerator $class_generator
     */
    public function addClassGenerator(ClassGenerator $class_generator): void
    {
        $this->class_generators[] = $class_generator;
    }

    /**
     * @return TestCaseGenerator[]
     */
    public function getTestCaseGenerators(): array
    {
        return $this->test_case_generators;
    }

    /**
     * @param array $test_case_generators
     */
    public function setTestCaseGenerators(array $test_case_generators): void
    {
        $this->test_case_generators = $test_case_generators;
    }

    /**
     * @param TestCaseGenerator $test_case_generator
     */
    public function addTestCaseGenerator(TestCaseGenerator $test_case_generator): void
    {
        $this->test_case_generators[] = $test_case_generator;
    }
}
