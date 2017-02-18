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
}
