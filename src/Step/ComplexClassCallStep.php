<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ComplexClassCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_instance;

    /**
     * @var string
     */
    protected $_class;

    /**
     * @var string
     */
    protected $_method;

    public function onCreate(): void
    {
        $this->_expression = $this->_instance . $this->_method;

        parent::onCreate();
    }

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
