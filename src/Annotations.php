<?php

namespace Perfumer\Component\Contracts\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Component\Contracts\Annotation;
use Perfumer\Component\Contracts\ContractsException;
use Perfumer\Component\Contracts\Service;
use Perfumer\Component\Contracts\Step;
use Perfumer\Component\Contracts\Variable;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Call extends Step
{
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Collection implements Annotation
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
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Context extends Variable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $class;

    /**
     * @return string
     */
    public function asArgument()
    {
        return '$this->get' . ucfirst($this->name) . 'Context()';
    }

    /**
     * @return string
     */
    public function asHeader()
    {
        return '$' . $this->name;
    }

    /**
     * @throws ContractsException
     */
    public function asReturn()
    {
        throw new ContractsException('@Context annotation can not be used for "return".');
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Custom extends Step
{
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Error extends Step
{
}

/**
 * @Annotation
 * @Target("CLASS")
 */
class Extend implements Annotation
{
    public $class;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
class Inject implements Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $variable;
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Output extends Variable
{
    /**
     * @throws ContractsException
     */
    public function asArgument()
    {
        throw new ContractsException('@Output annotation can not be used for "args".');
    }

    /**
     * @throws ContractsException
     */
    public function asHeader()
    {
        throw new ContractsException('@Output annotation can not be used for "args".');
    }

    /**
     * @return string
     */
    public function asReturn()
    {
        return '$_return';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Property extends Variable
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

    /**
     * @return string
     */
    public function asArgument()
    {
        return '$this->' . $this->name;
    }

    /**
     * @return string
     */
    public function asHeader()
    {
        return '$' . $this->name;
    }

    /**
     * @return string
     */
    public function asReturn()
    {
        return '$this->' . $this->name;
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceParent extends Service
{
    /**
     * @return string
     */
    public function getExpression()
    {
        return 'parent::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceProperty extends Service
{
    /**
     * @return string
     */
    public function getExpression()
    {
        return '$this->' . $this->name . '->';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceSelf extends Service
{
    /**
     * @return string
     */
    public function getExpression()
    {
        return 'self::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceStatic extends Service
{
    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->name . '::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceThis extends Service
{
    /**
     * @return string
     */
    public function getExpression()
    {
        return '$this->';
    }
}

/**
 * @Annotation
 * @Target("CLASS")
 */
class Template implements Annotation
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
class Test implements Annotation
{
}
