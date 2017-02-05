<?php

namespace Generated\Perfumer\Component\Bdd\Sandbox\Controller;

abstract class SampleController extends \Perfumer\Component\Bdd\Sandbox\ParentController
{
    protected $param4;
    protected $foobar;

    abstract public function sampleValidator($param1);
    abstract public function sampleFormatter($param2);
    abstract protected function sampleCall($param4);
    final private function serviceCall($param1, $param2)
    {
        $this->foobar->buzz($param1, $param2);
    }

    final public function sandboxActionOne($param1, $param2, $param3)
    {
        $_error = null;
        $_return = null;
        $param4 = $this->param4;

        if ($_error === null) {
            $_error = $this->sampleValidator($param1);
        }
        if ($_error === null) {
            $param4 = $this->sampleFormatter($param2);
        }
        if ($_error === null) {
            $_return = $this->sampleCall($param4);
        }

        if ($_error !== null) {
            return $_error;
        }

        return $_return;
    }

    final public function sandboxActionTwo($param1, $param2, $param3)
    {
        $_error = null;
        $_return = null;
        $param4 = $this->param4;

        if ($_error === null) {
            $_return = $this->sampleCall($param4);
        }
        if ($_error === null) {
            $this->serviceCall($param1, $param2);
        }
        if ($_error === null) {
            parent::sandboxActionTwo($param1, $param2);
        }

        if ($_error !== null) {
            return $_error;
        }

        return $_return;
    }

}
