<?php

namespace MultiMerch\Translator;

use Registry;

class Translator implements TranslatorInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    public function setRegistry(Registry $registry)
    {
        $this->registry = $registry;
        return $this;
    }

    public function getRegistry()
    {
        return $this->registry;
    }

    public function get($key)
    {
        return $this->getRegistry()->get('language')->get($key);
    }
}