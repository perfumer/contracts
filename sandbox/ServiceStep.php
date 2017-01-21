<?php

namespace Perfumer\Component\Bdd\Sandbox;

class ServiceStep extends \Perfumer\Component\Bdd\Step\CallStep
{
    /**
     * @var string
     */
    protected $name = 'service';

    /**
     * @var string
     */
    protected $service = 'foobar';

    /**
     * @var string
     */
    protected $method = 'buzz';

    /**
     * @var array
     */
    protected $arguments = ['param1', 'param2'];
}