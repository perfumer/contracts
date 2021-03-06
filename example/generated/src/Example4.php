<?php

namespace Generated\Barman\Example;

abstract class Example4 implements \Barman\Example\Contract\Example4
{
    abstract protected function sum($a, $b);

    abstract protected function product($sum, $c);

    final public function sumThenProduct(int $a, int $b, int $c): int
    {
        $sum = null;
        $_return = null;

        $sum = $this->sum($a, $b);

        $_return = $this->product($sum, $c);

        return $_return;
    }
}
