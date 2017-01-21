<?php

namespace Perfumer\Component\Bdd;

abstract class Context
{
    /**
     * @var string
     */
    protected $namespace_prefix;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $name_suffix;

    /**
     * @var string
     */
    protected $extends_class;

    /**
     * @var string
     */
    protected $extends_test;

    /**
     * @var string
     */
    protected $src_dir;

    /**
     * @var array
     */
    protected $actions;

    /**
     * @return string
     */
    public function getNamespacePrefix()
    {
        return $this->namespace_prefix;
    }

    /**
     * @param string $namespace_prefix
     */
    public function setNamespacePrefix($namespace_prefix)
    {
        $this->namespace_prefix = $namespace_prefix;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNameSuffix()
    {
        return $this->name_suffix;
    }

    /**
     * @param string $name_suffix
     */
    public function setNameSuffix($name_suffix)
    {
        $this->name_suffix = $name_suffix;
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
     * @return string
     */
    public function getSrcDir()
    {
        return $this->src_dir;
    }

    /**
     * @param string $src_dir
     */
    public function setSrcDir($src_dir)
    {
        $this->src_dir = $src_dir;
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
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }
}