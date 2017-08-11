<?php

namespace Barman\Example\Contract;

use Barman\Annotation\Context;
use Barman\Annotation\Output;

/**
 * Barman can auto-resolve arguments.
 *
 * @Context(name="math", class="\Barman\Example\Context\Math")
 */
interface Example6
{
    /**
     * This method does the same as in Example1.
     *
     * Note, we did not provide arguments to first step, since argument names $a and $b
     * are the same in call and context method "sum".
     *
     * This can be very helpful, if we change the number or the order of arguments in context method.
     * Just regenerate classes and Barman fixes all dependent calls.
     *
     * @Context(name="math", method="sum",                             return="sum")
     * @Context(name="math", method="product", arguments={"sum", "c"}, return=@Output)
     *
     * For now auto-arguments are supported for Context and Injection annotations only.
     *
     * Look at the code, which will be generated by this Contract, in file example/generated/src/Example6.php
     *
     * Go to Example7.
     *
     * @param int $a
     * @param int $b
     * @param int $c
     * @return int
     */
    public function sumThenProduct(int $a, int $b, int $c): int;
}
