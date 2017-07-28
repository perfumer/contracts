<?php

namespace Barman\Example\Contract;

use Barman\Annotation\Context;
use Barman\Annotation\Output;

/**
 * @Context(name="math", class="\Barman\Example\Context\Math")
 */
interface Example1
{
    /**
     * Get sum of $a and $b, then multiply result on $c
     *
     * @Context(name="math", method="sum", arguments={"a", "b"}, return="sum")
     * @Context(name="math", method="product", arguments={"sum", "c"}, return=@Output)
     *
     * @param int $a
     * @param int $b
     * @param int $c
     * @return int
     */
    public function sumThenProduct(int $a, int $b, int $c): int;
}
