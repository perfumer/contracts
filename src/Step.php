<?php

namespace Perfumer\Component\Bdd;

abstract class Step implements Annotation
{
    /**
     * @return string
     */
    public function prependCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function appendCode()
    {
        return '';
    }
}