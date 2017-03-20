<?php

namespace Generated\Perfumer\Component\Contracts\Example\Controller;

abstract class FooController extends \Perfumer\Component\Contracts\Example\ParentController
{
    protected $_context_validators;
    protected $sum;
    protected $staff;
    protected $foobar;

    abstract protected function sumDoubled($sum);

    final public function bar($param1, $param2)
    {
        $_valid = true;
        $_return = null;
        $param1_valid = true;
        $param2_valid = true;
        $double_sum = null;
        $sand = null;
        $box = null;


        if ($_valid === true) {
            $_valid = $param1_valid = $this->validatorsIntType($param1);
        }
        if ($_valid === true && $param1_valid) {
            $_valid = $param2_valid = $this->validatorsIntType($param2);
        }
        if ($_valid === true) {
            $this->sum = $this->validatorsSum($param1, $param2);
        }
        if ($_valid === true) {
            $double_sum = $this->sumDoubled($this->sum);
        }
        if ($_valid === true) {
            list($sand, $box) = parent::sandboxActionTwo($this->sum, $this->staff);
        }
        if ($_valid === true) {
            $_return = $this->foobar->baz($sand, $box);
        }

        return $_return;
    }

    final public function baz($param1, $param2)
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
            $sum = $this->validatorsSum($param1, $param2);
        }
        if ($_valid === true) {
            $sandbox = parent::sandboxActionTwo($sum, $this->staff);
        }
        if ($_valid === true) {
            $_return = $this->foobar->baz($this->getValidatorsContext());
        }
        if ($_valid === false) {
            $_return = $this->validatorsFooErrors($param1_valid, $param2_valid);
        }

        return $_return;
    }

    final private function validatorsIntType($param1)
    {
        return $this->getValidatorsContext()->intType($param1);
    }

    final private function validatorsSum($param1, $param2)
    {
        return $this->getValidatorsContext()->sum($param1, $param2);
    }

    final private function validatorsFooErrors($param1_valid, $param2_valid)
    {
        return $this->getValidatorsContext()->fooErrors($param1_valid, $param2_valid);
    }

    final private function getValidatorsContext(): \Perfumer\Component\Contracts\Example\Context\FooContext
    {
        if ($this->_context_validators === null) {
            $this->_context_validators = new \Perfumer\Component\Contracts\Example\Context\FooContext();
        }

        return $this->_context_validators;
    }

}
