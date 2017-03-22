<?php

namespace Perfumer\Component\Contracts;

abstract class Step implements Annotation
{
    /**
     * @var string
     */
    public $na;

    /**
     * @var string
     */
    public $me;

    /**
     * @var array
     */
    public $ar = [];

    /**
     * @var mixed
     */
    public $re;

    /**
     * @var string
     */
    public $if;

    /**
     * @var string
     */
    public $un;

    /**
     * @var bool
     */
    public $va = false;

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