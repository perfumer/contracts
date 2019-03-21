<?php

namespace Generated\Barman\Example;

abstract class Example6 implements \Barman\Example\Contract\Example6
{
    /**
     * @var \Barman\Example\Context\Math
     */
    private $_context_math = null;

    final public function sumThenProduct(int $a, int $b, int $c): int
    {
        $sum = null;
        $_return = null;

        $sum = $this->getMathContext()->sum($a, $b);

        $_return = $this->getMathContext()->product($sum, $c);

        return $_return;
    }

    /**
     * @return \Barman\Example\Context\Math
     */
    private function getMathContext(): \Barman\Example\Context\Math
    {
        if (null === $this->_context_math) {
            $this->_context_math = new \Barman\Example\Context\Math();
        }

        return $this->_context_math;
    }
}
