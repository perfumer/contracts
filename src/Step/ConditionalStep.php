<?php

namespace Perfumerlabs\Perfumer\Step;

use Zend\Code\Generator\MethodGenerator;

abstract class ConditionalStep extends PlainStep
{
    /**
     * @var string
     */
    public $if;

    /**
     * @var string
     */
    public $unless;

    public function onCreate(): void
    {
        parent::onCreate();

        $step_data = $this->getStepData();
        $step_data->setValidationCondition(true);

        if ($this->if || $this->unless) {
            $condition = $this->if ?: $this->unless;

            $body_argument = '$' . $condition;

            if ($this->unless) {
                $body_argument = '!' . $body_argument;
            }

            $step_data->setExtraCondition($body_argument);
        }

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

        if ($this->if && is_string($this->if)) {
            $body = $method->getBody() . '$this->assertNotEmpty($' . $this->if . ');';
            $method->setBody($body);
        }

        if ($this->unless && is_string($this->unless)) {
            $body = $method->getBody() . '$this->assertNotEmpty($' . $this->unless . ');';
            $method->setBody($body);
        }
    }
}
