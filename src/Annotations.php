<?php

namespace Perfumer\Component\Bdd\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
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
 * @Target("METHOD")
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
    public function before()
    {
        return '';
    }

    /**
     * @return string
     */
    public function after()
    {
        return '';
    }
}

/**
 * @Annotation
 * @Target("CLASS")
 */
class Context implements \Perfumer\Component\Bdd\Annotation
{
    public $name;

    public $class;
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Errors extends \Perfumer\Component\Bdd\Step
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
 * @Target("CLASS")
 */
class Extend implements \Perfumer\Component\Bdd\Annotation
{
    public $class;
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
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
 * @Target("CLASS")
 */
class Template implements \Perfumer\Component\Bdd\Annotation
{
    /**
     * @var string
     */
    public $name;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Test implements \Perfumer\Component\Bdd\Annotation
{
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
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

    /**
     * @var string
     */
    public $return;
}
