<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ThisCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_method;

    public function onCreate(): void
    {
        $this->_expression = '$this->' . $this->_method;

        parent::onCreate();
    }
}
