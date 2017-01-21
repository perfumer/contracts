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
    protected $arguments = ['param3'];

    /**
     * @var string
     */
    protected $return = '>';
}