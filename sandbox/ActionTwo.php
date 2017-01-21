<?php

namespace Perfumer\Component\Bdd\Sandbox;

class ActionTwo extends \Perfumer\Component\Bdd\Action
{
    /**
     * @var string
     */
    protected $name = 'sandboxActionTwo';

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
            new CallStep(),
            new ServiceStep(),
            new ParentStep()
        ];
    }
}