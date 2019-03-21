<?php

namespace Perfumerlabs\Perfumer\Data;

final class StepData
{
    /**
     * @var null|string
     */
    private $before_code;

    /**
     * @var null|string
     */
    private $after_code;

    /**
     * @var null|string
     */
    private $code;

    /**
     * @var bool
     */
    private $validation_condition = true;

    /**
     * @var null|string
     */
    private $extra_condition;

    public function getBeforeCode(): ?string
    {
        return $this->before_code;
    }

    public function setBeforeCode(?string $before_code): void
    {
        $this->before_code = $before_code;
    }

    public function getAfterCode(): ?string
    {
        return $this->after_code;
    }

    public function setAfterCode(?string $after_code): void
    {
        $this->after_code = $after_code;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getValidationCondition(): bool
    {
        return $this->validation_condition;
    }

    public function setValidationCondition(bool $validation_condition): void
    {
        $this->validation_condition = $validation_condition;
    }

    public function getExtraCondition(): ?string
    {
        return $this->extra_condition;
    }

    public function setExtraCondition(?string $extra_condition): void
    {
        $this->extra_condition = $extra_condition;
    }
}
