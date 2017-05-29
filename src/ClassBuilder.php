<?php

namespace Perfumer\Contracts;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

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

    /**
     * @return string
     */
    public function generate()
    {
        $this->generateContexts();
        $this->generateInjections();

        return parent::generate();
    }

    private function generateContexts()
    {
        foreach ($this->contexts as $name => $class) {
            $doc_block = DocBlockGenerator::fromArray([
                'tags' => [
                    [
                        'name'        => 'var',
                        'description' => $class,
                    ]
                ],
            ]);

            $property = new PropertyGenerator();
            $property->setDocBlock($doc_block);
            $property->setVisibility('private');
            $property->setName('_context_' . $name);

            $this->addPropertyFromGenerator($property);

            $doc_block = DocBlockGenerator::fromArray([
                'tags' => [
                    new ReturnTag([
                        'datatype'  => $class,
                    ]),
                ],
            ]);

            $getter = new MethodGenerator();
            $getter->setDocBlock($doc_block);
            $getter->setFinal(true);
            $getter->setVisibility('private');
            $getter->setName('get' . str_replace('_', '', ucwords($name, '_')) . 'Context');
            $getter->setReturnType($class);

            $getter->setBody('
                if ($this->_context_' . $name . ' === null) {
                    $this->_context_' . $name . ' = new ' . $class . '();
                }
                
                return $this->_context_' . $name . ';'
            );

            $this->addMethodFromGenerator($getter);
        }
    }

    private function generateInjections()
    {
        foreach ($this->injections as $name => $type) {
            $doc_block = DocBlockGenerator::fromArray([
                'tags' => [
                    [
                        'name'        => 'var',
                        'description' => $type,
                    ]
                ],
            ]);

            $property = new PropertyGenerator();
            $property->setDocBlock($doc_block);
            $property->setVisibility('private');
            $property->setName('_injection_' . $name);

            $this->addPropertyFromGenerator($property);

            $constructor = $this->getMethod('__construct');

            if (!$constructor) {
                $constructor = new MethodGenerator();
                $constructor->setVisibility('public');
                $constructor->setName('__construct');

                $this->addMethodFromGenerator($constructor);
            }

            $body = $constructor->getBody() . PHP_EOL . '$this->_injection_' . $name . ' = $' . $name . ';';

            $constructor->setParameter(new ParameterGenerator($name, $type));
            $constructor->setBody($body);

            $doc_block = DocBlockGenerator::fromArray([
                'tags' => [
                    new ReturnTag([
                        'datatype'  => $type,
                    ]),
                ],
            ]);

            $getter = new MethodGenerator();
            $getter->setDocBlock($doc_block);
            $getter->setFinal(true);
            $getter->setVisibility('protected');
            $getter->setName('get' . str_replace('_', '', ucwords($name, '_')));
            $getter->setReturnType($type);
            $getter->setBody('return $this->_injection_' . $name . ';');

            $this->addMethodFromGenerator($getter);
        }
    }
}
