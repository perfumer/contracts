<?php

namespace Generated\Tests\Perfumer\Component\Contracts\Example\Controller;

use PHPUnit\Framework\TestCase;

abstract class FooControllerTest extends TestCase
{
    final public function testSyntax()
    {
        new \ReflectionClass(\Perfumer\Component\Contracts\Example\Controller\FooController::class);
    }

    final public function testBarActionLocalVariables()
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
        $this->assertNotEmpty($param1_valid);
        $this->assertNotEmpty($param2_valid);
    }

    final public function testBazActionLocalVariables()
    {
        $param1 = true;
        $param2 = true;
        $this->assertNotEmpty($param1);
        $param1_valid = true;
        $this->assertNotEmpty($param1_valid);
        $this->assertNotEmpty($param2);
        $param2_valid = true;
        $this->assertNotEmpty($param1);
        $sum = true;
        $this->assertNotEmpty($sum);
        $sandbox = true;
    }
}
