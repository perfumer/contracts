<?php

namespace Generated\Perfumer\Component\Bdd\Example\Controller;

abstract class FooController extends \Perfumer\Component\Bdd\Example\ParentController
{
    protected $_context_validators;
    protected $staff;
    protected $foobar;

    final private function validatorsIntType($param1)
    {
        if ($this->_context_validators === null) {
            $this->_context_validators = new \Perfumer\Component\Bdd\Example\Context\FooContext();
        }

        return $this->_context_validators->intType($param1);
    }
    final private function validatorsSum($param1, $param2)
    {
        if ($this->_context_validators === null) {
            $this->_context_validators = new \Perfumer\Component\Bdd\Example\Context\FooContext();
        }

        return $this->_context_validators->sum($param1, $param2);
    }

    final public function bar($param1, $param2)
    {
        $_error = null;
        $_return = null;
        $sum = null;
        $sandbox = null;

    // Some new local variables added
    $a = 1;
    $b = 1;
    $c = 1;

        if ($_error === null) {
            $_error = $this->validatorsIntType($param1);
        }
        if ($_error === null) {
            $_error = $this->validatorsIntType($param2);
        }
        if ($_error === null) {
            $sum = $this->validatorsSum($param1, $param2);
        }
        if ($_error === null) {
            $sandbox = parent::sandboxActionTwo($sum, $this->staff);
        }
        if ($_error === null) {
            $_return = $this->foobar->baz($sandbox);
        }

        if ($_error !== null) {
            return $_error;
        }

        return $_return;
    }

    final public function baz($param1, $param2)
    {
        $_error = null;
        $_return = null;
        $sum = null;
        $sandbox = null;

    // Some new local variables added
    $a = 1;
    $b = 1;
    $c = 1;

        if ($_error === null) {
            $_error = $this->validatorsIntType($param1);
        }
        if ($_error === null) {
            $_error = $this->validatorsIntType($param2);
        }
        if ($_error === null) {
            $sum = $this->validatorsSum($param1, $param2);
        }
        if ($_error === null) {
            $sandbox = parent::sandboxActionTwo($sum, $this->staff);
        }
        if ($_error === null) {
            $_return = $this->foobar->baz($sandbox);
        }

        if ($_error !== null) {
            return $_error;
        }

        return $_return;
    }

}
