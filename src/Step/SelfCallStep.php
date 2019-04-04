<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class SelfCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_method;

    public function onCreate(): void
    {
        $this->_expression = 'self::' . $this->_method;

        parent::onCreate();
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
