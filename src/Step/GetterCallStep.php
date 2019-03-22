<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class GetterCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $getter;

    /**
     * @var string
     */
    public $method;

    public function onCreate(): void
    {
        $this->expression = '$this->' . $this->getter . '()->' . $this->method;

        parent::onCreate();
    }
}
