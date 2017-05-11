<?php
class oc_language {
	protected $default = 'english';
	protected $directory;
	protected $data = array();

	public function __construct($directory = '') {
		$this->directory = $directory;
	}

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}

	public function all() {
		return $this->data;
	}
	
	public function load($filename) {
		$_ = array();

		$file = DIR_LANGUAGE . $this->default . '/' . $filename . '.php';

		if (file_exists($file)) {
			require(\VQMod::modCheck($file));
		}

		$file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

		if (file_exists($file)) {
			require(\VQMod::modCheck($file));
		}

		$this->data = array_merge($this->data, $_);

		return $this->data;
	}
}