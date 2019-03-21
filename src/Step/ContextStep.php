<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ContextStep extends ExpressionStep
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
        $name = str_replace('\\', '', $this->class);

        $this->getClassKeeper()->addContext($name, $this->class);

        $this->expression = '$this->get' . $name . 'Context()->' . $this->method;

        parent::onCreate();
    }
}
