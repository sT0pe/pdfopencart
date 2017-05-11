<?php

class ControllerMultimerchShippingMethod extends ControllerMultimerchBase {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
	}

	public function getTableData() {
		$this->load->model('tool/image');

		$colMap = array(
			'id' => 'sm.shipping_method_id',
			'name' => 'smd.name',
			'description' => 'smd.description',
//			'logo' => 'smd.description',
			'status' => 'sm.status',
		);

		$sorts = array('name', 'description', 'status');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsShippingMethod->getShippingMethods(
			array(
				'language_id' => $this->config->get('config_language_id')
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/shipping-method/edit', 'token=' . $this->session->data['token'] . '&shipping_method_id=' . $result['shipping_method_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<a class='btn btn-danger' href='" . $this->url->link('multimerch/shipping-method/delete', 'token=' . $this->session->data['token'] . '&shipping_method_id=' . $result['shipping_method_id'], 'SSL') . "' title='".$this->language->get('ms_delete')."'><i class='fa fa-trash-o'></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['shipping_method_id']}' />",
					'name' => $result['name'],
					'description' => $result['description'],
					'logo' => $this->model_tool_image->resize($result['logo'], 30, 30),
					'status' => $result['status'] ? $this->language->get('ms_shipping_method_status_' . $result['status']) : '',
					'actions' => $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function jxSaveShippingMethod() {
		$data = $this->request->post['shipping_method'];

		if(!isset($data['logo'])) {
			$data['logo'] = NULL;
		}

		$json = array();

		foreach ($data['description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$json['errors']['shipping_method[description][' . $language_id . '][name]'] = $this->language->get('ms_shipping_method_name_error');
			}
		}

		if (empty($json['errors'])) {
			if (empty($data['shipping_method_id'])) {
				$this->MsLoader->MsShippingMethod->createShippingMethod($data);
				$this->session->data['success'] = $this->data['ms_shipping_method_add_success'];
			} else {
				$this->MsLoader->MsShippingMethod->editShippingMethod($data['shipping_method_id'], $data);
				$this->session->data['success'] = $this->data['ms_shipping_method_edit_success'];
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxSaveDeliveryTime() {
		$json = array();

		if (!empty($this->request->post) && isset($this->request->post['names'])) {
			$shipping_delivery_id = $this->MsLoader->MsShippingMethod->createShippingDeliveryTime(
				array(
					'names' => $this->request->post['names']
				)
			);
			$json['delivery_time_id'] = $shipping_delivery_id;
			$json['delivery_time_names'] = $this->request->post['names'];
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxEditDeliveryTime() {
		$json = array();

		if (!empty($this->request->post)) {
			$this->MsLoader->MsShippingMethod->editShippingDeliveryTime($this->request->post);
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteDeliveryTime() {
		$json = array();

		if (!empty($this->request->post) && $this->request->post['id']) {
			$this->MsLoader->MsShippingMethod->deleteShippingDeliveryTime($this->request->post['id']);
			$json['result'] = 'Success!';
		}

		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->_validateRoute();

		$this->document->addScript('//code.jquery.com/ui/1.11.2/jquery-ui.min.js');
		$this->validate(__FUNCTION__);
		$this->data['add'] = $this->url->link('multimerch/shipping-method/add', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('multimerch/shipping-method/add', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
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
		$this->data['heading'] = $this->language->get('ms_shipping_method_heading');
		$this->document->setTitle($this->language->get('ms_shipping_method_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_shipping_method_breadcrumbs'),
				'href' => $this->url->link('multimerch/shipping-method', '', 'SSL'),
			)
		));
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/shipping-method.tpl', $this->data));
	}

	public function add() {
		$this->_validateRoute();
		$this->_initForm();
	}

	public function edit() {
		$this->_validateRoute();
		$this->_initForm();
	}

	public function delete() {
		$this->_validateRoute();

		if(isset($this->request->post['selected'])){
			$shipping_method_ids = $this->request->post['selected'];
		} else if($this->request->get['shipping_method_id']) {
			$shipping_method_id = $this->request->get['shipping_method_id'];
			$shipping_method_ids = array($shipping_method_id);
		}
		foreach($shipping_method_ids as $shipping_method_id) {
			$this->MsLoader->MsShippingMethod->deleteShippingMethod($shipping_method_id);
		}

		$this->session->data['success'] = $this->data['ms_shipping_method_delete_success'];
		$this->response->redirect($this->url->link('multimerch/shipping-method', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function _validateRoute() {
		if(!$this->user->isLogged() || (int)$this->config->get('msconf_shipping_type') !== 2) {
			$this->response->redirect($this->url->link('multimerch/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	private function _initForm() {
		$this->data['token'] = $this->session->data['token'];
		$this->data['cancel'] = $this->url->link('multimerch/shipping-method', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if(isset($this->request->get['shipping_method_id'])) {
			$this->data['heading'] = $this->language->get('ms_shipping_method_edit_heading');
			$this->document->setTitle($this->language->get('ms_shipping_method_edit_heading'));

			$shipping_method_exists = $this->MsLoader->MsShippingMethod->shippingMethodExists($this->request->get['shipping_method_id']);
			if($shipping_method_exists) {
				$this->data['shipping_method'] = $this->MsLoader->MsShippingMethod->getShippingMethods(array('shipping_method_id' => $this->request->get['shipping_method_id']));
			} else {
				$this->response->redirect($this->url->link('multimerch/shipping-method', 'token=' . $this->session->data['token'], 'SSL'));
			}
		} else {
			$this->data['heading'] = $this->language->get('ms_shipping_method_add_heading');
			$this->document->setTitle($this->language->get('ms_shipping_method_add_heading'));

			$this->data['shipping_method'] = NULL;
		}

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_shipping_method_breadcrumbs'),
				'href' => $this->url->link('multimerch/shipping-method', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/shipping-method-form.tpl', $this->data));
	}
}
?>