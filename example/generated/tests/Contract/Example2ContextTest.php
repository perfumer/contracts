<?php

namespace Generated\Tests\Perfumer\Contracts\Example\Contract;

abstract class Example2ContextTest extends \PHPUnit\Framework\TestCase
{
    abstract public function sumDataProvider();

    abstract public function productDataProvider();

    /**
     * @dataProvider sumDataProvider
     */
    final public function testSum(int $a, int $b, $expected)
    {
        $_class_instance = new \Perfumer\Contracts\Example\Contract\Example2Context();

        $this->assertTestSum($expected, $_class_instance->sum($a, $b));
    }

    /**
     * @dataProvider productDataProvider
     */
    final public function testProduct(int $a, int $b, $expected)
    {
        $_class_instance = new \Perfumer\Contracts\Example\Contract\Example2Context();

        $this->assertTestProduct($expected, $_class_instance->product($a, $b));
    }

    protected function assertTestSum($expected, $result)
    {
        $this->assertEquals($expected, $result);
    }

    protected function assertTestProduct($expected, $result)
    {
        $this->assertEquals($expected, $result);
    }
}
