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
     * @var MethodGenerator
     */
    private $generator;

    /**
     * @var bool
     */
    private $_is_validating = false;

    /**
     * @var bool
     */
    private $_is_returning = false;

    public function __construct()
    {
        $this->generator = new MethodGenerator();
    }

    public function getInitialVariables(): array
    {
        return $this->initial_variables;
    }

    public function setInitialVariables(array $initial_variables): void
    {
        $this->initial_variables = $initial_variables;
    }

    public function addInitialVariable(string $name, string $value): void
    {
        $this->initial_variables[$name] = $value;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }

    public function addStep(StepData $step): void
    {
        $this->steps[] = $step;
    }

    public function getSets(): array
    {
        return $this->sets;
    }

    public function setSets(array $sets): void
    {
        $this->sets = $sets;
    }

    public function addSet(Set $set): void
    {
        $this->sets[] = $set;
    }

    public function isValidating(): bool
    {
        return $this->_is_validating;
    }

    public function setIsValidating(bool $_is_validating): void
    {
        $this->_is_validating = $_is_validating;
    }

    public function getGenerator(): MethodGenerator
    {
        return $this->generator;
    }

    public function setGenerator(MethodGenerator $generator): void
    {
        $this->generator = $generator;
    }

    public function isReturning(): bool
    {
        return $this->_is_returning;
    }

    public function setIsReturning(bool $is_returning): void
    {
        $this->_is_returning = $is_returning;
    }

    public function generate(): string
    {
        $this->generateBody();

        return $this->generator->generate();
    }

    private function generateBody(): void
    {
        $body = '';

        if ($this->isReturning()) {
            $body .= '$_return = null;';
        }

        if ($this->isValidating()) {
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

        if ($this->isReturning()) {
            $body .= 'return $_return;';
        }

        $this->generator->setBody($body);
    }

    private function generateStep(StepData $step)
    {
        $body = '';

        if ($step->getBeforeCode()) {
            $body .= $step->getBeforeCode() . PHP_EOL . PHP_EOL;
        }

        if ($step->isValidating()) {
            if ($this->isValidating() && $step->getExtraCondition()) {
                $body .= 'if ($_valid === ' . ($step->getValidationCondition() ? 'true' : 'false') . ' && ' . $step->getExtraCondition() . ') {' . PHP_EOL;
            } elseif ($this->isValidating() && !$step->getExtraCondition()) {
                $body .= 'if ($_valid === ' . ($step->getValidationCondition() ? 'true' : 'false') . ') {' . PHP_EOL;
            } elseif (!$this->isValidating() && $step->getExtraCondition()) {
                $body .= 'if (' . $step->getExtraCondition() . ') {' . PHP_EOL;
            }
        }

        $body .= $step->getCode() . PHP_EOL . PHP_EOL;

        if ($step->isValidating() && ($this->isValidating() || $step->getExtraCondition())) {
            $body .= '}' . PHP_EOL . PHP_EOL;
        }

        if ($step->getAfterCode()) {
            $body .= $step->getAfterCode() . PHP_EOL . PHP_EOL;
        }

        return $body;
    }
}
