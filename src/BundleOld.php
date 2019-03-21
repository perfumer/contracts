<?php

namespace Barman;

use Barman\Keeper\ClassKeeper;
use Barman\Keeper\TestCaseKeeper;

final class BundleOld
{
    /**
     * @var array
     */
    private $class_keepers = [];

    /**
     * @var array
     */
    private $test_case_keepers = [];

    /**
     * @return ClassKeeper[]
     */
    public function getClassKeepers(): array
    {
        return $this->class_keepers;
    }

    /**
     * @param array $class_keepers
     */
    public function setClassKeepers(array $class_keepers): void
    {
        $this->class_keepers = $class_keepers;
    }

    /**
     * @param ClassKeeper $class_keeper
     */
    public function addClassKeeper(ClassKeeper $class_keeper): void
    {
        $this->class_keepers[] = $class_keeper;
    }

    /**
     * @return TestCaseKeeper[]
     */
    public function getTestCaseKeepers(): array
    {
        return $this->test_case_keepers;
    }

    /**
     * @param array $test_case_keepers
     */
    public function setTestCaseKeepers(array $test_case_keepers): void
    {
        $this->test_case_keepers = $test_case_keepers;
    }

    /**
     * @param TestCaseKeeper $test_case_keeper
     */
    public function addTestCaseKeeper(TestCaseKeeper $test_case_keeper): void
    {
        $this->test_case_keepers[] = $test_case_keeper;
    }
}
