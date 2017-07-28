<?php

namespace Barman;

abstract class Service extends Step
{
    /**
     * @return string
     */
    abstract public function getCallExpression(): string;

    public function onDecorate(): void
    {
        parent::onDecorate();

        $this->getStepGenerator()->setCallExpression($this->getCallExpression());
    }
}
