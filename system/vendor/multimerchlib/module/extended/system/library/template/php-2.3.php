<?php

namespace Template;

class Php extends OC_php
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