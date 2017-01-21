<?php

namespace Perfumer\Component\Bdd\Sandbox;

class ValidatorStep extends \Perfumer\Component\Bdd\Step\ValidatorStep
{
    /**
     * @var string
     */
    protected $name = 'sample';

    /**
     * @var array
     */
    protected $arguments = ['param1'];
}