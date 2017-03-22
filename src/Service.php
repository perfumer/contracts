<?php

namespace Perfumer\Component\Contracts;

abstract class Service extends Step
{
    /**
     * @return string
     */
    abstract public function getExpression();
}