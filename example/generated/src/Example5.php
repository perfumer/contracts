<?php

namespace Generated\Barman\Example;

abstract class Example5 extends \Barman\Example\Contract\Example5
{
    final public function mathActions(\Barman\Example\Service\Math $math, int $a, int $b, int $c, int $d, int $e): int
    {
        $sum = null;
        $product = null;
        $quotient = null;
        $difference = null;
        $double = null;
        $_return = null;

        $sum = $math->sum($a, $b);

        $product = $this->product($sum, $c);

        $quotient = parent::quotient($product, $d);

        $difference = $this->math->difference($quotient, $e);

        $double = self::double($difference);

        $_return = \Barman\Example\Service\Math::square($double);

        return $_return;
    }
}
