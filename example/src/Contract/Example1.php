<?php

namespace Barman\Example\Contract;

use Generated\Annotation\Barman\Example\Contract\Example1Context\Product;
use Generated\Annotation\Barman\Example\Contract\Example1Context\Sum;
use Perfumerlabs\Perfumer\Annotation\AddDefaultContext;
use Perfumerlabs\Perfumer\Annotation\After;
use Perfumerlabs\Perfumer\Annotation\Before;
use Perfumerlabs\Perfumer\Annotation\Inject;
use Perfumerlabs\Perfumer\Annotation\Set;
use Perfumerlabs\Perfumer\Annotation\SetFromProperty;
use Perfumerlabs\Perfumer\Annotation\Out;
use Perfumerlabs\Perfumer\Annotation\Error;
use Perfumerlabs\Perfumer\Annotation\Test;

/**
 * @AddDefaultContext()
 *
 * @Inject(name="lalala", type="Barman\Example\Service\Math")
 *
 * @Before(steps={
 *   @Set(name="a", value="c")
 * })
 *
 * @After(steps={
 *   @Out(name="rrr")
 * })
 */
interface Example1
{
    /**
     * @Sum(           out="sum")
     * @Product(b="c", out="rrr")
     *
     * @Error(name="b", unless="sum")
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
     * @SetFromProperty(name="b", value="foobar")
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
     * @SetFromProperty(name="a", value="barbaz")
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