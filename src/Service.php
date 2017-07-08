<?php

namespace Perfumer\Contracts;

abstract class Service extends Step
{
    /**
     * @return string
     */
    abstract public function getCallExpression(): string;

    public function decorateGenerators(): void
    {
        parent::decorateGenerators();

        $this->getStepGenerator()->setCallExpression($this->getCallExpression());
    }
}
