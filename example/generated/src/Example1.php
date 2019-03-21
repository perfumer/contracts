<?php

namespace Generated\Barman\Example;

abstract class Example1 implements \Barman\Example\Contract\Example1
{
    /**
     * @var Barman\Example\Contract\Example1Context
     */
    private $_context_BarmanExampleContractExample1Context = null;

    final public function sumThenProduct(int $c): int
    {
        $_valid = true;
        $sum = true;
        $rrr = null;

        $a = $c;

        $b = $this->foobar;

        if (true === $_valid) {
            $_valid = (bool) $sum = $this->getBarmanExampleContractExample1ContextContext()->sum($a, $b);
        }

        if (true === $_valid) {
            $rrr = $this->getBarmanExampleContractExample1ContextContext()->product($sum, $c);
        }

        if (false === $_valid && !$sum) {
            return $b;
        }

        if (true === $_valid) {
            return $rrr;
        }
    }

    /**
     * @return Barman\Example\Contract\Example1Context
     */
    private function getBarmanExampleContractExample1ContextContext(): \Barman\Example\Contract\Example1Context
    {
        if (null === $this->_context_BarmanExampleContractExample1Context) {
            $this->_context_BarmanExampleContractExample1Context = new Barman\Example\Contract\Example1Context();
        }

        return $this->_context_BarmanExampleContractExample1Context;
    }
}
