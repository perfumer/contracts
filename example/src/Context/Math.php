<?php

namespace Barman\Example\Context;

use Barman\Annotation\Test;

/**
 * Context is a class with no constructor and consists of small methods.
 * Context is not such "big" class to call it some kind of service class.
 * Context is more like library of functions.
 */
class Math
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
