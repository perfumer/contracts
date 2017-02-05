<?php

namespace Perfumer\Component\Bdd;

interface StepParserInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function parseForMethod($value);

    /**
     * @param string $value
     * @return string
     */
    public function parseForCall($value);

    /**
     * @param string $value
     * @return string
     */
    public function parseReturn($value);
}
