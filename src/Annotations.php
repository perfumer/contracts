<?php

namespace Perfumer\Component\Contracts\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Component\Contracts\ContractsException;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Call extends \Perfumer\Component\Contracts\Step
{
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
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Context extends \Perfumer\Component\Contracts\Variable
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
class Custom extends \Perfumer\Component\Contracts\Step
{
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Error extends \Perfumer\Component\Contracts\Step
{
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
class Output extends \Perfumer\Component\Contracts\Variable
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
class Property extends \Perfumer\Component\Contracts\Variable
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
class ServiceParent extends \Perfumer\Component\Contracts\Service
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
class ServiceProperty extends \Perfumer\Component\Contracts\Service
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
class ServiceSelf extends \Perfumer\Component\Contracts\Service
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
class ServiceStatic extends \Perfumer\Component\Contracts\Service
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
class ServiceThis extends \Perfumer\Component\Contracts\Service
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
