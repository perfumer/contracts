<?php

namespace Generated\Barman\Example;

abstract class Example2 implements \Barman\Example\Contract\Example2
{
    /**
     * @var \Barman\Example\Contract\Example2Context
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
     * @return \Barman\Example\Contract\Example2Context
     */
    final private function getDefaultContext(): \Barman\Example\Contract\Example2Context
    {
        if ($this->_context_default === null) {
            $this->_context_default = new \Barman\Example\Contract\Example2Context();
        }

        return $this->_context_default;
    }
}
