<?php

namespace Perfumer\Component\Contracts;

interface Service extends Annotation
{
    /**
     * @return string
     */
    public function getExpression(): string;
}