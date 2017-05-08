<?php

namespace Generated\Tests\Perfumer\Component\Contracts\Example\Contract\Controller;

use PHPUnit\Framework\TestCase;

abstract class FooControllerContextTest extends TestCase
{
    abstract public function intTypeDataProvider();

    abstract public function sumDataProvider();

    abstract public function fooErrorsDataProvider();

    /**
     * @dataProvider intTypeDataProvider
     */
    final public function testIntType($value, $expected)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestIntType($_class_instance->intType($value), $expected);
    }

    /**
     * @dataProvider sumDataProvider
     */
    final public function testSum($a, $staff, $expected)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestSum($_class_instance->sum($a, $staff), $expected);
    }

    /**
     * @dataProvider fooErrorsDataProvider
     */
    final public function testFooErrors($expected)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Contract\Controller\FooControllerContext();

        $this->assertTestFooErrors($_class_instance->fooErrors(), $expected);
    }

    protected function assertTestIntType($result, $expected)
    {
        $this->assertEquals($result, $expected);
    }

    protected function assertTestSum($result, $expected)
    {
        $this->assertEquals($result, $expected);
    }

    protected function assertTestFooErrors($result, $expected)
    {
        $this->assertEquals($result, $expected);
    }

}
