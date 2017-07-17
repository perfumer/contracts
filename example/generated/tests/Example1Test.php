<?php

namespace Generated\Tests\Perfumer\Contracts\Example;

abstract class Example1Test extends \PHPUnit\Framework\TestCase
{
    final public function testSyntax()
    {
        new \ReflectionClass(\Perfumer\Contracts\Example\Example1::class);
    }

    final public function testSumThenProductLocalVariables()
    {
        $a = true;
        $b = true;
        $c = true;
        $this->assertNotEmpty($a);
        $this->assertNotEmpty($b);
        $sum = true;
        $this->assertNotEmpty($sum);
        $this->assertNotEmpty($c);
    }
}
