<?php

namespace Barman;

interface AutoArguments
{
    /**
     * @return string
     */
    public function getAutoArgumentsClass(): string;

    /**
     * @return string
     */
    public function getAutoArgumentsMethod(): string;
}
