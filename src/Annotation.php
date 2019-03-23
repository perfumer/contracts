<?php

namespace Perfumerlabs\Perfumer;

class Annotation
{
    /**
     * @var \ReflectionClass
     */
    private $_reflection_class;

    /**
     * @var \ReflectionMethod
     */
    private $_reflection_method;

    public function onCreate(): void
    {
    }

    public function onBuild(): void
    {
    }

    public function getReflectionClass(): ?\ReflectionClass
    {
        return $this->_reflection_class;
    }

    public function setReflectionClass(\ReflectionClass $reflection_class): void
    {
        $this->_reflection_class = $reflection_class;
    }

    public function getReflectionMethod(): ?\ReflectionMethod
    {
        return $this->_reflection_method;
    }

    public function setReflectionMethod(\ReflectionMethod $reflection_method): void
    {
        $this->_reflection_method = $reflection_method;
    }
}
