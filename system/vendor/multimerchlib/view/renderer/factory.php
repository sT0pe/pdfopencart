<?php

namespace MultiMerch\View\Renderer;

use Registry;

abstract class Factory
{
    public static function create(Registry $registry)
    {
        $renderer = new PhpRenderer();
        return $renderer;
    }
}
