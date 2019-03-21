<?php

namespace Barman\Example\Contract;

use Generated\Annotation\Barman\Example\Contract\Example1Context\Product;
use Generated\Annotation\Barman\Example\Contract\Example1Context\Sum;
use Perfumerlabs\Perfumer\Annotation\Set;
use Perfumerlabs\Perfumer\Annotation\SetFromProperty;
use Perfumerlabs\Perfumer\Annotation\Out;
use Perfumerlabs\Perfumer\Annotation\Error;
use Perfumerlabs\Perfumer\Annotation\Test;

interface Example1
{
    /**
     * @Set(name="a", value="c")
     * @SetFromProperty(name="b", value="foobar")
     *
     * @Sum(out="sum")
     *
     * @Product(a="sum", b="c", out="rrr")
     *
     * @Error(name="b", unless="sum")
     *
     * @Out(name="rrr")
     *
     * @param int $c
     * @return int
     */
    public function sumThenProduct(int $c): int;
}

class Example1Context
{
    /**
     * @Test
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    public function sum(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * @Test
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    public function product(int $a, int $b): int
    {
        return $a * $b;
    }
}