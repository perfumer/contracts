<?php

namespace Perfumer\Component\Contracts\Example;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Collection extends \Perfumer\Component\Contracts\Collection
{
    public function getBeforeCode(): string
    {
        return '';
    }

    public function getAfterCode(): string
    {
        return '';
    }
}
