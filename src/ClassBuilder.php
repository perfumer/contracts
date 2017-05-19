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
     * @var \ArrayObject
     */
    private $uses;

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
     * @var \ArrayObject
     */
    private $interfaces;

    /**
     * @var \ArrayObject
     */
    private $traits;

    /**
     * @var \ArrayObject
     */
    private $public_properties;

    /**
     * @var \ArrayObject
     */
    private $protected_properties;

    /**
     * @var \ArrayObject
     */
    private $private_properties;

    /**
     * @var \ArrayObject
     */
    private $contexts;

    /**
     * @var \ArrayObject
     */
    private $injections;

    /**
     * @var \ArrayObject
     */
    private $methods;

    /**
     * ClassBuilder constructor.
     */
    public function __construct()
    {
        $this->uses = new \ArrayObject();
        $this->interfaces = new \ArrayObject();
        $this->traits = new \ArrayObject();
        $this->public_properties = new \ArrayObject();
        $this->protected_properties = new \ArrayObject();
        $this->private_properties = new \ArrayObject();
        $this->contexts = new \ArrayObject();
        $this->injections = new \ArrayObject();
        $this->methods = new \ArrayObject();
    }

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
    public function setContract(\ReflectionClass $contract)
    {
        $this->contract = $contract;
    }

    /**
     * @return null|string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param null|string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
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
    public function setIsFinal(bool $is_final)
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
    public function setIsAbstract(bool $is_abstract)
    {
        $this->is_abstract = $is_abstract;
    }

    /**
     * @return null|string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * @param string $class_name
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;
    }

    /**
     * @return null|string
     */
    public function getParentClass()
    {
        return $this->parent_class;
    }

    /**
     * @param null|string $parent_class
     */
    public function setParentClass($parent_class)
    {
        $this->parent_class = $parent_class;
    }

    /**
     * @return \ArrayObject
     */
    public function getUses(): \ArrayObject
    {
        return $this->uses;
    }

    /**
     * @return \ArrayObject
     */
    public function getInterfaces(): \ArrayObject
    {
        return $this->interfaces;
    }

    /**
     * @return \ArrayObject
     */
    public function getTraits(): \ArrayObject
    {
        return $this->traits;
    }

    /**
     * @return \ArrayObject
     */
    public function getPublicProperties(): \ArrayObject
    {
        return $this->public_properties;
    }

    /**
     * @return \ArrayObject
     */
    public function getProtectedProperties(): \ArrayObject
    {
        return $this->protected_properties;
    }

    /**
     * @return \ArrayObject
     */
    public function getPrivateProperties(): \ArrayObject
    {
        return $this->private_properties;
    }

    /**
     * @return \ArrayObject
     */
    public function getContexts(): \ArrayObject
    {
        return $this->contexts;
    }

    /**
     * @return \ArrayObject
     */
    public function getInjections(): \ArrayObject
    {
        return $this->injections;
    }

    /**
     * @return \ArrayObject
     */
    public function getMethods(): \ArrayObject
    {
        return $this->methods;
    }
}
