<?php

namespace Perfumer\Contracts;

interface Decorator
{
    /**
     * @param array $annotations
     * @return array
     */
    public function decorate(array $annotations): array;
}
