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
}
