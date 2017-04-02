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
    final public function test_intType($value, $result)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Context\FooContext();

        $this->assertEquals($_class_instance->intType($value), $result);
    }

    /**
     * @dataProvider sumDataProvider
     */
    final public function test_sum($a, $b, $result)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Context\FooContext();

        $this->assertEquals($_class_instance->sum($a, $b), $result);
    }

    /**
     * @dataProvider fooErrorsDataProvider
     */
    final public function test_fooErrors($result)
    {
        $_class_instance = new \Perfumer\Component\Contracts\Example\Context\FooContext();

        $this->assertEquals($_class_instance->fooErrors(), $result);
    }

}
