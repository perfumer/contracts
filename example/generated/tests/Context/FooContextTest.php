<?php

namespace Generated\Tests\Perfumer\Component\Contracts\Example\Context;

use PHPUnit\Framework\TestCase;

abstract class FooContextTest extends TestCase
{
    abstract public function intTypeDataProvider();

    abstract public function sumDataProvider();

    abstract public function fooErrorsDataProvider();

    /**
     * @dataProvider intTypeDataProvider
     */
    final public function testIntType($value, $expected)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Context\FooContext();

        $this->assertTestIntType($_class_instance->intType($value), $expected);
    }

    /**
     * @dataProvider sumDataProvider
     */
    final public function testSum($a, $b, $expected)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Context\FooContext();

        $this->assertTestSum($_class_instance->sum($a, $b), $expected);
    }

    /**
     * @dataProvider fooErrorsDataProvider
     */
    final public function testFooErrors($expected)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Context\FooContext();

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
