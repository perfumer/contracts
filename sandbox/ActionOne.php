<?php

namespace Perfumer\Component\Bdd\Sandbox;

class ActionOne extends \Perfumer\Component\Bdd\Action
{
    /**
     * @var string
     */
    protected $name = 'sandboxActionOne';

    /**
     * @var array
     */
    protected $arguments = ['param1', 'param2', 'param3'];

    /**
     * Action constructor.
     */
    public function __construct()
    {
        $this->steps = [
            new ValidatorStep(),
            new FormatterStep(),
            new CallStep()
        ];
    }
}