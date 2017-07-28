<?php

namespace Barman\Variable;

interface ReturnedVariable
{
    /**
     * @return string
     */
    public function getReturnedVariableExpression(): string;
}
