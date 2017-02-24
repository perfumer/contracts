<?php

namespace Perfumer\Component\Bdd\Annotations;

/**
 * @Annotation
 */
class Extend
{
    public $class;
}

/**
 * @Annotation
 */
class Call
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
    public $arguments = [];

    /**
     * @var string
     */
    public $return;
}

/**
 * @Annotation
 */
class Context
{
    public $name;

    public $class;
}

/**
 * @Annotation
 */
class Service
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
    public $arguments = [];

    /**
     * @var string
     */
    public $return;
}

/**
 * @Annotation
 */
class Validate
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
    public $arguments = [];
}

/**
 * @Annotation
 */
class Test
{
}
