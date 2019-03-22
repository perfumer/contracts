<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class SelfCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $method;

    public function onCreate(): void
    {
        $this->expression = 'self::' . $this->method;

        parent::onCreate();
    }
}
