<?php

namespace Barman\Keeper;

use Zend\Code\Generator\ClassGenerator;

final class TestCaseKeeper
{
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
        return $this->generator->generate();
    }
}
