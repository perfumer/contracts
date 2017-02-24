<?php

namespace Perfumer\Component\Bdd\Annotations;

/**
 * @Annotation
 */
class Call extends \Perfumer\Component\Bdd\Step
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
class Collection implements \Perfumer\Component\Bdd\Annotation
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
class Service extends \Perfumer\Component\Bdd\Step
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
class Test implements \Perfumer\Component\Bdd\Annotation
{
}

/**
 * @Annotation
 */
class Validate extends \Perfumer\Component\Bdd\Step
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
