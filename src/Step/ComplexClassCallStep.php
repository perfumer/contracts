<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ComplexClassCallStep extends ClassCallStep
{
    /**
     * @var string
     */
    protected $_instance;

    public function onCreate(): void
    {
        $this->_expression = $this->_instance . $this->_method;

        parent::onCreate();
    }
}
