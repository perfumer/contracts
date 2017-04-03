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
class Context implements Variable
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
    public function asArgument(): string
    {
        return '$this->get' . ucfirst($this->name) . 'Context()';
    }

    /**
     * @return string
     */
    public function asHeader(): string
    {
        return '$' . $this->name;
    }

    /**
     * @throws ContractsException
     */
    public function asReturn(): string
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
class Output implements Variable
{
    /**
     * @throws ContractsException
     */
    public function asArgument(): string
    {
        throw new ContractsException('@Output annotation can not be used for "args".');
    }

    /**
     * @throws ContractsException
     */
    public function asHeader(): string
    {
        throw new ContractsException('@Output annotation can not be used for "args".');
    }

    /**
     * @return string
     */
    public function asReturn(): string
    {
        return '$_return';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Property implements Variable
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
    public function asArgument(): string
    {
        return '$this->' . $this->name;
    }

    /**
     * @return string
     */
    public function asHeader(): string
    {
        return '$' . $this->name;
    }

    /**
     * @return string
     */
    public function asReturn(): string
    {
        return '$this->' . $this->name;
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceParent extends Step implements Service
{
    /**
     * @return string
     */
    public function getExpression(): string
    {
        return 'parent::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceProperty extends Step implements Service
{
    /**
     * @return string
     */
    public function getExpression(): string
    {
        return '$this->' . $this->name . '->';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceSelf extends Step implements Service
{
    /**
     * @return string
     */
    public function getExpression(): string
    {
        return 'self::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceStatic extends Step implements Service
{
    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->name . '::';
    }
}

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceThis extends Step implements Service
{
    /**
     * @return string
     */
    public function getExpression(): string
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
