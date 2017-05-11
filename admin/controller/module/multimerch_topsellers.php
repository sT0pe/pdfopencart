<?php
class ControllerModuleMultiMerchTopsellers extends ControllerMultimerchBase {
	private $version = '2.0';
	private $name = 'multimerch_topsellers';
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'), $this->load->language("module/{$this->name}"));
		$this->data['token'] = $this->session->data['token'];
	}

	public function index() {
		$this->load->language("module/{$this->name}");
		$this->document->setTitle($this->language->get('ms_config_topsellers'));
		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule($this->name, $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
		$this->data['error_width'] = isset($this->error['width']) ? $this->error['width'] : '';
		$this->data['error_height'] = isset($this->error['height']) ? $this->error['height'] : '';

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_module'),
				'href' => $this->url->link('extension/extension', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link("extension/module/{$this->name}", '', 'SSL'),
			)
		));

		if (!isset($this->request->get['module_id'])) {
			$this->data['action'] = $this->url->link("extension/module/{$this->name}", 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link("extension/module/{$this->name}", 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
		}

		$this->data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}	
			
		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$this->data['name'] = $module_info['name'];
		} else {
			$this->data['name'] = '';
		}
				
		if (isset($this->request->post['limit'])) {
			$this->data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$this->data['limit'] = $module_info['limit'];
		} else {
			$this->data['limit'] = 5;
		}	
				
		if (isset($this->request->post['width'])) {
			$this->data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$this->data['width'] = $module_info['width'];
		} else {
			$this->data['width'] = 200;
		}	
			
		if (isset($this->request->post['height'])) {
			$this->data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$this->data['height'] = $module_info['height'];
		} else {
			$this->data['height'] = 200;
		}
				
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$this->data['status'] = $module_info['status'];
		} else {
			$this->data['status'] = '';
		}
			
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view("module/{$this->name}.tpl", $this->data));
	}

	private function _validate() {
		if (!$this->user->hasPermission('modify', "module/{$this->name}")) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}
		
		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}	

		return !$this->error;
	}
}