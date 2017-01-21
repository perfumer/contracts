<?php

namespace Perfumer\Component\Bdd;

use Perfumer\Component\Bdd\Step\AbstractStep;

abstract class Action
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var array
     */
    protected $steps = [];

    /**
     * @return string
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
     * @return array
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param array $steps
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;
    }

    /**
     * @param AbstractStep $step
     */
    public function addStep(AbstractStep $step)
    {
        $this->steps[] = $step;
    }
}