<?php

namespace Barman\Example\Service;

class Math
{
    /**
     * @param int $a
     * @param int $b
     * @return int
     */
    public function sum(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * @param int $a
     * @param int $b
     * @return int
     */
    public function difference(int $a, int $b): int
    {
        return $a - $b;
    }

    /**
     * @param int $a
     * @param int $b
     * @return int
     */
    public function product(int $a, int $b): int
    {
        return $a * $b;
    }

    /**
     * @param int $a
     * @return int
     */
    static public function square(int $a): int
    {
        return $a * $a;
    }
}
