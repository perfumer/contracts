<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class PropertyCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_property;

    /**
     * @var string
     */
    protected $_method;

    public function onCreate(): void
    {
        $this->_expression = '$this->' . $this->_property . '->' . $this->_method;

        parent::onCreate();
    }

    public function getProperty(): ?string
    {
        return $this->_property;
    }

    public function setProperty(string $property): void
    {
        $this->_property = $property;
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
