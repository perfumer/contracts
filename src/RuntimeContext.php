<?php

namespace Perfumer\Component\Bdd;

class RuntimeContext
{
    /**
     * @var string
     */
    protected $template = 'BaseClassBuilder';

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
    protected $properties = [];

    /**
     * @var array
     */
    protected $steps = [];

    /**
     * @var array
     */
    protected $actions = [];

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
    public function getProperties()
    {
        return array_unique($this->properties);
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param string $property
     */
    public function addProperty($property)
    {
        $this->properties[] = $property;
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
}
