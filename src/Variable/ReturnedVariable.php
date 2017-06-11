<?php

namespace Perfumer\Contracts\Variable;

interface ReturnedVariable
{
    /**
     * @return string
     */
    public function getReturnedVariableExpression(): string;
}
