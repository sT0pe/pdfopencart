<?php

class ControllerSellerAccountPayment extends ControllerSellerAccount {
	public function __construct($registry) {
		parent::__construct($registry);
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
	}

	public function getPaymentData() {
		$colMap = array(
			'type' => 'payment_type',
			'description' => 'description',
			'date_created' => 'date_created'
		);

		$seller_id = $this->customer->getId();

		$sorts = array('payment_id', 'amount', 'date_created');
		$filters = array_merge($sorts, array('description'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsPgPayment->getPayments(
			array(
				'seller_id' => $seller_id,
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
			$pg_name = str_replace('ms_pg_', '', $result['payment_code']);
			$this->load->language('multimerch/payment/' . $pg_name);

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

			$payment_status = $this->language->get('ms_pg_payment_status_' . $result['payment_status']);
//			if($result['payment_status'] == MsPgPayment::STATUS_INCOMPLETE && $pg_name == 'bank_transfer' && (in_array(MsPgRequest::TYPE_LISTING, $requests_types) || in_array(MsPgRequest::TYPE_SIGNUP, $requests_types))) {
//				$payment_status .= '<button type="button" data-toggle="tooltip" title="" class="ms-confirm-bank-transfer btn btn-primary" data-original-title="Apply"><i class="fa  fa-check"></i></button>';
//			}

			$columns[] = array_merge(
				$result,
				array(
					'payment_id' => $result['payment_id'],
					'payment_type' => $this->language->get('ms_pg_payment_type_' . $result['payment_type']),
					'payment_status' => $payment_status,
					'description' => $description,
					'amount' => $this->currency->format(abs($result['amount']), $result['currency_code']),
					'payment_method' => $result['payment_code'] == 'ms_pp_adaptive' ? 'PayPal Adaptive': $this->language->get('text_title'), //todo language files
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function index() {
		$seller_id = $this->customer->getId();

		$seller_balance = $this->MsLoader->MsBalance->getSellerBalance($seller_id);
		$pending_funds = $this->MsLoader->MsBalance->getReservedSellerFunds($seller_id);
		$waiting_funds = $this->MsLoader->MsBalance->getWaitingSellerFunds($seller_id, 14);
		$balance_formatted = $this->currency->format($seller_balance,$this->config->get('config_currency'));

		$balance_reserved_formatted = $pending_funds > 0 ? sprintf($this->language->get('ms_account_balance_reserved_formatted'), $this->currency->format($pending_funds)) . ', ' : '';
		$balance_reserved_formatted .= $waiting_funds > 0 ? sprintf($this->language->get('ms_account_balance_waiting_formatted'), $this->currency->format($waiting_funds)) . ', ' : '';
		$balance_reserved_formatted = ($balance_reserved_formatted == '' ? '' : '(' . substr($balance_reserved_formatted, 0, -2) . ')');

		$this->data['ms_balance_formatted'] = $balance_formatted;
		$this->data['ms_reserved_formatted'] = $balance_reserved_formatted;

		$earnings = $this->MsLoader->MsSeller->getTotalEarnings($seller_id);

		$this->data['earnings'] = $this->currency->format($earnings, $this->config->get('config_currency'));
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');

		$this->document->setTitle($this->language->get('ms_payment_payments_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_transactions_breadcrumbs'),
				'href' => $this->url->link('seller/account-payment', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-payment');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function create() {
		$this->document->addScript('catalog/view/javascript/multimerch/account-payment-form.js');

		$this->data['payment_methods'] = $this->_getPaymentMethods();

		if(empty($this->data['payment_methods'])) {
			$this->data['error_payment_methods'] = $this->language->get('ms_pg_payment_error_not_available');
			$this->data['action_back'] = $this->url->link('seller/account-payment-request', '', 'SSL');
		}

		$payment_request_data = array();
		$total_amount = 0;
		$payment_description = array();

		if(isset($this->request->post['payment_requests']) && is_array($this->request->post['payment_requests'])) {
			foreach ($this->request->post['payment_requests'] as $request_id) {
				$payment_request_data[$request_id] = $this->MsLoader->MsPgRequest->getRequests(
					array(
						'request_id' => $request_id,
						'single' => 1
					)
				);

				if(empty($payment_request_data[$request_id])) {
					unset($payment_request_data[$request_id]);
					continue;
				}

				$total_amount += $payment_request_data[$request_id]['amount'];
				$payment_description[$request_id] = $payment_request_data[$request_id]['description'];
			}
		} else {
			$this->response->redirect($this->url->link('account/account', '', 'SSL'));
		}

		if(empty($payment_request_data)) {
			$this->response->redirect($this->url->link('seller/account-payment-request', '', 'SSL'));
		}

		$this->data['payment_requests'] = $payment_request_data;

		$this->data['seller_id'] = $this->customer->getId();
		$this->data['total_amount'] = $total_amount;
		$this->data['payment_description'] = htmlspecialchars(json_encode($payment_description));

		$this->data['heading'] = $this->language->get('ms_pg_new_payment');

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_transactions_breadcrumbs'),
				'href' => $this->url->link('seller/account-transaction', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-payment-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	private function _getPaymentMethods() {
		$this->load->model('extension/extension');
		$this->load->model('setting/setting');

		$methods = array();
		$extensions = $this->model_extension_extension->getExtensions('ms_payment');
		foreach ($extensions as $extension) {
			if(strpos($extension['code'], 'ms_pg_') !== false) {
				$extension_name = str_replace('ms_pg_', '', $extension['code']);
				$this->load->language('multimerch/payment/' . $extension_name);

				$extension['name'] = $this->language->get('text_title');

				$settings = $this->model_setting_setting->getSetting($extension['code']);
				foreach ($settings as $key => $value) {
					if(strpos($key, 'fee_enabled') && $value) $methods[] = $extension;
				}
			}
		}

		return $methods;
	}
}

?>
