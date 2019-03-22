<?php

namespace Perfumerlabs\Perfumer\Annotation;

use Perfumerlabs\Perfumer\Annotation;
use Perfumerlabs\Perfumer\Step\PlainStep;
use Zend\Code\Generator\MethodGenerator;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Set extends PlainStep
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    public function onCreate(): void
    {
        parent::onCreate();

        $code = '$' . $this->name . ' = $' . $this->value . ';';

        $this->getStepData()->setCode($code);
        $this->getStepData()->setIsValidating(false);

        $this->mutateTestCaseData();
    }

    protected function mutateTestCaseData(): void
    {
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

        $body = $method->getBody() . '$' . $this->name . ' = true;';
        $method->setBody($body);
    }
}
