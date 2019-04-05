<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class FactoryClassCallStep extends ClassCallStep
{
    public function onCreate(): void
    {
        if ($this->_class[0] !== '\\') {
            $this->_class = '\\' . $this->_class;
        }

        $this->_expression = '(new ' . $this->_class . '())->' . $this->_method;

        parent::onCreate();
    }
}
