<?php

namespace Perfumer\Contracts;

interface Decorator
{
    /**
     * @param array $annotations
     */
    public function decorate(array &$annotations): void;
}
