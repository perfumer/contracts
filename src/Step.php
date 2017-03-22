<?php

namespace Perfumer\Component\Contracts;

abstract class Step implements Annotation
{
    /**
     * @var string
     */
    public $name;

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
     * @var string
     */
    public $unless;

    /**
     * @return string
     */
    public function prepend()
    {
        return '';
    }

    /**
     * @return string
     */
    public function append()
    {
        return '';
    }
}