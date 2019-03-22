<?php

namespace Perfumerlabs\Perfumer;

use Perfumerlabs\Perfumer\Data\MethodData;
use Perfumerlabs\Perfumer\Data\StepData;
use Perfumerlabs\Perfumer\Data\TestCaseData;

class MethodAnnotation extends Annotation
{
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
}
