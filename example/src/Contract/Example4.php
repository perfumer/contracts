<?php

namespace Barman\Example\Contract;

use Barman\Annotation\Custom;
use Barman\Annotation\Output;

interface Example4
{
    /**
     * Get sum of $a and $b, then multiply result on $c
     *
     * @Custom(method="sum", arguments={"a", "b"}, return="sum")
     * @Custom(method="product", arguments={"sum", "c"}, return=@Output)
     *
     * @param int $a
     * @param int $b
     * @param int $c
     * @return int
     */
    public function sumThenProduct(int $a, int $b, int $c): int;
}
