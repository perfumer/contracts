<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Step\ConditionalStep;
use Zend\Code\Generator\MethodGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Error extends ConditionalStep
{
    /**
     * @var string
     */
    public $name;

    public function onCreate(): void
    {
        parent::onCreate();

        $code = '$_return = $' . $this->name . ';';

        $this->getStepData()->setValidationCondition(false);
        $this->getStepData()->setCode($code);
        $this->setIsReturning(true);
    }

    protected function mutateTestCaseData(): void
    {
        parent::mutateTestCaseData();

        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$this->getTestCaseData()->getGenerator()->hasMethod($test_method)) {
            $method = new MethodGenerator();
            $method->setFinal(true);
            $method->setVisibility('public');
            $method->setName($test_method);

            $body = '';

            foreach ($this->getReflectionMethod()->getParameters() as $parameter) {
                $body .= '$' . $parameter->getName() . ' = true;';
            }

            $method->setBody($body);

            $this->getTestCaseData()->getGenerator()->addMethodFromGenerator($method);
        } else {
            $method = $this->getTestCaseData()->getGenerator()->getMethod($test_method);
        }

        $body = $method->getBody() . '$this->assertNotEmpty($' . $this->name . ');';
        $method->setBody($body);
    }
}
