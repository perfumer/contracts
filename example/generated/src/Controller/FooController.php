<?php

namespace Generated\Perfumer\Component\Contracts\Example\Controller;

abstract class FooController extends \Perfumer\Component\Contracts\Example\ParentController implements \Perfumer\Component\Contracts\Example\Contract\Controller\FooController
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @var \DateTime
     */
    protected $date;

    public function __construct(\Iterator $iterator, \DateTime $date)
    {
        $this->iterator = $iterator;
        $this->date = $date;
    }

    protected $sum;
    protected $_context_validators;
    protected $box;
    protected $staff;
    protected $foobar;

    abstract protected function sumDoubled($sum);

    final public function barAction(int $param1, \Perfumer\Component\Contracts\Annotations\Output $param2): string
    {
        $_valid = true;
        $_return = null;
        $param1_valid = true;
        $param2_valid = true;
        $double_sum = null;
        $sand = null;


        if ($_valid === true) {
            $this->date->format($this->sum);
        }
        if ($_valid === true) {
            $_valid = $param1_valid = $this->getValidatorsContext()->intType($param1);
        }
        if ($_valid === true && $param1_valid) {
            $_valid = $param2_valid = $this->getValidatorsContext()->intType($param2);
        }
        if ($_valid === true) {
            $this->sum = $this->getValidatorsContext()->sum($param1, $this->box);
        }
        if ($_valid === true) {
            $double_sum = $this->sumDoubled($this->sum);
        }
        if ($_valid === true) {
            list($sand, $this->box) = parent::sandboxActionTwo($this->sum, $this->staff);
        }
        if ($_valid === true) {
            $_return = $this->foobar->baz($this->date, $this->box);
        }
        if ($_valid === false && !$param1_valid) {
            $_return = $this->getValidatorsContext()->fooErrors();
        }
        if ($_valid === false && !$param2_valid) {
            $_return = $this->getValidatorsContext()->fooErrors();
        }

        return $_return;
    }

    final public function bazAction(int $param1, int $param2): \DateTime
    {
        $_valid = true;
        $_return = null;
        $param1_valid = true;
        $param2_valid = true;
        $sum = null;
        $sandbox = null;


        if ($_valid === true) {
            $_valid = $param1_valid = $this->getValidatorsContext()->intType($param1);
        }
        if ($_valid === true) {
            $_valid = $param2_valid = $this->getValidatorsContext()->intType($param2);
        }
        if ($_valid === true) {
            $sum = $this->getValidatorsContext()->sum($param1, $this->box);
        }
        if ($_valid === true) {
            $sandbox = parent::sandboxActionTwo($sum, $this->staff);
        }
        if ($_valid === true) {
            $_return = $this->foobar->baz($this->getValidatorsContext());
        }
        if ($_valid === false && !$param1_valid) {
            $_return = $this->getValidatorsContext()->fooErrors();
        }
        if ($_valid === false && !$param2_valid) {
            $_return = $this->getValidatorsContext()->fooErrors();
        }

        return $_return;
    }

    final private function getValidatorsContext(): \Perfumer\Component\Contracts\Example\Context\FooContext
    {
        if ($this->_context_validators === null) {
            $this->_context_validators = new \Perfumer\Component\Contracts\Example\Context\FooContext();
        }

        return $this->_context_validators;
    }

}
