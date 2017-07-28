<?php

namespace Generated\Barman\Example;

abstract class Example3 implements \Barman\Example\Contract\Example3
{
    /**
     * @var \Barman\Example\Service\Math
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

    public function __construct(\Barman\Example\Service\Math $math)
    {
        $this->_injection_math = $math;
    }

    /**
     * @return \Barman\Example\Service\Math
     */
    final protected function getMath(): \Barman\Example\Service\Math
    {
        return $this->_injection_math;
    }
}
