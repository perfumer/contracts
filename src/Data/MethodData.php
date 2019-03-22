<?php

namespace Perfumerlabs\Perfumer\Data;

use Perfumerlabs\Perfumer\Annotation\Set;
use Zend\Code\Generator\MethodGenerator;

final class MethodData
{
    /**
     * @var array
     */
    private $initial_variables = [];

    /**
     * @var array
     */
    private $steps = [];

    /**
     * @var array
     */
    private $sets = [];

    /**
     * @var bool
     */
    private $validation = false;

    /**
     * @var MethodGenerator
     */
    private $generator;

    /**
     * MethodKeeper constructor.
     */
    public function __construct()
    {
        $this->generator = new MethodGenerator();
    }

    /**
     * @return array
     */
    public function getInitialVariables(): array
    {
        return $this->initial_variables;
    }

    /**
     * @param array $initial_variables
     */
    public function setInitialVariables(array $initial_variables): void
    {
        $this->initial_variables = $initial_variables;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addInitialVariable(string $name, string $value): void
    {
        $this->initial_variables[$name] = $value;
    }

    /**
     * @return array
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @param array $steps
     */
    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }

    /**
     * @param StepData $step
     */
    public function addStep(StepData $step): void
    {
        $this->steps[] = $step;
    }

    /**
     * @return array
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    /**
     * @param array $sets
     */
    public function setSets(array $sets): void
    {
        $this->sets = $sets;
    }

    /**
     * @param Set $set
     */
    public function addSet(Set $set): void
    {
        $this->sets[] = $set;
    }

    /**
     * @return bool
     */
    public function hasValidation(): bool
    {
        return $this->validation;
    }

    /**
     * @param bool $validation
     */
    public function setValidation(bool $validation): void
    {
        $this->validation = $validation;
    }

    /**
     * @return MethodGenerator
     */
    public function getGenerator(): MethodGenerator
    {
        return $this->generator;
    }

    /**
     * @param MethodGenerator $generator
     */
    public function setGenerator(MethodGenerator $generator): void
    {
        $this->generator = $generator;
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $this->generateBody();

        return $this->generator->generate();
    }

    private function generateBody(): void
    {
        $body = '';

        if ($this->hasValidation()) {
            $body .= '$_valid = true;' . PHP_EOL;
        }

        foreach ($this->initial_variables as $name => $value) {
            $body .= '$' . $name . ' = ' . $value . ';' . PHP_EOL;
        }

        $body .= PHP_EOL;

        /** @var Set $set */
        foreach ($this->sets as $set) {
            $body .= $this->generateStep($set->getStepData());
        }

        /** @var StepData $step */
        foreach ($this->steps as $step) {
            $body .= $this->generateStep($step);
        }

        $this->generator->setBody($body);
    }

    private function generateStep(StepData $step)
    {
        $body = '';

        if ($step->getBeforeCode()) {
            $body .= $step->getBeforeCode() . PHP_EOL . PHP_EOL;
        }

        if ($step->isValidationEnabled()) {
            if ($this->hasValidation() && $step->getExtraCondition()) {
                $body .= 'if ($_valid === ' . ($step->getValidationCondition() ? 'true' : 'false') . ' && ' . $step->getExtraCondition() . ') {' . PHP_EOL;
            } elseif ($this->hasValidation() && !$step->getExtraCondition()) {
                $body .= 'if ($_valid === ' . ($step->getValidationCondition() ? 'true' : 'false') . ') {' . PHP_EOL;
            } elseif (!$this->hasValidation() && $step->getExtraCondition()) {
                $body .= 'if (' . $step->getExtraCondition() . ') {' . PHP_EOL;
            }
        }

        $body .= $step->getCode() . PHP_EOL . PHP_EOL;

        if ($step->isValidationEnabled() && ($this->hasValidation() || $step->getExtraCondition())) {
            $body .= '}' . PHP_EOL . PHP_EOL;
        }

        if ($step->getAfterCode()) {
            $body .= $step->getAfterCode() . PHP_EOL . PHP_EOL;
        }

        return $body;
    }
}
