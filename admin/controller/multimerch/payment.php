<?php

class ControllerMultimerchPayment extends ControllerMultimerchBase {
	public function getTableData() {
		$colMap = array(
			'payment_id' => 'payment_id',
			'payment_code' => 'payment_code',
			'seller' => 'nickname',
			'type' => 'payment_type',
			'description' => 'description',
			'payment_status' => 'payment_status',
			'date_created' => 'date_created'
		);
		
		$sorts = array('payment_id', 'payment_type', 'payment_code', 'seller', 'description', 'amount', 'payment_status', 'date_created');
		$filters = array_diff($sorts, array('payment_status'));
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsPgPayment->getPayments(
			array(
//				'payment_status' => array(MsPgPayment::STATUS_COMPLETE, MsPgPayment::STATUS_WAITING_CONFIRMATION)
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
			// payment method name
			$pg_name = str_replace(MsPgPayment::ADMIN_SETTING_PREFIX, '', $result['payment_code']);

			// description
			$description = '';
			$description .= '<ul style="list-style: none; padding-left: 0;">';
			foreach ($result['description'] as $request_id => $value) {
				$description .= '<li>' . $value . '</li>';
			}
			$description .= '</ul>';

			$requests = $this->MsLoader->MsPgRequest->getRequests(array(
				'payment_id' => $result['payment_id']
			));
			$requests_types = array();
			foreach ($requests as $request) {
				$requests_types[] = $request['request_type'];
			}

			if($pg_name == 'ms_pp_adaptive') {
				$this->load->language('payment/ms_pp_adaptive');
				$payment_method = $this->language->get('ppa_adaptive');
			} else {
				$this->load->language('multimerch/payment/' . $pg_name);
				if($pg_name == 'paypal') {
					$payment_method = count($requests) > 1 ? $this->language->get('text_mp_method_name') : $this->language->get('text_s_method_name');
				} else {
					$payment_method = $this->language->get('text_method_name');
				}
			}

			$payment_status = $this->language->get('ms_pg_payment_status_' . $result['payment_status']);
//			if($result['payment_status'] == MsPgPayment::STATUS_INCOMPLETE && $pg_name == 'bank_transfer' && (in_array(MsPgRequest::TYPE_LISTING, $requests_types) || in_array(MsPgRequest::TYPE_SIGNUP, $requests_types))) {
			if($result['payment_status'] == MsPgPayment::STATUS_INCOMPLETE && $pg_name == 'bank_transfer') {
				$payment_status .= '<button type="button" data-toggle="tooltip" title="" class="ms-confirm-bank-transfer btn btn-primary" data-original-title="Apply"><i class="fa  fa-check"></i></button>';
			}

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['payment_id']}' />",
					'payment_id' => "<input type='hidden' name='payment_id' value='" . $result['payment_id']. "' />" . $result['payment_id'],
					'payment_type' => $this->language->get('ms_pg_payment_type_' . $result['payment_type']),
					'payment_code' => $payment_method,
					'seller' => "<a href='".$this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL')."'>{$result['nickname']}</a>",
					'description' => $description,
					'amount' => $this->currency->format(abs($result['amount']), $result['currency_code']),
					'payment_status' => $payment_status,
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created']))
				)
			);
		}
		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function jxDelete() {
		$json = array();
		$data = $this->request->post['payment_ids'];

		if(!isset($data) || empty($data)) {
			$json['errors'][] = 'Something is empty!';
		}

		if(!isset($json['errors'])) {
			foreach ($data as $payment_id) {
				$this->MsLoader->MsPgPayment->deletePayment($payment_id);

				$linked_requests = $this->MsLoader->MsPgRequest->getRequests(array('payment_id' => $payment_id));
				if(isset($linked_requests) && !empty($linked_requests)) {
					foreach ($linked_requests as $linked_request) {
						$this->MsLoader->MsPgRequest->deleteRequest($linked_request['request_id']);
					}
				}
			}

			$json['success'] = 'Success!';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->validate(__FUNCTION__);

		$this->document->addScript('view/javascript/multimerch/payment.js');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_payment_heading');
		$this->document->setTitle($this->language->get('ms_payment_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payment_breadcrumbs'),
				'href' => $this->url->link('multimerch/payment', '', 'SSL'),
			)
		));
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/payment.tpl', $this->data));
	}

	public function create() {
		$this->document->addScript('view/javascript/multimerch/payment-form.js');

		$request_ids = array();

		if(isset($this->request->get['request_ids'])) {
			$request_ids = explode(',', $this->request->get['request_ids']);
		} else if(isset($this->request->post['selected'])) {
			$request_ids = $this->request->post['selected'];
		} else {
			$this->response->redirect('multimerch/payment-request', 'token=' . $this->session->data['token'], 'SSL');
		}

		$this->data['payment_requests'] = array();

		if(!empty($request_ids)) {
			foreach ($request_ids as $request_id) {
				$request_info = $this->MsLoader->MsPgRequest->getRequests(array(
					'request_id' => $request_id,
					'single' => 1
				));

				if(!$request_info || empty($request_info)) {
					$this->response->redirect($this->url->link('multimerch/payment-request', 'token=' . $this->session->data['token'], 'SSL'));
				}

				$request_info['amount_formatted'] = $this->currency->format(abs($request_info['amount']), $request_info['currency_code']);

				$this->data['payment_requests'][] = $request_info;
			}
		} else {
			$this->data['error_warning'] = $this->language->get('ms_pg_payment_error_no_requests');
		}

		$this->data['payment_type'] = array(
			'id' => MsPgPayment::TYPE_PAID_REQUESTS,
			'name' => $this->language->get('ms_pg_payment_type_' . MsPgPayment::TYPE_PAID_REQUESTS)
		);

		$this->data['payment_methods'] = $this->_getPaymentMethods();

		// if there are payouts for more than one seller, only PayPal Masspay can be used
		if(count($this->data['payment_requests']) > 1) {
			foreach ($this->data['payment_methods'] as $key => &$payment_method) {
				if ($payment_method['code'] !== 'ms_pg_paypal') unset($this->data['payment_methods'][$key]);
			}
		}

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_payment_new');
		$this->document->setTitle($this->language->get('ms_payment_new'));

		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', 0);
		$this->data['store_name'] = $store_info['config_name'];

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payment_breadcrumbs'),
				'href' => $this->url->link('multimerch/payment', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payment_new'),
				'href' => $this->url->link('multimerch/payment/create', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/payment-form.tpl', $this->data));
	}

	private function _getPaymentMethods() {
		$methods = array();

		$this->load->model('extension/extension');
		$this->load->model('setting/setting');

		$extensions = $this->model_extension_extension->getInstalled('ms_payment');
		$i = 0;
		foreach ($extensions as $extension) {
			if(strpos($extension, 'ms_pg_') !== false) {
				$extension_name = str_replace('ms_pg_', '', $extension);
				$this->load->language('multimerch/payment/' . $extension_name);

				$settings = $this->model_setting_setting->getSetting($extension);
				foreach ($settings as $key => $value) {
					if(strpos($key, 'payout_enabled') && $value) {
						$methods[$i] = array(
							'code' => $extension,
							'name' => $this->language->get('heading_title')
						);
						$i++;
					}
				}
			}
		}

		return $methods;
	}

}

