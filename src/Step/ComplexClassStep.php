<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ComplexClassStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $instance;

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
        $this->expression = $this->instance . $this->method;

        parent::onCreate();
    }
}
