<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class PropertyCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $property;

    /**
     * @var string
     */
    public $method;

    public function onCreate(): void
    {
        $this->expression = '$this->' . $this->property . '->' . $this->method;

        parent::onCreate();
    }
}
