<?php

namespace Barman\Example\Contract;

use Barman\Annotation\Context;
use Barman\Annotation\Output;
use Barman\Annotation\Test;

interface Example2
{
    /**
     * Get sum of $a and $b, then multiply result on $c
     *
     * @Context(method="sum", arguments={"a", "b"}, return="sum")
     * @Context(method="product", arguments={"sum", "c"}, return=@Output)
     *
     * @param int $a
     * @param int $b
     * @param int $c
     * @return int
     */
    public function sumThenProduct(int $a, int $b, int $c): int;
}

class Example2Context
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
