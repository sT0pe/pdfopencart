<?php

class Language extends OC_Language
{
	public function load($filename)
	{
		$_ = array();

		// check en-gb directory first
		$file = DIR_LANGUAGE . 'en-gb/' . $filename . '.php';
		if (file_exists($file)) {
			require(\VQMod::modCheck($file));
			$this->data = array_merge($this->data, $_);
			return $this->data;
		} else {
			return parent::load($filename);
		}
	}
}