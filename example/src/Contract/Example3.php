<?php

namespace Perfumer\Contracts\Example\Contract;

use Perfumer\Contracts\Annotation\Injection;
use Perfumer\Contracts\Annotation\Output;

/**
 * @Injection(name="math", type="\Perfumer\Contracts\Example\Service\Math")
 */
interface Example3
{
    /**
     * Get sum of $a and $b, then multiply result on $c
     *
     * @Injection(name="math", method="sum", arguments={"a", "b"}, return="sum")
     * @Injection(name="math", method="product", arguments={"sum", "c"}, return=@Output)
     *
     * @param int $a
     * @param int $b
     * @param int $c
     * @return int
     */
    public function sumThenProduct(int $a, int $b, int $c): int;
}
