<?php

class ControllerMultimerchDebug extends ControllerMultimerchBase {
	private function _readLog($file, $lines = 50, $adaptive = true) {
		$f = @fopen($file, "rb");
		if ($f === false) return false;

		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

		fseek($f, -1, SEEK_END);
		if (fread($f, 1) != "\n") $lines -= 1;
		$output = '';
		$chunk = '';
		while (ftell($f) > 0 && $lines >= 0) {
			$seek = min(ftell($f), $buffer);
			fseek($f, -$seek, SEEK_CUR);
			$output = ($chunk = fread($f, $seek)) . $output;
			fseek($f, -utf8_strlen($chunk, '8bit'), SEEK_CUR);
			$lines -= substr_count($chunk, "\n");
		}

		while ($lines++ < 0) $output = substr($output, strpos($output, "\n") + 1);
		fclose($f);

		return trim($output);
	}

	public function index() {
		$this->validate(__FUNCTION__);

		$this->load->model('extension/extension');
		$this->load->model('extension/module');

		// extensions
		$this->data['extensions'] = array();
		$this->data['installed_extensions'] = array();
		$this->data['other_extensions'] = array();
		$installed = $this->model_extension_extension->getInstalled('module');

		$files = glob(DIR_APPLICATION . 'controller/module/*.php');
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				$this->load->language('module/' . $extension);

				$version = '';
				if (strpos($extension, 'multimerch_') !== FALSE) {
					$f = file($file);
					foreach ($f as $line) {
						if (strpos($line, 'version') !== false) {
							if (preg_match("/['\"](.*?)['\"]/", $line, $matches)) $version = $matches[1];
							break;
						}
					}
				}

				if (in_array($extension, $installed)) {
					$this->data['installed_extensions'][] = array(
						'name' => $this->language->get('heading_title'),
						'version' => $version
					);
				}else {
					$this->data['other_extensions'][] = array(
						'name' => $this->language->get('heading_title'),
						'version' => $version
					);
				}
			}
		}

		// error log
		$file = DIR_LOGS . $this->config->get('config_error_filename');
		$this->data['error_log'] = '';
		if (file_exists($file)) {
			$this->data['error_log'] = $this->_readLog($file);
		}

		// vqmod log
		$files = glob(DIR_APPLICATION . '../' . vQmod::$logFolder . '*.log');
		if ($files) {
			usort($files, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
			$log = array_shift($files);
			$this->data['vqmod_log'] = $this->_readLog($log, 150);
		} else {
			$this->data['vqmod_log'] = '';
		}

		//server log
		$this->data['server_log'] = '';
		$serverlog = @ini_get('error_log');
		if($serverlog) {
			$l = $this->_readLog($serverlog, 50);
			if ($l !== FALSE) {
				$this->data['server_log'] = $l;
			} else {
				$this->data['server_log'] = "Server log unavailable ($serverlog)";
			}
		} else {
			$this->data['server_log'] = 'Server log unavailable';
		}

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_debug_heading');
		$this->document->setTitle($this->language->get('ms_debug_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_debug_breadcrumbs'),
				'href' => $this->url->link('multimerch/debug', '', 'SSL'),
			)
		));
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/debug.tpl', $this->data));
	}
}
?>
