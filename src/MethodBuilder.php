<?php

namespace Perfumer\Component\Contracts;

final class MethodBuilder
{
    /**
     * @var bool
     */
    private $is_final = true;

    /**
     * @var bool
     */
    private $is_abstract = false;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var \ArrayObject
     */
    private $arguments;

    /**
     * @var null|string
     */
    private $return_type;

    /**
     * @var \ArrayObject
     */
    private $initial_variables;

    /**
     * @var \ArrayObject
     */
    private $prepended_code;

    /**
     * @var \ArrayObject
     */
    private $appended_code;

    /**
     * @var \ArrayObject
     */
    private $steps;

    /**
     * MethodBuilder constructor.
     */
    public function __construct()
    {
        $this->arguments = new \ArrayObject();
        $this->initial_variables = new \ArrayObject();
        $this->prepended_code = new \ArrayObject();
        $this->appended_code = new \ArrayObject();
        $this->steps = new \ArrayObject();
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->is_final;
    }

    /**
     * @param bool $is_final
     */
    public function setIsFinal(bool $is_final)
    {
        $this->is_final = $is_final;
    }

    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->is_abstract;
    }

    /**
     * @param bool $is_abstract
     */
    public function setIsAbstract(bool $is_abstract)
    {
        $this->is_abstract = $is_abstract;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getReturnType()
    {
        return $this->return_type;
    }

    /**
     * @param null|string $return_type
     */
    public function setReturnType($return_type)
    {
        $this->return_type = $return_type;
    }

    /**
     * @return \ArrayObject
     */
    public function getArguments(): \ArrayObject
    {
        return $this->arguments;
    }

    /**
     * @return \ArrayObject
     */
    public function getInitialVariables(): \ArrayObject
    {
        return $this->initial_variables;
    }

    /**
     * @return \ArrayObject
     */
    public function getPrependedCode(): \ArrayObject
    {
        return $this->prepended_code;
    }

    /**
     * @return \ArrayObject
     */
    public function getAppendedCode(): \ArrayObject
    {
        return $this->appended_code;
    }

    /**
     * @return \ArrayObject
     */
    public function getSteps(): \ArrayObject
    {
        return $this->steps;
    }
}
