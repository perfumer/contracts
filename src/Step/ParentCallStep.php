<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ParentCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $method;

    public function onCreate(): void
    {
        $this->expression = 'parent::' . $this->method;

        parent::onCreate();
    }
}
