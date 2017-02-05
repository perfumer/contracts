<?php

namespace Perfumer\Component\Bdd\Sandbox;

class FormatterStep extends \Perfumer\Component\Bdd\Step\FormatterStep
{
    /**
     * @var string
     */
    protected $name = 'sample';

    /**
     * @var array
     */
    protected $arguments = ['param2'];

    /**
     * @var string
     */
    protected $return = 'param4';
}
