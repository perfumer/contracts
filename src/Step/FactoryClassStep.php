<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class FactoryClassStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $method;

    public function onCreate(): void
    {
        if ($this->class[0] !== '\\') {
            $this->class = '\\' . $this->class;
        }

        $this->expression = '(new ' . $this->class . '())->' . $this->method;

        parent::onCreate();
    }
}
