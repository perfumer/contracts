<?php

namespace Generated\Barman\Example;

abstract class Example7 implements \Barman\Example\Contract\Example7
{
    /**
     * @var \Barman\Example\Context\Math
     */
    private $_context_math = null;

    final public function sum(int $foo, int $bar): int
    {
        $_return = null;

        $_return = $this->getMathContext()->sum($foo, $bar);

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
