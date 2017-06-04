<?php

namespace Perfumer\Contracts;

abstract class Service extends Step
{
    /**
     * @return string
     */
    abstract public function getCallExpression(): string;

    /**
     * @return null|StepBuilder|StepBuilder[]
     */
    public function getBuilder()
    {
        $step_builder = parent::getBuilder();

        $step_builder->setCallExpression($this->getCallExpression());

        return $step_builder;
    }
}
