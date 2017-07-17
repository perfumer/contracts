<?php

namespace Perfumer\Contracts\Example\Service;

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
    public function product(int $a, int $b): int
    {
        return $a * $b;
    }
}
