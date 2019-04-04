<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class GetterCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_getter;

    /**
     * @var string
     */
    protected $_method;

    public function onCreate(): void
    {
        $this->_expression = '$this->' . $this->_getter . '()->' . $this->_method;

        parent::onCreate();
    }

    public function getGetter(): ?string
    {
        return $this->_getter;
    }

    public function setGetter(string $getter): void
    {
        $this->_getter = $getter;
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
