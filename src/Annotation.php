<?php

namespace Perfumer\Contracts;

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
}
