<?php

namespace Perfumer\Contracts;

class TwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ucwords', [$this, 'ucwords']),
        );
    }

    /**
     * @param string $text
     * @return string
     */
    public function ucwords(string $text): string
    {
        return str_replace('_', '', ucwords($text, '_'));
    }
}
