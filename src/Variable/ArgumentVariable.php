<?php

namespace Perfumer\Contracts\Variable;

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
