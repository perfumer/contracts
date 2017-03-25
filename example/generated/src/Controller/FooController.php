<?php

namespace Generated\Perfumer\Component\Contracts\Example\Controller;

abstract class FooController extends \Perfumer\Component\Contracts\Example\ParentController implements \Perfumer\Component\Contracts\Example\Contract\Controller\FooController
{
    protected $_context_validators;
    protected $sum;
    protected $box;
    protected $staff;
    protected $foobar;

    abstract protected function sumDoubled($sum);

    final public function bar(int $param1, \Perfumer\Component\Contracts\Annotations\Output $param2): string
    {
        $_valid = true;
        $_return = null;
        $param1_valid = true;
        $param2_valid = true;
        $double_sum = null;
        $sand = null;


        if ($_valid === true) {
            $_valid = $param1_valid = $this->validatorsIntType($param1);
        }
        if ($_valid === true && $param1_valid) {
            $_valid = $param2_valid = $this->validatorsIntType($param2);
        }
        if ($_valid === true) {
            $this->sum = $this->validatorsSum($param1, $this->box);
        }
        if ($_valid === true) {
            $double_sum = $this->sumDoubled($this->sum);
        }
        if ($_valid === true) {
            list($sand, $this->box) = parent::sandboxActionTwo($this->sum, $this->staff);
        }
        if ($_valid === true) {
            $_return = $this->foobar->baz($sand, $this->box);
        }

        return $_return;
    }

    final public function baz(int $param1, int $param2): \DateTime
    {
        $_valid = true;
        $_return = null;
        $param1_valid = true;
        $param2_valid = true;
        $sum = null;
        $sandbox = null;


        if ($_valid === true) {
            $_valid = $param1_valid = $this->validatorsIntType($param1);
        }
        if ($_valid === true) {
            $_valid = $param2_valid = $this->validatorsIntType($param2);
        }
        if ($_valid === true) {
            $sum = $this->validatorsSum($param1, $this->box);
        }
        if ($_valid === true) {
            $sandbox = parent::sandboxActionTwo($sum, $this->staff);
        }
        if ($_valid === true) {
            $_return = $this->foobar->baz($this->getValidatorsContext());
        }
        if ($_valid === false && !$param1_valid) {
            $_return = $this->validatorsFooErrors();
        }

        return $_return;
    }

    final private function validatorsIntType($param1)
    {
        return $this->getValidatorsContext()->intType($param1);
    }

    final private function validatorsSum($param1, $box)
    {
        return $this->getValidatorsContext()->sum($param1, $box);
    }

    final private function validatorsFooErrors()
    {
        return $this->getValidatorsContext()->fooErrors();
    }

    final private function getValidatorsContext(): \Perfumer\Component\Contracts\Example\Context\FooContext
    {
        if ($this->_context_validators === null) {
            $this->_context_validators = new \Perfumer\Component\Contracts\Example\Context\FooContext();
        }

        return $this->_context_validators;
    }

}
