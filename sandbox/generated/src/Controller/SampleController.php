<?php

namespace Perfumer\Component\Bdd\Sandbox\Controller;

class SampleController extends \Generated\Perfumer\Component\Bdd\Sandbox\Controller\SampleController
{
    public function sampleValidator($param1)
    {
        return $param1 ? null : 123;
    }
    
    public function sampleFormatter($param2)
    {
        return $param2 ? null : 123;
    }
    
    protected function sampleCall($param1)
    {
        throw new \Exception('"sampleCall" is not implemented.');
    }

}