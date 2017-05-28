<?php

namespace Perfumer\Contracts;

use Zend\Code\Generator\ClassGenerator;

final class ClassBuilder extends ClassGenerator
{
    /**
     * @var \ReflectionClass
     */
    private $contract;

    /**
     * @var array
     */
    private $contexts = [];

    /**
     * @var array
     */
    private $injections = [];

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
}
