<?php

namespace Perfumerlabs\Perfumer\Step;

use Zend\Code\Generator\MethodGenerator;

abstract class CodeStep extends Step
{
    /**
     * @var string
     */
    private $_code;

    /**
     * @var string
     */
    private $_before_code;

    /**
     * @var string
     */
    private $_after_code;

    /**
     * @var bool
     */
    private $_validating_value = true;

    public function getCode(): ?string
    {
        return $this->_code;
    }

    public function setCode(?string $code): void
    {
        $this->_code = $code;
    }

    public function getBeforeCode(): ?string
    {
        return $this->_before_code;
    }

    public function setBeforeCode(?string $before_code): void
    {
        $this->_before_code = $before_code;
    }

    public function getAfterCode(): ?string
    {
        return $this->_after_code;
    }

    public function setAfterCode(?string $after_code): void
    {
        $this->_after_code = $after_code;
    }

    public function getValidatingValue(): bool
    {
        return $this->_validating_value;
    }

    public function setValidatingValue(bool $validating_value): void
    {
        $this->_validating_value = $validating_value;
    }

    protected function addDeclarationsToBaseTestData(array $vars): void
    {
        $method = $this->addLocalVariablesTestToBaseTestData();

        $body = '';

        foreach ($vars as $var) {
            if (isset($this->redeclare) && $this->redeclare === false) {
                $body .= sprintf('$this->assertArrayNotHasKey(\'%s\', $vars, \'"%s" is already used. If you want to redeclare variable, set "redeclare=true" in the annotation.\');', $var, $var) . PHP_EOL;
            }

            $body .= '$vars[\'' . $var . '\'] = true;' . PHP_EOL;
        }

        $method->setBody($method->getBody() . $body);
    }

    protected function addAssertionsToBaseTestData(array $vars): void
    {
        $method = $this->addLocalVariablesTestToBaseTestData();

        $body = '';

        foreach ($vars as $var) {
            $body .= sprintf('$this->assertArrayHasKey(\'%s\', $vars, \'"%s" is undefined. Possibly, you have mistyped variable name or variable is not initialised yet.\');', $var, $var) . PHP_EOL;
        }

        $method->setBody($method->getBody() . $body);
    }

    protected function addLocalVariablesTestToBaseTestData(): MethodGenerator
    {
        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        if (!$this->getBaseTestData()->getGenerator()->hasMethod($test_method)) {
            $method = new MethodGenerator();
            $method->setFinal(true);
            $method->setVisibility('public');
            $method->setName($test_method);

            $body = '$vars = [];' . PHP_EOL;

            foreach ($this->getReflectionMethod()->getParameters() as $parameter) {
                $body .= '$vars[\'' . $parameter->getName() . '\'] = true;' . PHP_EOL;
            }

            $method->setBody($body);

            $this->getBaseTestData()->getGenerator()->addMethodFromGenerator($method);
        } else {
            $method = $this->getBaseTestData()->getGenerator()->getMethod($test_method);
        }

        return $method;
    }
}
