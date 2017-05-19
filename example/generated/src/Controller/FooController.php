<?php

namespace Generated\Perfumer\Component\Contracts\Example\Controller;

abstract class FooController extends \Perfumer\Component\Contracts\Example\ParentController implements \Perfumer\Component\Contracts\Example\Contract\Controller\FooController
{
    protected $a;

    protected $staff;

    protected $box;

    protected $foobar;

    /**
     * @var \Iterator
     */
    private $_injected_iterator;

    /**
     * @var \Perfumer\Component\Contracts\Example\FooService
     */
    private $_injected_foo;

    /**
     * @var string
     */
    private $_injected_some_string;

    /**
     * @var \Perfumer\Component\Contracts\Example\Contract\Controller\FooControllerContext
     */
    private $_context_default;

    public function __construct(\Iterator $iterator, \Perfumer\Component\Contracts\Example\FooService $foo, string $some_string)
    {
        $this->_injected_iterator = $iterator;
        $this->_injected_foo = $foo;
        $this->_injected_some_string = $some_string;
    }

    abstract protected function sumDoubled($a);

    final public function barAction(\Perfumer\Component\Contracts\Annotations\Output $param2): string
    {
        $_valid = true;
        $a_valid = true;
        $param2_valid = true;
        $_return = null;
        $double_sum = null;
        $sand = null;

        if ($_valid === true) {
            $_valid = (bool) $a_valid = $this->getDefaultContext()->intType($this->a);
        }

        if ($_valid === true && $a_valid) {
            $_valid = (bool) $param2_valid = $this->getDefaultContext()->intType($this->a);
        }

        if ($_valid === true && $a_valid) {
            $this->getFoo()->bar($a);
        }

        if ($_valid === true) {
            $this->a = $this->getDefaultContext()->sum($a, $this->staff);
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
     * @return \Iterator
     */
    final protected function getIterator(): \Iterator
    {
        return $this->_injected_iterator;
    }

    /**
     * @return \Perfumer\Component\Contracts\Example\FooService
     */
    final protected function getFoo(): \Perfumer\Component\Contracts\Example\FooService
    {
        return $this->_injected_foo;
    }

    /**
     * @return string
     */
    final protected function getSomeString(): string
    {
        return $this->_injected_some_string;
    }

    /**
     * @return \Perfumer\Component\Contracts\Example\Contract\Controller\FooControllerContext
     */
    final private function getDefaultContext(): \Perfumer\Component\Contracts\Example\Contract\Controller\FooControllerContext
    {
        if ($this->_context_default === null) {
            $this->_context_default = new \Perfumer\Component\Contracts\Example\Contract\Controller\FooControllerContext();
        }

        return $this->_context_default;
    }

}
