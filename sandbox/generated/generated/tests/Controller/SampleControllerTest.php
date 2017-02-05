<?php

namespace Generated\Tests\Perfumer\Component\Bdd\Sandbox\Controller;

abstract class SampleControllerTest extends \PHPUnit_Framework_TestCase
{
    abstract public function getClassInstance();

    abstract public function sampleValidatorDataProvider();

    /**
     * @dataProvider sampleValidatorDataProvider
     */
    final public function test_sampleValidator($param1, $result)
    {
        $_class_instance = $this->getClassInstance();

        $this->assertEquals($_class_instance->sampleValidator($param1), $result);
    }

    abstract public function sampleFormatterDataProvider();

    /**
     * @dataProvider sampleFormatterDataProvider
     */
    final public function test_sampleFormatter($param2, $result)
    {
        $_class_instance = $this->getClassInstance();

        $this->assertEquals($_class_instance->sampleFormatter($param2), $result);
    }

}
