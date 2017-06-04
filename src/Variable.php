<?php

namespace Perfumer\Contracts;

interface Variable
{
    /**
     * @return string
     */
    public function asArgument(): string;

    /**
     * @return string
     */
    public function asHeader(): string;

    /**
     * @return string
     */
    public function asReturn(): string;
}
