<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ThisCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $method;

    public function onCreate(): void
    {
        $this->expression = '$this->' . $this->method;

        parent::onCreate();
    }
}
