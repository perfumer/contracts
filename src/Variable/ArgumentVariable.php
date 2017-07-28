<?php

namespace Barman\Variable;

interface ArgumentVariable
{
    /**
     * @return string
     */
    public function getArgumentVariableName(): string;

    /**
     * @return string
     */
    public function getArgumentVariableExpression(): string;
}
