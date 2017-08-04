<?php

namespace Barman\Keeper;

use Zend\Code\Generator\MethodGenerator;

final class MethodKeeper
{
    /**
     * @var array
     */
    private $initial_variables = [];

    /**
     * @var array
     */
    private $prepended_code = [];

    /**
     * @var array
     */
    private $appended_code = [];

    /**
     * @var array
     */
    private $steps = [];

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
    public function getPrependedCode(): array
    {
        return $this->prepended_code;
    }

    /**
     * @param array $prepended_code
     */
    public function setPrependedCode(array $prepended_code): void
    {
        $this->prepended_code = $prepended_code;
    }

    /**
     * @param string $key
     * @param string $code
     */
    public function addPrependedCode(string $key, string $code): void
    {
        $this->prepended_code[$key] = $code;
    }

    /**
     * @return array
     */
    public function getAppendedCode(): array
    {
        return $this->appended_code;
    }

    /**
     * @param array $appended_code
     */
    public function setAppendedCode(array $appended_code): void
    {
        $this->appended_code = $appended_code;
    }

    /**
     * @param string $key
     * @param string $code
     */
    public function addAppendedCode(string $key, string $code): void
    {
        $this->appended_code[$key] = $code;
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
     * @param StepKeeper $step
     */
    public function addStep(StepKeeper $step): void
    {
        $this->steps[] = $step;
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

        foreach ($this->prepended_code as $code) {
            $body .= $code . PHP_EOL . PHP_EOL;
        }

        /** @var StepKeeper $step */
        foreach ($this->steps as $step) {
            foreach ($step->getBeforeCode() as $code) {
                $body .= $code . PHP_EOL . PHP_EOL;
            }

            if ($this->hasValidation() && $step->getExtraCondition()) {
                $body .= 'if ($_valid === ' . ($step->isValidationCondition() ? 'true' : 'false') . ' && ' . $step->getExtraCondition() . ') {' . PHP_EOL;
            } elseif ($this->hasValidation() && !$step->getExtraCondition()) {
                $body .= 'if ($_valid === ' . ($step->isValidationCondition() ? 'true' : 'false') . ') {' . PHP_EOL;
            } elseif (!$this->hasValidation() && $step->getExtraCondition()) {
                $body .= 'if (' . $step->getExtraCondition() . ') {' . PHP_EOL;
            }

            foreach ($step->getPrependedCode() as $code) {
                $body .= $code . PHP_EOL . PHP_EOL;
            }

            if ($step->getReturnExpression()) {
                $body .= $step->getReturnExpression() . ' = ';
            }

            $body .= $step->getCallExpression() . $step->getMethod() . '(' . implode(', ', $step->getArguments()) . ');' . PHP_EOL . PHP_EOL;

            foreach ($step->getAppendedCode() as $code) {
                $body .= $code . PHP_EOL . PHP_EOL;
            }

            if ($this->hasValidation() || $step->getExtraCondition()) {
                $body .= '}' . PHP_EOL . PHP_EOL;
            }

            foreach ($step->getAfterCode() as $code) {
                $body .= $code . PHP_EOL . PHP_EOL;
            }
        }

        foreach ($this->appended_code as $code) {
            $body .= $code . PHP_EOL . PHP_EOL;
        }

        $this->generator->setBody($body);
    }
}
