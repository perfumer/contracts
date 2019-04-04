<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ExpressionStep extends ConditionalStep
{
    /**
     * @var bool
     */
    public $validate = false;

    /**
     * @var string
     */
    protected $_expression;

    /**
     * @var array
     */
    protected $_arguments = [];

    /**
     * @var mixed
     */
    protected $_return;

    /**
     * @return mixed
     */
    public function getReturn()
    {
        return $this->_return;
    }

    /**
     * @param mixed $return
     */
    public function setReturn($return): void
    {
        $this->_return = $return;
    }

    public function getExpression(): ?string
    {
        return $this->_expression;
    }

    public function setExpression(string $expression): void
    {
        $this->_expression = $expression;
    }

    public function getArguments(): array
    {
        return $this->_arguments;
    }

    public function setArguments(array $arguments): void
    {
        $this->_arguments = $arguments;
    }

    public function onCreate(): void
    {
        parent::onCreate();

        $return = is_array($this->_return) ? $this->_return : [$this->_return];

        foreach ($return as $key => $item) {
            $value = $this->validate ? 'true' : 'null';
            $name = is_string($key) ? $key : $item;
            $this->getMethodData()->addInitialVariable($name, $value);
        }

        $step_data = $this->getStepData();

        $return_expression = '';
        $return_expression_after = '';

        if ($this->_return) {
            if (is_array($this->_return)) {
                if ($this->isAssociative($this->_return)) {
                    $return_expression = '$_tmp = ';

                    foreach ($this->_return as $key => $value) {
                        $return_expression_after .= sprintf('$%s = $_tmp[\'%s\'];', $key, $key) . PHP_EOL;
                    }

                    $return_expression_after .= '$_tmp = null;' . PHP_EOL;
                } else {
                    $vars = array_map(function ($v) {
                        return '$' . $v;
                    }, $this->_return);

                    $return_expression = 'list(' . implode(', ', $vars) . ') = ';
                }
            } else {
                $return_expression = '$' . $this->_return . ' = ';
            }
        }

        $code = $return_expression . $this->_expression;

        $arguments_expression = '';

        if ($this->_arguments) {
            $vars = array_map(function ($v) {
                return '$' . $v;
            }, $this->_arguments);

            $arguments_expression = implode(', ', $vars);
        }

        $code = $code . '(' . $arguments_expression . ');';

        if ($this->validate) {
            $code = '$_valid = (bool) ' . $code;
        }

        if ($return_expression_after) {
            $code .= PHP_EOL . $return_expression_after;
        }

        $step_data->setCode($code);
    }

    protected function mutateTestCaseData(): void
    {
        parent::mutateTestCaseData();

        $test_method = 'test' . ucfirst($this->getReflectionMethod()->getName()) . 'LocalVariables';

        $method = $this->getTestCaseData()->getGenerator()->getMethod($test_method);

        foreach ($this->_arguments as $argument) {
            if (is_string($argument)) {
                $body = $method->getBody() . '$this->assertNotEmpty($' . $argument . ');';
                $method->setBody($body);
            }
        }

        $return = is_array($this->_return) ? $this->_return : [$this->_return];

        foreach ($return as $key => $item) {
            $name = is_string($key) ? $key : $item;
            $body = $method->getBody() . '$' . $name . ' = true;';
            $method->setBody($body);
        }
    }

    private function isAssociative(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
