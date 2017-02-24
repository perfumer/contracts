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

    /**
     * @return string
     */
    public function prependCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function appendCode()
    {
        return '';
    }
}

/**
 * @Annotation
 */
class Collection
{
    /**
     * @var array
     */
    public $steps = [];

    /**
     * @return string
     */
    public function beforeCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function afterCode()
    {
        return '';
    }
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
class Extend
{
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

    /**
     * @return string
     */
    public function prependCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function appendCode()
    {
        return '';
    }
}

/**
 * @Annotation
 */
class Test
{
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

    /**
     * @return string
     */
    public function prependCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function appendCode()
    {
        return '';
    }
}
