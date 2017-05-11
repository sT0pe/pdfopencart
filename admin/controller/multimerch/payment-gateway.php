<?php

class ControllerMultimerchPaymentGateway extends ControllerMultimerchBase {
	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model('extension/extension');
		$this->load->model('user/user_group');
	}

	public function getTableData() {
		$extensions = $this->model_extension_extension->getInstalled('ms_payment');

		foreach ($extensions as $key => $value) {
			$ext_name = str_replace('ms_pg_', '', $value);
			if (!file_exists(DIR_APPLICATION . 'controller/multimerch/payment/' . $ext_name . '.php')) {
				$this->model_extension_extension->uninstall('ms_payment', $value);

				unset($extensions[$key]);
			}
		}

		$files = glob(DIR_APPLICATION . 'controller/multimerch/payment/*.php');

		$total = 0;
		$columns = array();

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				$ms_extension = 'ms_pg_' . $extension;

				$this->load->language('multimerch/payment/' . $extension);

				$text_link = $this->language->get('text_' . $extension);

				if ($text_link != 'text_' . $extension) {
					$link = $this->language->get('text_' . $extension);
				} else {
					$link = '';
				}

				// actions
				$actions = "";

				$ext_installed = in_array($ms_extension, $extensions) ? 1 : 0;

				if($ext_installed) {
					$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/payment/' . $extension, 'token=' . $this->session->data['token'], true) . "' title='" . $this->language->get('button_edit') . "'><i class='fa fa-pencil''></i></a>";
					$actions .= "<a class='btn btn-danger pg_uninstall' href='" . $this->url->link('multimerch/payment-gateway/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true) . "' title='" . $this->language->get('ms_delete') . "'><i class='fa fa-trash-o'></i></a>";
				} else {
					$actions .= "<a class='btn btn-success' href='" . $this->url->link('multimerch/payment-gateway/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true) . "' title='" . $this->language->get('button_install') . "'><i class='fa fa-plus''></i></a>";
				}

				$status = "";

				$status .= "<p><strong>" . $this->data['ms_pg_for_fee'] . "</strong> " . ($this->config->get($ms_extension . '_fee_enabled') ? $this->language->get('text_yes') : $this->language->get('text_no')) . "</p>";
				$status .= "<p><strong>" . $this->data['ms_pg_for_payout'] . "</strong> " . ($this->config->get($ms_extension . '_payout_enabled') ? $this->language->get('text_yes') : $this->language->get('text_no')) . "</p>";

				$columns[] = array(
					'name' => $this->language->get('heading_title'),
					'logo' => $link,
					'status' => $status,
					'actions' => $actions
				);

				$total++;
			}
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function index() {
		if (isset($this->session->data['error_warning'])) {
			$this->data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_pg_heading');
		$this->document->setTitle($this->language->get('ms_pg_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_pg_heading'),
				'href' => $this->url->link('multimerch/payment-gateway', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/payment-gateway.tpl', $this->data));
	}

	public function install() {
		if (isset($this->request->get['extension']) && $this->_validateAccess()) {
			$this->model_extension_extension->install('ms_payment', 'ms_pg_' . $this->request->get['extension']);

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'multimerch/payment/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'multimerch/payment/' . $this->request->get['extension']);

			// Call install method if it exsits
			$this->load->controller('multimerch/payment/' . $this->request->get['extension'] . '/install');

			$this->session->data['success'] = sprintf($this->data['ms_pg_install'], $this->request->get['extension']);
		}

		$this->response->redirect($this->url->link('multimerch/payment-gateway', 'token=' . $this->session->data['token'], true));
	}

	public function uninstall() {
		if (isset($this->request->get['extension']) && $this->_validateAccess()) {
			$this->model_extension_extension->uninstall('ms_payment', 'ms_pg_' . $this->request->get['extension']);

			MsLoader::getInstance()->MsSetting->deleteSellerSetting(array('code' => 'slr_pg_' . $this->request->get['extension']));

			$this->load->model('user/user_group');
			$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'multimerch/payment/' . $this->request->get['extension']);
			$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'multimerch/payment/' . $this->request->get['extension']);

			// Call uninstall method if it exsits
			$this->load->controller('multimerch/payment/' . $this->request->get['extension'] . '/uninstall');

			$this->session->data['success'] = sprintf($this->data['ms_pg_uninstall'], $this->request->get['extension']);
		}

		$this->response->redirect($this->url->link('multimerch/payment-gateway', 'token=' . $this->session->data['token'], true));
	}

	private function _validateAccess() {
		if (!$this->user->hasPermission('modify', 'multimerch/payment-gateway')) {
			$this->session->data['error_warning'] = $this->data['ms_pg_modify_error'];
		}

		return isset($this->session->data['error_warning']) ? false : true;
	}
}
?>