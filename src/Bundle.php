<?php

namespace Perfumer\Contracts;

class Bundle
{
    /**
     * @var \ArrayObject
     */
    private $class_builders;

    /**
     * @var \ArrayObject
     */
    private $test_case_builders;

    /**
     * Bundle constructor.
     */
    public function __construct()
    {
        $this->class_builders = new \ArrayObject();
        $this->test_case_builders = new \ArrayObject();
    }

    /**
     * @return \ArrayObject
     */
    public function getClassBuilders(): \ArrayObject
    {
        return $this->class_builders;
    }

    /**
     * @return \ArrayObject
     */
    public function getTestCaseBuilders(): \ArrayObject
    {
        return $this->test_case_builders;
    }
}
