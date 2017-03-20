<?php

namespace Perfumer\Component\Contracts;

use Perfumer\Component\Contracts\Annotations\Output;
use Perfumer\Component\Contracts\Annotations\Property;

class StepParser
{
    /**
     * @param string $value
     * @return string
     */
    public function parseHeaderArgument($value)
    {
        return '$' . ($value instanceof Property ? $value->name : $value);
    }

    /**
     * @param string $value
     * @return string
     */
    public function parseBodyArgument($value)
    {
        return $value instanceof Property ? '$this->' . $value->name : '$' . $value;
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
        } elseif ($value instanceof Property) {
            return '$this->' . $value->name . ' = ';
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
