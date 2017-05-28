<?php

namespace Generated\Tests\Perfumer\Contracts\Example\Contract\Controller;

abstract class FooControllerContext extends \PHPUnit\Framework\TestCase
{
    abstract public function intTypeDataProvider();

    final public function testIntType($value, $expected)
    {
        $_class_instance = new \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestIntType($expected, $_class_instance->intType($value));
    }

    protected function assertTestIntType($expected, $result)
    {
        $this->assertEquals($expected, $result);
    }

    abstract public function sumDataProvider();

    final public function testSum(int $a, int $staff, $expected)
    {
        $_class_instance = new \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestSum($expected, $_class_instance->sum($a, $staff));
    }

    protected function assertTestSum($expected, $result)
    {
        $this->assertEquals($expected, $result);
    }

    abstract public function fooErrorsDataProvider();

    final public function testFooErrors($expected)
    {
        $_class_instance = new \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestFooErrors($expected, $_class_instance->fooErrors());
    }

    protected function assertTestFooErrors($expected, $result)
    {
        $this->assertEquals($expected, $result);
    }
}
