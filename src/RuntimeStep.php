<?php

namespace Perfumer\Component\Contracts;

class RuntimeStep
{
    /**
     * @var string
     */
    protected $function_name;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $context_name;

    /**
     * @var string
     */
    protected $service;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $header_arguments = [];

    /**
     * @var array
     */
    protected $body_arguments = [];

    /**
     * @var string
     */
    protected $return_expression;

    /**
     * @var string
     */
    protected $before_code;

    /**
     * @var string
     */
    protected $after_code;

    /**
     * @var string
     */
    protected $append_code;

    /**
     * @var string
     */
    protected $prepend_code;

    /**
     * @var bool
     */
    protected $valid;

    /**
     * @return string
     */
    public function getFunctionName()
    {
        return $this->function_name;
    }

    /**
     * @param string $function_name
     */
    public function setFunctionName($function_name)
    {
        $this->function_name = $function_name;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getContextName()
    {
        return $this->context_name;
    }

    /**
     * @param string $context_name
     */
    public function setContextName(string $context_name)
    {
        $this->context_name = $context_name;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getHeaderArguments(): array
    {
        return $this->header_arguments;
    }

    /**
     * @param array $header_arguments
     */
    public function setHeaderArguments(array $header_arguments)
    {
        $this->header_arguments = $header_arguments;
    }

    /**
     * @param string $argument
     */
    public function addHeaderArgument($argument)
    {
        $this->header_arguments[] = $argument;
    }

    /**
     * @return array
     */
    public function getBodyArguments(): array
    {
        return $this->body_arguments;
    }

    /**
     * @param array $body_arguments
     */
    public function setBodyArguments(array $body_arguments)
    {
        $this->body_arguments = $body_arguments;
    }

    /**
     * @param string $argument
     */
    public function addBodyArgument($argument)
    {
        $this->body_arguments[] = $argument;
    }

    /**
     * @return string
     */
    public function getReturnExpression()
    {
        return $this->return_expression;
    }

    /**
     * @param string $return_expression
     */
    public function setReturnExpression($return_expression)
    {
        $this->return_expression = $return_expression;
    }

    /**
     * @return string
     */
    public function getBeforeCode()
    {
        return $this->before_code;
    }

    /**
     * @param string $before_code
     */
    public function setBeforeCode(string $before_code)
    {
        $this->before_code = $before_code;
    }

    /**
     * @return string
     */
    public function getAfterCode()
    {
        return $this->after_code;
    }

    /**
     * @param string $after_code
     */
    public function setAfterCode(string $after_code)
    {
        $this->after_code = $after_code;
    }

    /**
     * @return string
     */
    public function getAppendCode()
    {
        return $this->append_code;
    }

    /**
     * @param string $append_code
     */
    public function setAppendCode(string $append_code)
    {
        $this->append_code = $append_code;
    }

    /**
     * @return string
     */
    public function getPrependCode()
    {
        return $this->prepend_code;
    }

    /**
     * @param string $prepend_code
     */
    public function setPrependCode(string $prepend_code)
    {
        $this->prepend_code = $prepend_code;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     */
    public function setValid(bool $valid)
    {
        $this->valid = $valid;
    }
}
