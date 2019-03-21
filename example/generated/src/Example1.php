<?php

namespace Generated\Barman\Example;

abstract class Example1 implements \Barman\Example\Contract\Example1
{
    final public function sumThenProduct(int $a, int $b, int $c): int
    {
        $_valid = true;
        $sum = true;
        $res = null;
        $rrr = null;

        if (true === $_valid) {
            $_valid = (bool) $sum = asd($a, $b);
        }

        if (true === $_valid) {
            list($res, $rrr) = zxc($sum, $c);
        }

        if (false === $_valid && !$sum) {
            return $b;
        }

        if (true === $_valid) {
            return $rrr;
        }
    }
}
