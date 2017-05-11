<?php

namespace MultiMerch\Stdlib;

abstract class CName
{
    /**
     * Lookup for canonicalized names.
     *
     * @var array
     */
    protected static $canonicalNames = array();

    /**
     * @var array map of characters to be replaced through strtr
     */
    protected static $canonicalNamesReplacements = array('-' => '', '_' => '', ' ' => '', '\\' => '', '/' => '');

    /**
     * Canonicalize name
     *
     * @param  string $name
     * @return string
     */
    public static function canonicalizeName($name)
    {
        if (isset(self::$canonicalNames[$name])) {
            return self::$canonicalNames[$name];
        }

        // this is just for performance instead of using str_replace
        return self::$canonicalNames[$name] = strtolower(strtr($name, self::$canonicalNamesReplacements));
    }
}