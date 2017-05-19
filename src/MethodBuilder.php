<?php

namespace Perfumer\Component\Contracts;

final class MethodBuilder
{
    /**
     * @var bool
     */
    private $is_final = false;

    /**
     * @var bool
     */
    private $is_abstract = false;

    /**
     * @var bool
     */
    private $is_static = false;

    /**
     * @var null|string
     */
    private $access;

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
    private $test_variables;

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
     * @var bool
     */
    private $validation = false;

    /**
     * MethodBuilder constructor.
     */
    public function __construct()
    {
        $this->arguments = new \ArrayObject();
        $this->initial_variables = new \ArrayObject();
        $this->test_variables = new \ArrayObject();
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
     * @return bool
     */
    public function isIsStatic(): bool
    {
        return $this->is_static;
    }

    /**
     * @param bool $is_static
     */
    public function setIsStatic(bool $is_static)
    {
        $this->is_static = $is_static;
    }

    /**
     * @return null|string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param null|string $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
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
    public function getTestVariables(): \ArrayObject
    {
        return $this->test_variables;
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

    /**
     * @return bool
     */
    public function isValidation(): bool
    {
        return $this->validation;
    }

    /**
     * @param bool $validation
     */
    public function setValidation(bool $validation)
    {
        $this->validation = $validation;
    }
}
