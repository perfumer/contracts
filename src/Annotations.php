<?php

namespace Perfumer\Component\Bdd\Annotations;

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
class Extend
{
    public $name;
}

/**
 * @Annotation
 */
class Format
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
