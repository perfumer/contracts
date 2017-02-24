<?php

namespace Perfumer\Component\Bdd;

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
    protected $call_arguments = [];

    /**
     * @var array
     */
    protected $method_arguments = [];

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
    public function getCallArguments()
    {
        return $this->call_arguments;
    }

    /**
     * @param array $call_arguments
     */
    public function setCallArguments($call_arguments)
    {
        $this->call_arguments = $call_arguments;
    }

    /**
     * @param string $argument
     */
    public function addCallArgument($argument)
    {
        $this->call_arguments[] = $argument;
    }

    /**
     * @return array
     */
    public function getMethodArguments()
    {
        return $this->method_arguments;
    }

    /**
     * @param array $method_arguments
     */
    public function setMethodArguments($method_arguments)
    {
        $this->method_arguments = $method_arguments;
    }

    /**
     * @param string $argument
     */
    public function addMethodArgument($argument)
    {
        $this->method_arguments[] = $argument;
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
}
