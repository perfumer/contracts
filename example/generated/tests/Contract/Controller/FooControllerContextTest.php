<?php

namespace Generated\Tests\Perfumer\Contracts\Example\Contract\Controller;

abstract class FooControllerContext extends \PHPUnit\Framework\TestCase
{
    abstract public function intTypeDataProvider();

    abstract public function sumDataProvider();

    abstract public function fooErrorsDataProvider();

    final public function testIntType($value, $expected)
    {
        $_class_instance = new \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestIntType($expected, $_class_instance->intType($value));
    }

    final public function testSum(int $a, int $staff, $expected)
    {
        $_class_instance = new \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestSum($expected, $_class_instance->sum($a, $staff));
    }

    final public function testFooErrors($expected)
    {
        $_class_instance = new \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestFooErrors($expected, $_class_instance->fooErrors());
    }

    protected function assertTestIntType($expected, $result)
    {
        $this->assertEquals($expected, $result);
    }

    protected function assertTestSum($expected, $result)
    {
        $this->assertEquals($expected, $result);
    }

    protected function assertTestFooErrors($expected, $result)
    {
        $this->assertEquals($expected, $result);
    }
}
