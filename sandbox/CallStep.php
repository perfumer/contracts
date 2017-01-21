<?php

namespace Perfumer\Component\Bdd\Sandbox;

class CallStep extends \Perfumer\Component\Bdd\Step\CallStep
{
    /**
     * @var string
     */
    protected $name = 'sample';

    /**
     * @var array
     */
    protected $arguments = ['param1'];

    /**
     * @var string
     */
    protected $return = 'return';
}