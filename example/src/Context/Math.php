<?php

namespace Perfumer\Contracts\Example\Context;

use Perfumer\Contracts\Annotation\Test;

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
