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

        if ($_valid === true) {
            $_valid = (bool) $b_valid = $this->getDefaultContext()->isNonZero($b);
        }

        if ($_valid === true) {
            $_return = $this->getMathContext()->quotient($a, $b);
        }

        if ($_valid === false && !$b_valid) {
            $_return = $this->getDefaultContext()->defaultValue();
        }

        return $_return;
    }

    /**
     * @return \Barman\Example\Context\Math
     */
    final private function getMathContext(): \Barman\Example\Context\Math
    {
        if ($this->_context_math === null) {
            $this->_context_math = new \Barman\Example\Context\Math();
        }

        return $this->_context_math;
    }

    /**
     * @return \Barman\Example\Contract\Example8Context
     */
    final private function getDefaultContext(): \Barman\Example\Contract\Example8Context
    {
        if ($this->_context_default === null) {
            $this->_context_default = new \Barman\Example\Contract\Example8Context();
        }

        return $this->_context_default;
    }
}
