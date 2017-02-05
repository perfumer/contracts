<?php

namespace Perfumer\Component\Bdd\Sandbox;

class ParentStep extends \Perfumer\Component\Bdd\Step\CallStep
{
    /**
     * @var string
     */
    protected $name = 'parent';

    /**
     * @var string
     */
    protected $service = '_parent';

    /**
     * @var array
     */
    protected $arguments = ['param1', 'param2'];
}
