<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ClassCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_class;

    /**
     * @var string
     */
    protected $_method;

    public function getClass(): ?string
    {
        return $this->_class;
    }

    public function setClass(string $class): void
    {
        $this->_class = $class;
    }

    public function getMethod(): ?string
    {
        return $this->_method;
    }

    public function setMethod(string $method): void
    {
        $this->_method = $method;
    }
}
