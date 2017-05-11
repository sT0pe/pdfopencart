<?php

namespace MultiMerch\Translator;

/**
 * Translator interface.
 */
interface TranslatorInterface
{
    /**
     * Translate a message by key
     *
     * @param  string $key
     * @return string
     */
    public function get($key);
}
