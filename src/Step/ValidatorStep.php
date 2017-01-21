<?php

namespace Perfumer\Component\Bdd\Step;

class ValidatorStep extends AbstractStep
{
    /**
     * @var string
     */
    protected $type = 'validator';

    /**
     * @return string
     */
    public function getFunctionName()
    {
        return $this->name . 'Validator';
    }
}