<?php

namespace Perfumer\Component\Contracts;

class RuntimeContext
{
    /**
     * @var string
     */
    protected $template = 'BaseClass';

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $class_name;

    /**
     * @var string
     */
    protected $extends_class;

    /**
     * @var string
     */
    protected $extends_test;

    /**
     * @var array
     */
    protected $protected_properties = [];

    /**
     * @var array
     */
    protected $private_properties = [];

    /**
     * @var array
     */
    protected $steps = [];

    /**
     * @var array
     */
    protected $interfaces = [];

    /**
     * @var array
     */
    protected $actions = [];

    /**
     * @var array
     */
    protected $contexts = [];

    /**
     * @var array
     */
    protected $injected = [];

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string
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
     * @return string
     */
    public function getExtendsClass()
    {
        return $this->extends_class;
    }

    /**
     * @param string $extends_class
     */
    public function setExtendsClass($extends_class)
    {
        $this->extends_class = $extends_class;
    }

    /**
     * @return string
     */
    public function getExtendsTest()
    {
        return $this->extends_test;
    }

    /**
     * @param string $extends_test
     */
    public function setExtendsTest($extends_test)
    {
        $this->extends_test = $extends_test;
    }

    /**
     * @return array
     */
    public function getProtectedProperties()
    {
        return $this->protected_properties;
    }

    /**
     * @param array $protected_properties
     */
    public function setProtectedProperties($protected_properties)
    {
        $this->protected_properties = $protected_properties;
    }

    /**
     * @param string $protected_property
     * @param mixed $type
     */
    public function addProtectedProperty($protected_property, $type = null)
    {
        if (!$this->hasProtectedProperty($protected_property)) {
            $this->protected_properties[$protected_property] = $type;
        }
    }

    /**
     * @param string $protected_property
     * @return bool
     */
    public function hasProtectedProperty($protected_property)
    {
        return isset($this->protected_properties[$protected_property]);
    }

    /**
     * @return array
     */
    public function getPrivateProperties()
    {
        return $this->private_properties;
    }

    /**
     * @param array $private_properties
     */
    public function setPrivateProperties($private_properties)
    {
        $this->private_properties = $private_properties;
    }

    /**
     * @param string $private_property
     * @param mixed $type
     */
    public function addPrivateProperty($private_property, $type = null)
    {
        if (!$this->hasProtectedProperty($private_property)) {
            $this->private_properties[$private_property] = $type;
        }
    }

    /**
     * @param string $private_property
     * @return bool
     */
    public function hasPrivateProperty($private_property)
    {
        return isset($this->private_properties[$private_property]);
    }

    /**
     * @return array
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param string $key
     * @param RuntimeStep $step
     */
    public function addStep($key, RuntimeStep $step)
    {
        $this->steps[$key] = $step;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasStep($key)
    {
        return isset($this->steps[$key]);
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
    public function setInterfaces(array $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    /**
     * @param string $interface
     */
    public function addInterface(string $interface)
    {
        $this->interfaces[] = $interface;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param RuntimeAction $action
     */
    public function addAction(RuntimeAction $action)
    {
        $this->actions[] = $action;
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
    public function setContexts(array $contexts)
    {
        $this->contexts = $contexts;
    }

    /**
     * @return array
     */
    public function getInjected(): array
    {
        return $this->injected;
    }

    /**
     * @param array $injected
     */
    public function setInjected(array $injected)
    {
        $this->injected = $injected;
    }
}
