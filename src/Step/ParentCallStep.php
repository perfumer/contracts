<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ParentCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_method;

    public function onCreate(): void
    {
        $this->_expression = 'parent::' . $this->_method;

        parent::onCreate();
    }
}
