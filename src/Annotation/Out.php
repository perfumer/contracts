<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Annotation;
use Perfumerlabs\Perfumer\Step\PlainStep;
use Zend\Code\Generator\MethodGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Out extends PlainStep
{
    /**
     * @var string
     */
    public $name;

    public function onCreate(): void
    {
        parent::onCreate();

        $code = 'return $' . $this->name . ';';

        $this->getStepData()->setCode($code);

        $this->mutateTestCaseData();
    }

    protected function mutateTestCaseData(): void
    {
        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$this->getTestCaseKeeper()->getGenerator()->hasMethod($test_method)) {
            $method = new MethodGenerator();
            $method->setFinal(true);
            $method->setVisibility('public');
            $method->setName($test_method);

            $body = '';

            foreach ($this->getReflectionMethod()->getParameters() as $parameter) {
                $body .= '$' . $parameter->getName() . ' = true;';
            }

            $method->setBody($body);

            $this->getTestCaseKeeper()->getGenerator()->addMethodFromGenerator($method);
        } else {
            $method = $this->getTestCaseKeeper()->getGenerator()->getMethod($test_method);
        }

        $body = $method->getBody() . '$this->assertNotEmpty($' . $this->name . ');';
        $method->setBody($body);
    }
}