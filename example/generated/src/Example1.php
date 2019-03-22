<?php

namespace Generated\Barman\Example;

abstract class Example1 implements \Barman\Example\Contract\Example1
{
    /**
     * @var \Barman\Example\Contract\Example1Context
     */
    private $_shared_BarmanExampleContractExample1Context = null;

    /**
     * @var \Barman\Example\Service\Math
     */
    private $_inject_lalala = null;

    final public function sumThenProduct(int $c): int
    {
        $_valid = true;
        $sum = true;
        $rrr = null;

        $a = $c;

        $b = $this->foobar;

        $a = $this->barbaz;

        if (true === $_valid) {
            $_valid = (bool) $sum = $this->getBarmanExampleContractExample1ContextContext()->sum($a, $b);
        }

        if (true === $_valid) {
            $rrr = $this->getBarmanExampleContractExample1ContextContext()->product($a, $c);
        }

        if (false === $_valid && !$sum) {
            return $b;
        }

        if (true === $_valid) {
            return $rrr;
        }
    }

    /**
     * @return \Barman\Example\Contract\Example1Context
     */
    private function getBarmanExampleContractExample1ContextContext(): \Barman\Example\Contract\Example1Context
    {
        if (null === $this->_shared_BarmanExampleContractExample1Context) {
            $this->_shared_BarmanExampleContractExample1Context = new \Barman\Example\Contract\Example1Context();
        }

        return $this->_shared_BarmanExampleContractExample1Context;
    }

    public function __construct(\Barman\Example\Service\Math $lalala)
    {
        $this->_inject_lalala = $lalala;
    }

    /**
     * @return \Barman\Example\Service\Math
     */
    final protected function getLalala(): \Barman\Example\Service\Math
    {
        return $this->_inject_lalala;
    }
}
