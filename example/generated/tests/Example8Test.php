<?php

namespace Generated\Tests\Barman\Example;

abstract class Example8Test extends \PHPUnit\Framework\TestCase
{
    final public function testQuotientLocalVariables()
    {
        $a = true;
        $b = true;
        $this->assertNotEmpty($b);
        $b_non_zero = true;
        $this->assertNotEmpty($a);
        $this->assertNotEmpty($b);
        $this->assertNotEmpty($b_non_zero);
    }
}
