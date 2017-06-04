<?php

namespace Generated\Perfumer\Contracts\Example\Controller;

abstract class FooController extends \Perfumer\Contracts\Example\Contract\Controller\FooController
{
    protected $a = null;

    protected $staff = null;

    protected $box = null;

    protected $foobar = null;

    /**
     * @var \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext
     */
    private $_context_default = null;

    /**
     * @var \Iterator
     */
    private $_injection_iterator = null;

    /**
     * @var \Perfumer\Contracts\Example\FooService
     */
    private $_injection_foo = null;

    /**
     * @var string
     */
    private $_injection_some_string = null;

    abstract protected function sumDoubled($a);

    final public function barAction(\Perfumer\Contracts\Annotations\Output $param2, array $param3, $param4 = '12\'3', int $param5 = 140): string
    {
        $_valid = true;
        $a_valid = true;
        $param2_valid = true;
        $double_sum = null;
        $sand = null;
        $_return = null;

        if ($_valid === true) {
            $_valid = (bool) $a_valid = $this->getDefaultContext()->intType($this->a);
        }

        if ($_valid === true && $a_valid) {
            $_valid = (bool) $param2_valid = $this->getDefaultContext()->intType($this->a);
        }

        if ($_valid === true && $a_valid) {
            $this->getFoo()->bar($this->a);
        }

        if ($_valid === true) {
            $this->a = $this->getDefaultContext()->sum($this->a, $this->staff);
        }

        if ($_valid === true && $this->a) {
            $double_sum = $this->sumDoubled($this->a);
        }

        if ($_valid === true) {
            list($sand, $this->box) = parent::sandboxActionTwo($this->a, $this->staff);
        }

        if ($_valid === true) {
            $_return = $this->foobar->baz($this->a, $this->box);
        }

        if ($_valid === false && !$a_valid) {
            $_return = $this->getDefaultContext()->fooErrors();
        }

        if ($_valid === false && !$param2_valid) {
            $_return = $this->getDefaultContext()->fooErrors();
        }

        return $_return;
    }

    final public function bazAction(int $param1, int $param2)
    {
        $param1_valid = null;
        $param2_valid = null;
        $sum = null;
        $sandbox = null;

        $param1_valid = $this->getDefaultContext()->intType($param1);

        if ($param1_valid) {
            $param2_valid = $this->getDefaultContext()->intType($param2);
        }

        $sum = $this->getDefaultContext()->sum($param1, $this->staff);

        $sandbox = parent::sandboxActionTwo($sum, $this->staff);

        $this->foobar->baz($this->getDefaultContext());

        $sandbox->execute();
    }

    /**
     * @return \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext
     */
    final private function getDefaultContext(): \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext
    {
        if ($this->_context_default === null) {
            $this->_context_default = new \Perfumer\Contracts\Example\Contract\Controller\FooControllerContext();
        }

        return $this->_context_default;
    }

    public function __construct(\Iterator $iterator, \Perfumer\Contracts\Example\FooService $foo, string $some_string)
    {
        $this->_injection_iterator = $iterator;
        $this->_injection_foo = $foo;
        $this->_injection_some_string = $some_string;
    }

    /**
     * @return \Iterator
     */
    final protected function getIterator(): \Iterator
    {
        return $this->_injection_iterator;
    }

    /**
     * @return \Perfumer\Contracts\Example\FooService
     */
    final protected function getFoo(): \Perfumer\Contracts\Example\FooService
    {
        return $this->_injection_foo;
    }

    /**
     * @return string
     */
    final protected function getSomeString(): string
    {
        return $this->_injection_some_string;
    }
}
