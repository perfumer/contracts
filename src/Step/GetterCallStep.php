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
}
