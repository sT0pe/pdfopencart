<?php

namespace Template;

class Basic extends OC_Basic
{
    public function __get($key)
    {
        return \MsLoader::getInstance()->getRegistry()->get($key);
    }

    public function __set($key, $value)
    {
        return \MsLoader::getInstance()->getRegistry()->set($key, $value);
    }
}