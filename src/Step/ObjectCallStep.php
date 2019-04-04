<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ObjectCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_method;

    public function onCreate(): void
    {
        $this->_expression = '$' . $this->_object . '->' . $this->_method;

        parent::onCreate();
    }

    public function getObject(): ?string
    {
        return $this->_object;
    }

    public function setObject(string $object): void
    {
        $this->_object = $object;
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
