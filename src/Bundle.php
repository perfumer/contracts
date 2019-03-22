<?php

namespace Perfumerlabs\Perfumer;

use Perfumerlabs\Perfumer\Data\ClassData;
use Perfumerlabs\Perfumer\Data\TestCaseData;

final class Bundle
{
    /**
     * @var array
     */
    private $classes = [];

    /**
     * @var array
     */
    private $test_cases = [];

    /**
     * @return ClassData[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param array $classes
     */
    public function setClasses(array $classes): void
    {
        $this->classes = $classes;
    }

    /**
     * @param ClassData $class
     */
    public function addClass(ClassData $class): void
    {
        $this->classes[] = $class;
    }

    /**
     * @return TestCaseData[]
     */
    public function getTestCases(): array
    {
        return $this->test_cases;
    }

    /**
     * @param array $test_cases
     */
    public function setTestCases(array $test_cases): void
    {
        $this->test_cases = $test_cases;
    }

    /**
     * @param TestCaseData $test_case
     */
    public function addTestCase(TestCaseData $test_case): void
    {
        $this->test_cases[] = $test_case;
    }
}
