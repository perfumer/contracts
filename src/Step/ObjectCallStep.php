<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ObjectCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    public $object;

    /**
     * @var string
     */
    public $method;

    public function onCreate(): void
    {
        $this->expression = '$' . $this->object . '->' . $this->method;

        parent::onCreate();
    }
}
