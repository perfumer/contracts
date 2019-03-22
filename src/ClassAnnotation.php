<?php

namespace Perfumerlabs\Perfumer;

use Perfumerlabs\Perfumer\Data\ClassData;
use Perfumerlabs\Perfumer\Data\TestCaseData;

class ClassAnnotation extends Annotation
{
    /**
     * @var ClassData
     */
    private $_class_data;

    /**
     * @var TestCaseData
     */
    private $_test_case_data;

    public function getClassData(): ?ClassData
    {
        return $this->_class_data;
    }

    public function setClassData(ClassData $class_data): void
    {
        $this->_class_data = $class_data;
    }

    public function getTestCaseData(): ?TestCaseData
    {
        return $this->_test_case_data;
    }

    public function setTestCaseData(TestCaseData $test_case_data): void
    {
        $this->_test_case_data = $test_case_data;
    }
}
