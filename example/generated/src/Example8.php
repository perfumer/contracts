<?php

namespace Generated\Barman\Example;

abstract class Example8 implements \Barman\Example\Contract\Example8
{
    /**
     * @var \Barman\Example\Context\Math
     */
    private $_context_math = null;

    /**
     * @var \Barman\Example\Contract\Example8Context
     */
    private $_context_default = null;

    final public function quotient(int $a, int $b): float
    {
        $_valid = true;
        $b_valid = true;
        $_return = null;

        if (true === $_valid) {
            $_valid = (bool) $b_valid = $this->getDefaultContext()->isNonZero($b);
        }

        if (true === $_valid) {
            $_return = $this->getMathContext()->quotient($a, $b);
        }

        if (false === $_valid && !$b_valid) {
            $_return = $this->getDefaultContext()->defaultValue();
        }

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

    /**
     * @return \Barman\Example\Contract\Example8Context
     */
    private function getDefaultContext(): \Barman\Example\Contract\Example8Context
    {
        if (null === $this->_context_default) {
            $this->_context_default = new \Barman\Example\Contract\Example8Context();
        }

        return $this->_context_default;
    }
}
