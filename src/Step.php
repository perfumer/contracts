<?php

namespace Perfumer\Component\Contracts;

abstract class Step implements Annotation
{
    /**
     * @return string
     */
    public function prepend()
    {
        return '';
    }

    /**
     * @return string
     */
    public function append()
    {
        return '';
    }
}