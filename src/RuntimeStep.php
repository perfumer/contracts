<?php

namespace Perfumer\Component\Bdd;

use Perfumer\Component\Bdd\Step\AbstractStep;
use Perfumer\Component\Bdd\Step\CallStep;
use Perfumer\Component\Bdd\Step\FormatterStep;
use Perfumer\Component\Bdd\Step\ValidatorStep;

class RuntimeStep
{
    /**
     * @var AbstractStep
     */
    protected $step;

    /**
     * @var string
     */
    protected $method_name;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var string
     */
    protected $return_expression;

    /**
     * RuntimeStep constructor.
     * @param AbstractStep $step
     */
    public function __construct(AbstractStep $step)
    {
        $this->step = $step;

        if ($step instanceof CallStep) {
            $this->method_name = $step->getName() . 'Call';
        }

        if ($step instanceof FormatterStep) {
            $this->method_name = $step->getName() . 'Formatter';
        }

        if ($step instanceof ValidatorStep) {
            $this->method_name = $step->getName() . 'Validator';
        }
    }

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
     * @return string
     */
    public function getMethodName()
    {
        return $this->method_name;
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
