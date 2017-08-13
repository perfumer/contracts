<?php

namespace Barman\Example\Contract;

use Barman\Annotation\Context;
use Barman\Annotation\Error;
use Barman\Annotation\Output;

/**
 * Barman executes method step-by-step. Assume, we want to
 * break execution when some validation fails, get some default value and return it
 * by method.
 *
 * @Context(name="math", class="\Barman\Example\Context\Math")
 */
interface Example8
{
    /**
     * This method checks if $b is a non-zero value, and then returns the result
     * of expression "$a divide to $b". If $b equals to 0, then return null.
     *
     * Call "isNonZero" method of default context and save the result to $b_valid variable.
     * @Context(method="isNonZero", return="b_valid")
     *
     * Get the quotient of $a and $b and provide the result to return statement.
     * @Context(name="math", method="quotient", return=@Output())
     *
     * Error Annotation automatically adds some validation boilerplate code to whole method.
     * If $b_valid equals to false in step it is initialised, then any other steps, except Errors,
     * will not be executed.
     *
     * If $b_valid is false, then call "defaultValue" method of default context, and provide the result
     * to return statement.
     * @Error(method="defaultValue", unless="b_valid")
     *
     * Look at the code, which will be generated by this Contract, in file example/generated/src/Example8.php
     *
     * @param int $a
     * @param int $b
     * @return float|int
     */
    public function quotient(int $a, int $b): ?float;
}

class Example8Context
{
    /**
     * @param $b
     * @return bool
     */
    public function isNonZero($b): bool
    {
        return $b !== 0;
    }

    /**
     * @return null
     */
    public function defaultValue()
    {
        return null;
    }
}