<?php
namespace Template;
class oc_php
{
	private $data = array();

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function render($template) {
		$file = DIR_TEMPLATE . $template;

		if (is_file($file)) {
			extract($this->data);

			ob_start();

			require(\VQMod::modCheck(modification($file), $file));

			return ob_get_clean();
		}

		trigger_error('Error: Could not load template ' . $file . '!');
		exit();
	}
}