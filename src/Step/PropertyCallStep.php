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
}
