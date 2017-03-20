<?php

namespace Perfumer\Component\Contracts;

interface Variable extends Annotation
{
    /**
     * @return string
     */
    public function asArg();

    /**
     * @return string
     */
    public function asHeader();

    /**
     * @return string
     */
    public function asReturn();
}