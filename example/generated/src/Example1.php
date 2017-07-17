<?php

namespace Generated\Perfumer\Contracts\Example;

abstract class Example1 implements \Perfumer\Contracts\Example\Contract\Example1
{
    /**
     * @var \Perfumer\Contracts\Example\Context\Math
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
     * @return \Perfumer\Contracts\Example\Context\Math
     */
    final private function getMathContext(): \Perfumer\Contracts\Example\Context\Math
    {
        if ($this->_context_math === null) {
            $this->_context_math = new \Perfumer\Contracts\Example\Context\Math();
        }

        return $this->_context_math;
    }
}
