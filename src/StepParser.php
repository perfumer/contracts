<?php

namespace Perfumer\Component\Contracts;

use Perfumer\Component\Contracts\Annotations\Output;

class StepParser
{
    /**
     * @param string $value
     * @return string
     */
    public function parseHeaderArgument($value)
    {
        if (substr($value, 0, 5) == 'this.') {
            $value = substr($value, 5);
        }

        return '$' . $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public function parseBodyArgument($value)
    {
        if (substr($value, 0, 5) == 'this.') {
            $value = substr($value, 5);

            return '$this->' . $value;
        } else {
            return '$' . $value;
        }
    }

    /**
     * @param string $value
     * @return string
     */
    public function parseReturn($value)
    {
        if (!$value) {
            return '';
        } elseif ($value instanceof Output) {
            return '$_return = ';
        } elseif (is_array($value)) {
            $vars = array_map(function ($v) {
                return '$' . $v;
            }, $value);

            return 'list(' . implode(', ', $vars) . ') = ';
        } elseif (substr($value, 0, 5) == 'this.') {
            $value = substr($value, 5);

            return '$this->' . $value . ' = ';
        } else {
            return '$' . $value . ' = ';
        }
    }

    /**
     * @param string $value
     * @return string
     */
    public function parseServiceName($value)
    {
        return $value;
    }
}
