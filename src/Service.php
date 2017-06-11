<?php

namespace Perfumer\Contracts;

use Perfumer\Contracts\Generator\StepGenerator;

abstract class Service extends Step
{
    /**
     * @return string
     */
    abstract public function getCallExpression(): string;

    /**
     * @return null|StepGenerator|StepGenerator[]
     */
    public function getGenerator()
    {
        $step_generator = parent::getGenerator();

        $step_generator->setCallExpression($this->getCallExpression());

        return $step_generator;
    }
}
