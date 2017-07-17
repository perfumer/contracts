<?php

namespace Generated\Perfumer\Contracts\Example;

abstract class Example3 implements \Perfumer\Contracts\Example\Contract\Example3
{
    /**
     * @var \Perfumer\Contracts\Example\Service\Math
     */
    private $_injection_math = null;

    final public function sumThenProduct(int $a, int $b, int $c): int
    {
        $sum = null;
        $_return = null;

        $sum = $this->getMath()->sum($a, $b);

        $_return = $this->getMath()->product($sum, $c);

        return $_return;
    }

    public function __construct(\Perfumer\Contracts\Example\Service\Math $math)
    {
        $this->_injection_math = $math;
    }

    /**
     * @return \Perfumer\Contracts\Example\Service\Math
     */
    final protected function getMath(): \Perfumer\Contracts\Example\Service\Math
    {
        return $this->_injection_math;
    }
}
