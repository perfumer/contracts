<?php

namespace Perfumer\Component\Contracts;

class RuntimeAction
{
    /**
     * @var string
     */
    protected $method_name;

    /**
     * @var array
     */
    protected $header_arguments = [];

    /**
     * @var array
     */
    protected $local_variables = [];

    /**
     * @var array
     */
    protected $steps = [];

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->method_name;
    }

    /**
     * @param string $method_name
     */
    public function setMethodName($method_name)
    {
        $this->method_name = $method_name;
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
     * @param string $header_argument
     */
    public function addHeaderArgument($header_argument)
    {
        $this->header_arguments[] = $header_argument;
    }

    /**
     * @return array
     */
    public function getLocalVariables()
    {
        return $this->local_variables;
    }

    /**
     * @param array $local_variables
     */
    public function setLocalVariables($local_variables)
    {
        $this->local_variables = $local_variables;
    }

    /**
     * @param string $local_variable
     * @param string $value
     */
    public function addLocalVariable($local_variable, $value)
    {
        $this->local_variables[$local_variable] = $value;
    }

    /**
     * @param string $local_variable
     * @return bool
     */
    public function hasLocalVariable($local_variable)
    {
        return array_key_exists($local_variable, $this->local_variables);
    }

    /**
     * @return array
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param RuntimeStep $step
     */
    public function addStep(RuntimeStep $step)
    {
        $this->steps[] = $step;
    }
}
