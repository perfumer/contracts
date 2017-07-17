<?php

namespace Perfumer\Contracts\Example\Contract;

use Perfumer\Contracts\Annotation\Context;
use Perfumer\Contracts\Annotation\Output;

/**
 * @Context(name="math", class="\Perfumer\Contracts\Example\Context\Math")
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
