<?php

namespace Perfumer\Contracts;

class Argument
{
    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @var null|string
     */
    protected $doc_type;

    /**
     * @var bool
     */
    protected $allows_null = false;

    /**
     * @var null|string
     */
    protected $default_value;

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getDocType(): ?string
    {
        return $this->doc_type;
    }

    /**
     * @param null|string $doc_type
     */
    public function setDocType($doc_type): void
    {
        $this->doc_type = $doc_type;
    }

    /**
     * @return bool
     */
    public function allowsNull(): bool
    {
        return $this->allows_null;
    }

    /**
     * @param bool $allows_null
     */
    public function setAllowsNull(bool $allows_null): void
    {
        $this->allows_null = $allows_null;
    }

    /**
     * @return null|string
     */
    public function getDefaultValue(): ?string
    {
        return $this->default_value;
    }

    /**
     * @param null|string $default_value
     */
    public function setDefaultValue($default_value): void
    {
        $this->default_value = $default_value;
    }
}
