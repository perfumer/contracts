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
}
