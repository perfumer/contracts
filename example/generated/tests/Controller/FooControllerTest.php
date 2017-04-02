<?php

namespace Generated\Tests\Perfumer\Component\Contracts\Example\Controller;

use PHPUnit\Framework\TestCase;

abstract class FooControllerTest extends TestCase
{
    final public function testSyntax()
    {
        new \ReflectionClass(\Perfumer\Component\Contracts\Example\Controller\FooController::class);
    }

    final public function testBarLocalVariables()
    {
        $param1 = true;
        $param2 = true;
        $this->assertNotEmpty($param1);
        $param1_valid = true;
        $this->assertNotEmpty($param1_valid);
        $this->assertNotEmpty($param2);
        $param2_valid = true;
        $this->assertNotEmpty($param1);
        $double_sum = true;
        $sand = true;
        $this->assertNotEmpty($sand);
    }

    final public function testBazLocalVariables()
    {
        $param1 = true;
        $param2 = true;
        $this->assertNotEmpty($param1);
        $param1_valid = true;
        $this->assertNotEmpty($param2);
        $param2_valid = true;
        $this->assertNotEmpty($param1);
        $sum = true;
        $this->assertNotEmpty($sum);
        $sandbox = true;
        $this->assertNotEmpty($param1_valid);
    }
}
