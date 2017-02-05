<?php

namespace Perfumer\Component\Bdd;

use Perfumer\Component\Bdd\Step\AbstractStep;

class RuntimeStep
{
    /**
     * @var AbstractStep
     */
    protected $step;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var string
     */
    protected $return_expression;

    /**
     * @return AbstractStep
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param AbstractStep $step
     */
    public function setStep(AbstractStep $step)
    {
        $this->step = $step;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param string $argument
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
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
