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

        $this->assertIntType($_class_instance->intType($value), $expected);
    }

    /**
     * @dataProvider sumDataProvider
     */
    final public function testSum($a, $b, $expected)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Context\FooContext();

        $this->assertSum($_class_instance->sum($a, $b), $expected);
    }

    /**
     * @dataProvider fooErrorsDataProvider
     */
    final public function testFooErrors($expected)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Context\FooContext();

        $this->assertFooErrors($_class_instance->fooErrors(), $expected);
    }

    protected function assertIntType($result, $expected)
    {
        $this->assertEquals($result, $expected);
    }

    protected function assertSum($result, $expected)
    {
        $this->assertEquals($result, $expected);
    }

    protected function assertFooErrors($result, $expected)
    {
        $this->assertEquals($result, $expected);
    }

}
