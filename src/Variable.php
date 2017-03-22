<?php

namespace Perfumer\Component\Contracts;

abstract class Variable implements Annotation
{
    /**
     * @return string
     */
    abstract public function asArgument();

    /**
     * @return string
     */
    abstract public function asHeader();

    /**
     * @return string
     */
    abstract public function asReturn();
}