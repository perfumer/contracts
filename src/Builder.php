<?php

namespace Perfumer\Component\Bdd;

use TwigGenerator\Builder\BaseBuilder;

class Builder extends BaseBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this->twigExtensions[] = 'Perfumer\\Component\\Bdd\\TwigExtension';
        $this->twigFilters[] = 'str_replace';
    }
}