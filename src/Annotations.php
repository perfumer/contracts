<?php

namespace Perfumer\Component\Bdd\Annotations;

/**
 * @Annotation
 */
class Call implements \Perfumer\Component\Bdd\Step
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
class Collection implements \Perfumer\Component\Bdd\Step
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
class Context implements \Perfumer\Component\Bdd\Annotation
{
    public $name;

    public $class;
}

/**
 * @Annotation
 */
class Extend implements \Perfumer\Component\Bdd\Annotation
{
    public $class;
}

/**
 * @Annotation
 */
class Service implements \Perfumer\Component\Bdd\Step
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
class Test implements \Perfumer\Component\Bdd\Annotation
{
}

/**
 * @Annotation
 */
class Validate implements \Perfumer\Component\Bdd\Step
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
