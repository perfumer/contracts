<?php

namespace Perfumer\Component\Bdd;

class StepParser
{
    /**
     * @param string $value
     * @return string
     */
    public function parseForMethod($value)
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
    public function parseForCall($value)
    {
        if (substr($value, 0, 5) == 'this.') {
            $value = substr($value, 5);

            return '$this->' . $value;
        } else {
            return '$' . $value;
        }
    }
}
