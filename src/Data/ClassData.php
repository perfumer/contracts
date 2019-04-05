<?php

namespace Perfumerlabs\Perfumer\Data;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

final class ClassData
{
    /**
     * @var array
     */
    private $shared_classes = [];

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

    public function __construct()
    {
        $this->generator = new ClassGenerator();
    }

    public function getSharedClasses(): array
    {
        return $this->shared_classes;
    }

    public function setSharedClasses(array $shared_classes): void
    {
        $this->shared_classes = $shared_classes;
    }

    public function addSharedClass(string $shared_class): void
    {
        if ($shared_class[0] !== '\\') {
            $shared_class = '\\' . $shared_class;
        }

        if (in_array($shared_class, $this->shared_classes)) {
            return;
        }

        $this->shared_classes[] = $shared_class;
    }

    public function getInjections(): array
    {
        return $this->injections;
    }

    public function setInjections(array $injections): void
    {
        $this->injections = $injections;
    }

    public function addInjection(string $name, string $type): void
    {
        if ($type[0] !== '\\') {
            $type = '\\' . $type;
        }

        $this->injections[$name] = $type;
    }

    public function hasInjection(string $name): bool
    {
        return isset($this->injections[$name]);
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function addTag(string $name): void
    {
        if (!$this->hasTag($name)) {
            $this->tags[] = $name;
        }
    }

    public function hasTag(string $name): bool
    {
        return in_array($name, $this->tags);
    }

    public function getGenerator(): ClassGenerator
    {
        return $this->generator;
    }

    public function setGenerator(ClassGenerator $generator): void
    {
        $this->generator = $generator;
    }

    public function generate(): string
    {
        $this->generateSharedClasses();
        $this->generateInjections();

        return $this->generator->generate();
    }

    private function generateSharedClasses(): void
    {
        foreach ($this->shared_classes as $name => $class) {
            $name = str_replace('\\', '_', trim($class, '\\'));

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
            $property->setName('_shared_' . $name);

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
            $getter->setName('get_' . $name);
            $getter->setReturnType($class);

            $getter->setBody('
                if ($this->_shared_' . $name . ' === null) {
                    $this->_shared_' . $name . ' = new ' . $class . '();
                }
                
                return $this->_shared_' . $name . ';'
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
            $property->setName('_inject_' . $name);

            $this->generator->addPropertyFromGenerator($property);

            $constructor = $this->generator->getMethod('__construct');

            if (!$constructor) {
                $constructor = new MethodGenerator();
                $constructor->setVisibility('public');
                $constructor->setName('__construct');

                $this->generator->addMethodFromGenerator($constructor);
            }

            $body = $constructor->getBody() . PHP_EOL . '$this->_inject_' . $name . ' = $' . $name . ';';

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
            $getter->setBody('return $this->_inject_' . $name . ';');

            $this->generator->addMethodFromGenerator($getter);
        }
    }
}
