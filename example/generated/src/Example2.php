<?php

namespace Generated\Perfumer\Contracts\Example;

abstract class Example2 implements \Perfumer\Contracts\Example\Contract\Example2
{
    /**
     * @var \Perfumer\Contracts\Example\Contract\Example2Context
     */
    private $_context_default = null;

    final public function sumThenProduct(int $a, int $b, int $c): int
    {
        $sum = null;
        $_return = null;

        $sum = $this->getDefaultContext()->sum($a, $b);

        $_return = $this->getDefaultContext()->product($sum, $c);

        return $_return;
    }

    /**
     * @return \Perfumer\Contracts\Example\Contract\Example2Context
     */
    final private function getDefaultContext(): \Perfumer\Contracts\Example\Contract\Example2Context
    {
        if ($this->_context_default === null) {
            $this->_context_default = new \Perfumer\Contracts\Example\Contract\Example2Context();
        }

        return $this->_context_default;
    }
}
