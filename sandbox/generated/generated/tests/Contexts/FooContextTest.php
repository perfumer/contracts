<?php

namespace Generated\Tests\Perfumer\Component\Bdd\Sandbox\Contexts;

abstract class FooContextTest extends \PHPUnit_Framework_TestCase
{
    abstract public function intTypeDataProvider();

    abstract public function sumDataProvider();

    /**
     * @dataProvider intTypeDataProvider
     */
    final public function test_intType($value, $result)
    {
        $_class_instance = new \Perfumer\Component\Bdd\Sandbox\Contexts\FooContext();

        $this->assertEquals($_class_instance->intType($value), $result);
    }

    /**
     * @dataProvider sumDataProvider
     */
    final public function test_sum($a, $b, $result)
    {
        $_class_instance = new \Perfumer\Component\Bdd\Sandbox\Contexts\FooContext();

        $this->assertEquals($_class_instance->sum($a, $b), $result);
    }

}
