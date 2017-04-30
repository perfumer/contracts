<?php

namespace Generated\Perfumer\Component\Contracts\Example\Controller;

abstract class FooController extends \Perfumer\Component\Contracts\Example\ParentController implements \Perfumer\Component\Contracts\Example\Contract\Controller\FooController
{
    protected $sum;

    protected $box;

    protected $staff;

    protected $foobar;

    /**
     * @var \Perfumer\Component\Contracts\Example\Context\FooContext
     */
    private $_context_validators_lib;

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

    public function __construct(\Iterator $iterator, \Perfumer\Component\Contracts\Example\FooService $foo, string $some_string)
    {
        $this->_injected_iterator = $iterator;
        $this->_injected_foo = $foo;
        $this->_injected_some_string = $some_string;
    }

    abstract protected function sumDoubled($sum);

    final public function barAction(int $a, \Perfumer\Component\Contracts\Annotations\Output $param2): string
    {
        $_valid = true;
        $_return = null;
        $a_valid = true;
        $param2_valid = true;
        $double_sum = null;
        $sand = null;

        if ($_valid === true) {
            $_valid = (bool) $a_valid = $this->getValidatorsLibContext()->intType($a);
        }

        if ($_valid === true && $a_valid) {
            $_valid = (bool) $param2_valid = $this->getValidatorsLibContext()->intType($a);
        }

        if ($_valid === true && $a_valid) {
            $this->_injected_foo->bar($a);
        }

        if ($_valid === true) {
            $this->sum = $this->getValidatorsLibContext()->sum($a, $this->box);
        }

        if ($_valid === true) {
            $double_sum = $this->sumDoubled($this->sum);
        }

        if ($_valid === true) {
            list($sand, $this->box) = parent::sandboxActionTwo($this->sum, $this->staff);
        }

        if ($_valid === true) {
            $_return = $this->foobar->baz($this->sum, $this->box);
        }

        if ($_valid === false && !$a_valid) {
            $_return = $this->getValidatorsLibContext()->fooErrors();
        }

        if ($_valid === false && !$param2_valid) {
            $_return = $this->getValidatorsLibContext()->fooErrors();
        }

        return $_return;
    }

    final public function bazAction(int $param1, int $param2)
    {
        $param1_valid = null;
        $param2_valid = null;
        $sum = null;
        $sandbox = null;

        $param1_valid = $this->getValidatorsLibContext()->intType($param1);

        if ($param1_valid) {
            $param2_valid = $this->getValidatorsLibContext()->intType($param2);
        }

        $sum = $this->getValidatorsLibContext()->sum($param1, $this->box);

        $sandbox = parent::sandboxActionTwo($sum, $this->staff);

        $this->foobar->baz($this->getValidatorsLibContext());

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
     * @return \Perfumer\Component\Contracts\Example\Context\FooContext
     */
    final private function getValidatorsLibContext(): \Perfumer\Component\Contracts\Example\Context\FooContext
    {
        if ($this->_context_validators_lib === null) {
            $this->_context_validators_lib = new \Perfumer\Component\Contracts\Example\Context\FooContext();
        }

        return $this->_context_validators_lib;
    }

}
