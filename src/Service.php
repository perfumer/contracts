<?php

namespace Perfumer\Component\Contracts;

abstract class Service extends Step
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $args = [];

    /**
     * @var mixed
     */
    public $return;

    /**
     * @var string
     */
    public $if;

    /**
     * @return string
     */
    abstract public function getExpression();
}