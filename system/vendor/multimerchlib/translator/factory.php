<?php

namespace MultiMerch\Translator;

use Registry;

abstract class Factory
{
    public static function create(Registry $registry)
    {
        $translator = new Translator();
        $translator->setRegistry($registry);
        return $translator;
    }
}
