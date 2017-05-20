<?php

namespace Perfumer\Contracts;

final class ClassBuilder
{
    /**
     * @var \ReflectionClass
     */
    private $contract;

    /**
     * @var null|string
     */
    private $namespace;

    /**
     * @var array
     */
    private $uses = [];

    /**
     * @var bool
     */
    private $is_final = true;

    /**
     * @var bool
     */
    private $is_abstract = false;

    /**
     * @var null|string
     */
    private $class_name;

    /**
     * @var null|string
     */
    private $parent_class;

    /**
     * @var array
     */
    private $interfaces = [];

    /**
     * @var array
     */
    private $traits = [];

    /**
     * @var array
     */
    private $public_properties = [];

    /**
     * @var array
     */
    private $protected_properties = [];

    /**
     * @var array
     */
    private $private_properties = [];

    /**
     * @var array
     */
    private $contexts = [];

    /**
     * @var array
     */
    private $injections = [];

    /**
     * @var array
     */
    private $methods = [];

    /**
     * @return \ReflectionClass
     */
    public function getContract(): \ReflectionClass
    {
        return $this->contract;
    }

    /**
     * @param \ReflectionClass $contract
     */
    public function setContract(\ReflectionClass $contract): void
    {
        $this->contract = $contract;
    }

    /**
     * @return null|string
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * @param null|string $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return array
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @param array $uses
     */
    public function setUses(array $uses): void
    {
        $this->uses = $uses;
    }

    /**
     * @param string $use
     */
    public function addUse(string $use): void
    {
        $this->uses[] = $use;
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->is_final;
    }

    /**
     * @param bool $is_final
     */
    public function setIsFinal(bool $is_final): void
    {
        $this->is_final = $is_final;
    }

    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->is_abstract;
    }

    /**
     * @param bool $is_abstract
     */
    public function setIsAbstract(bool $is_abstract): void
    {
        $this->is_abstract = $is_abstract;
    }

    /**
     * @return null|string
     */
    public function getClassName(): ?string
    {
        return $this->class_name;
    }

    /**
     * @param null|string $class_name
     */
    public function setClassName($class_name): void
    {
        $this->class_name = $class_name;
    }

    /**
     * @return null|string
     */
    public function getParentClass(): ?string
    {
        return $this->parent_class;
    }

    /**
     * @param null|string $parent_class
     */
    public function setParentClass($parent_class): void
    {
        $this->parent_class = $parent_class;
    }

    /**
     * @return array
     */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * @param array $interfaces
     */
    public function setInterfaces(array $interfaces): void
    {
        $this->interfaces = $interfaces;
    }

    /**
     * @param string $interface
     */
    public function addInterface(string $interface): void
    {
        $this->interfaces[] = $interface;
    }

    /**
     * @return array
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @param array $traits
     */
    public function setTraits(array $traits): void
    {
        $this->traits = $traits;
    }

    /**
     * @param string $trait
     */
    public function addTrait(string $trait): void
    {
        $this->traits[] = $trait;
    }

    /**
     * @return array
     */
    public function getPublicProperties(): array
    {
        return $this->public_properties;
    }

    /**
     * @param array $public_properties
     */
    public function setPublicProperties(array $public_properties): void
    {
        $this->public_properties = $public_properties;
    }

    /**
     * @param string $name
     * @param null|string $type
     */
    public function addPublicProperty(string $name, ?string $type = null): void
    {
        $this->public_properties[$name] = $type;
    }

    /**
     * @return array
     */
    public function getProtectedProperties(): array
    {
        return $this->protected_properties;
    }

    /**
     * @param array $protected_properties
     */
    public function setProtectedProperties(array $protected_properties): void
    {
        $this->protected_properties = $protected_properties;
    }

    /**
     * @param string $name
     * @param null|string $type
     */
    public function addProtectedProperty(string $name, ?string $type = null): void
    {
        $this->protected_properties[$name] = $type;
    }

    /**
     * @return array
     */
    public function getPrivateProperties(): array
    {
        return $this->private_properties;
    }

    /**
     * @param array $private_properties
     */
    public function setPrivateProperties(array $private_properties): void
    {
        $this->private_properties = $private_properties;
    }

    /**
     * @param string $name
     * @param null|string $type
     */
    public function addPrivateProperty(string $name, ?string $type = null): void
    {
        $this->private_properties[$name] = $type;
    }

    /**
     * @return array
     */
    public function getContexts(): array
    {
        return $this->contexts;
    }

    /**
     * @param array $contexts
     */
    public function setContexts(array $contexts): void
    {
        $this->contexts = $contexts;
    }

    /**
     * @param string $name
     * @param string $class
     */
    public function addContext(string $name, string $class): void
    {
        $this->contexts[$name] = $class;
    }

    /**
     * @return array
     */
    public function getInjections(): array
    {
        return $this->injections;
    }

    /**
     * @param array $injections
     */
    public function setInjections(array $injections): void
    {
        $this->injections = $injections;
    }

    /**
     * @param string $name
     * @param string $type
     */
    public function addInjection(string $name, string $type): void
    {
        $this->injections[$name] = $type;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @param MethodBuilder $method
     */
    public function addMethod(MethodBuilder $method): void
    {
        $this->methods[] = $method;
    }
}
