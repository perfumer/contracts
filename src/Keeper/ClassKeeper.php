<?php

namespace Barman\Keeper;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

final class ClassKeeper
{
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
    private $tags = [];

    /**
     * @var ClassGenerator
     */
    private $generator;

    /**
     * ClassKeeper constructor.
     */
    public function __construct()
    {
        $this->generator = new ClassGenerator();
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
     * @param string $name
     * @return bool
     */
    public function hasContext(string $name): bool
    {
        return isset($this->contexts[$name]);
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
     * @param string $name
     * @return bool
     */
    public function hasInjection(string $name): bool
    {
        return isset($this->injections[$name]);
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @param string $name
     */
    public function addTag(string $name): void
    {
        if (!$this->hasTag($name)) {
            $this->tags[] = $name;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasTag(string $name): bool
    {
        return in_array($name, $this->tags);
    }

    /**
     * @return ClassGenerator
     */
    public function getGenerator(): ClassGenerator
    {
        return $this->generator;
    }

    /**
     * @param ClassGenerator $generator
     */
    public function setGenerator(ClassGenerator $generator): void
    {
        $this->generator = $generator;
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $this->generateContexts();
        $this->generateInjections();

        return $this->generator->generate();
    }

    private function generateContexts(): void
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

            $this->generator->addPropertyFromGenerator($property);

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

            $this->generator->addMethodFromGenerator($getter);
        }
    }

    private function generateInjections(): void
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

            $this->generator->addPropertyFromGenerator($property);

            $constructor = $this->generator->getMethod('__construct');

            if (!$constructor) {
                $constructor = new MethodGenerator();
                $constructor->setVisibility('public');
                $constructor->setName('__construct');

                $this->generator->addMethodFromGenerator($constructor);
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

            $this->generator->addMethodFromGenerator($getter);
        }
    }
}
