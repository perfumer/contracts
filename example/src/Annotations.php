<?php

namespace Perfumer\Contracts\Example;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Collection extends \Perfumer\Contracts\Collection
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
