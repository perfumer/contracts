<?php

namespace Perfumer\Component\Contracts\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Ancestor extends \Perfumer\Component\Contracts\Step
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $arguments = [];

    /**
     * @var mixed
     */
    public $return;

    /**
     * @var string
     */
    public $if;
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Call extends \Perfumer\Component\Contracts\Step
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
     * @var mixed
     */
    public $return;

    /**
     * @var string
     */
    public $if;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Collection implements \Perfumer\Component\Contracts\Annotation
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
class Context implements \Perfumer\Component\Contracts\Annotation
{
    public $name;

    public $class;
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Custom extends \Perfumer\Component\Contracts\Step
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $arguments = [];

    /**
     * @var mixed
     */
    public $return;

    /**
     * @var string
     */
    public $if;
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Errors extends \Perfumer\Component\Contracts\Step
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
class Extend implements \Perfumer\Component\Contracts\Annotation
{
    public $class;
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Output implements \Perfumer\Component\Contracts\Annotation
{
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Property implements \Perfumer\Component\Contracts\Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @param array $values
     */
    public function __construct($values)
    {
        $this->name = $values['value'];
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Service extends \Perfumer\Component\Contracts\Step
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
     * @var mixed
     */
    public $return;

    /**
     * @var string
     */
    public $if;
}

/**
 * @Annotation
 * @Target("CLASS")
 */
class Template implements \Perfumer\Component\Contracts\Annotation
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
class Test implements \Perfumer\Component\Contracts\Annotation
{
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Validate extends \Perfumer\Component\Contracts\Step
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
     * @var string
     */
    public $if;
}
