<?php

namespace Perfumer\Contracts;

class Bundle
{
    /**
     * @var \ArrayObject
     */
    private $class_builders;

    /**
     * Bundle constructor.
     */
    public function __construct()
    {
        $this->class_builders = new \ArrayObject();
    }

    /**
     * @return \ArrayObject
     */
    public function getClassBuilders(): \ArrayObject
    {
        return $this->class_builders;
    }
}
