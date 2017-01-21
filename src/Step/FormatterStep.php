<?php

namespace Perfumer\Component\Bdd\Step;

class FormatterStep extends AbstractStep
{
    /**
     * @var string
     */
    protected $type = 'formatter';

    /**
     * @return string
     */
    public function getFunctionName()
    {
        return $this->name . 'Formatter';
    }
}