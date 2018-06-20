<?php

namespace Barman\Keeper;

final class StepKeeper
{
    /**
     * @var string
     */
    private $before_code;

    /**
     * @var string
     */
    private $after_code;

    /**
     * @var string
     */
    private $appended_code;

    /**
     * @var string
     */
    private $prepended_code;

    /**
     * @var bool
     */
    private $validation_condition = true;

    /**
     * @var null|string
     */
    private $extra_condition;

    /**
     * @var null|string
     */
    private $return_expression;

    /**
     * @var null|string
     */
    private $call_expression;

    /**
     * @var null|string
     */
    private $method;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @return string
     */
    public function getBeforeCode(): string
    {
        return $this->before_code;
    }

    /**
     * @param string $before_code
     */
    public function setBeforeCode(string $before_code): void
    {
        $this->before_code = $before_code;
    }

    /**
     * @return string
     */
    public function getAfterCode(): ?string
    {
        return $this->after_code;
    }

    /**
     * @param string $after_code
     */
    public function setAfterCode(string $after_code): void
    {
        $this->after_code = $after_code;
    }

    /**
     * @return string
     */
    public function getAppendedCode(): ?string
    {
        return $this->appended_code;
    }

    /**
     * @param string $appended_code
     */
    public function setAppendedCode(string $appended_code): void
    {
        $this->appended_code = $appended_code;
    }

    /**
     * @return string
     */
    public function getPrependedCode(): ?string
    {
        return $this->prepended_code;
    }

    /**
     * @param string $prepended_code
     */
    public function setPrependedCode(string $prepended_code): void
    {
        $this->prepended_code = $prepended_code;
    }

    /**
     * @return bool
     */
    public function isValidationCondition(): bool
    {
        return $this->validation_condition;
    }

    /**
     * @param bool $validation_condition
     */
    public function setValidationCondition(bool $validation_condition): void
    {
        $this->validation_condition = $validation_condition;
    }

    /**
     * @return null|string
     */
    public function getExtraCondition(): ?string
    {
        return $this->extra_condition;
    }

    /**
     * @param null|string $extra_condition
     */
    public function setExtraCondition($extra_condition): void
    {
        $this->extra_condition = $extra_condition;
    }

    /**
     * @return null|string
     */
    public function getReturnExpression(): ?string
    {
        return $this->return_expression;
    }

    /**
     * @param null|string $return_expression
     */
    public function setReturnExpression($return_expression): void
    {
        $this->return_expression = $return_expression;
    }

    /**
     * @return null|string
     */
    public function getCallExpression(): ?string
    {
        return $this->call_expression;
    }

    /**
     * @param null|string $call_expression
     */
    public function setCallExpression($call_expression): void
    {
        $this->call_expression = $call_expression;
    }

    /**
     * @return null|string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param null|string $method
     */
    public function setMethod($method): void
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @param string $argument
     */
    public function addArgument(string $argument): void
    {
        $this->arguments[] = $argument;
    }
}
