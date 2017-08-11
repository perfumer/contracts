<?php

namespace Generated\Tests\Barman\Example;

abstract class Example7Test extends \PHPUnit\Framework\TestCase
{
    final public function testSyntax()
    {
        new \ReflectionClass(\Barman\Example\Example7::class);
    }

    final public function testSumLocalVariables()
    {
        $foo = true;
        $bar = true;
        $this->assertNotEmpty($foo);
        $this->assertNotEmpty($bar);
    }
}
