<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class ExpressionStep extends ConditionalStep
{
    /**
     * @var string
     */
    public $expression;

    /**
     * @var array
     */
    public $arguments = [];

    /**
     * @var mixed
     */
    public $return;

    /**
     * @var bool
     */
    public $validate = false;

    public function onCreate(): void
    {
        parent::onCreate();

        $return = is_array($this->return) ? $this->return : [$this->return];

        foreach ($return as $key => $item) {
            $value = $this->validate ? 'true' : 'null';
            $name = is_string($key) ? $key : $item;
            $this->getMethodData()->addInitialVariable($name, $value);
        }

        $step_data = $this->getStepData();

        $return_expression = '';
        $return_expression_after = '';

        if ($this->return) {
            if (is_array($this->return)) {
                if ($this->isAssociative($this->return)) {
                    $return_expression = '$_tmp = ';

                    foreach ($this->return as $key => $value) {
                        $return_expression_after .= sprintf('$%s = $_tmp[\'%s\'];', $key, $key) . PHP_EOL;
                    }

                    $return_expression_after .= '$_tmp = null;' . PHP_EOL;
                } else {
                    $vars = array_map(function ($v) {
                        return '$' . $v;
                    }, $this->return);

                    $return_expression = 'list(' . implode(', ', $vars) . ') = ';
                }
            } else {
                $return_expression = '$' . $this->return . ' = ';
            }
        }

        $code = $return_expression . $this->expression;

        $arguments_expression = '';

        if ($this->arguments) {
            $vars = array_map(function ($v) {
                return '$' . $v;
            }, $this->arguments);

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

        foreach ($this->arguments as $argument) {
            if (is_string($argument)) {
                $body = $method->getBody() . '$this->assertNotEmpty($' . $argument . ');';
                $method->setBody($body);
            }
        }

        $return = is_array($this->return) ? $this->return : [$this->return];

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
