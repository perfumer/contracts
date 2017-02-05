<?php

namespace Tests\Perfumer\Component\Bdd\Sandbox\Controller;

use Perfumer\Component\Bdd\Sandbox\Controller\SampleController;

class SampleControllerTest extends \Generated\Tests\Perfumer\Component\Bdd\Sandbox\Controller\SampleControllerTest
{
    public function getClassInstance()
    {
        return new SampleController();
    }

    /**
     * @return array
     */
    public function sampleValidatorDataProvider()
    {
        return [
            [1, null]
        ];
    }

    /**
     * @return array
     */
    public function sampleFormatterDataProvider()
    {
        return [
            [1, null]
        ];
    }

}