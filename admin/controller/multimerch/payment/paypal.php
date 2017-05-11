<?php
class ControllerMultimerchPaymentPaypal extends ControllerMultimerchBase {
	private $version = '1.0';
	private $name = 'paypal';
	private $_log;

	private $settings = Array(
		'pp_address' => '',		// PayPal address
		's_sandbox' => 1,		// PP Standard sandbox mode
		'mp_sandbox' => 1,		// PP Masspay sandbox mode
		'api_username' => '',	// Masspay API username
		'api_password' => '',	// Masspay API password
		'api_signature' => '',	// Masspay API signature
		'fee_enabled' => '',	// Enable gateway for fee payments
		'payout_enabled' => '',	// Enable gateway for payouts
		'debug' => 0
	);

	private $seller_settings = array(
		'pp_address'
	);

	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
		$this->data = array_merge($this->data, $this->load->language('multimerch/payment/' . $this->name));

		$this->load->model('setting/setting');

		$this->data['token'] = $this->session->data['token'];
		$this->data['name'] = $this->name;
		$this->data['admin_setting_full_prefix'] = MsPgPayment::ADMIN_SETTING_PREFIX . $this->name;
		$this->data['seller_setting_full_prefix'] = MsPgPayment::SELLER_SETTING_PREFIX . $this->name;

		if ($this->config->get($this->data['admin_setting_full_prefix'] . '_log_filename')){
			$this->_log = new Log($this->config->get($this->data['admin_setting_full_prefix'] . '_log_filename'));
		}else{
			$this->_log = new Log("pg_paypal.log");
		}
	}

	public function jxGetPaymentForm() {
		if(empty($this->request->post) || !isset($this->request->post['pg_code']) || $this->request->post['pg_code'] !== $this->name) {
			$this->data['errors'][] = $this->language->get('ms_pg_payment_error_no_method');
		}

		if (empty($this->data['errors'])) {
			$sellers = array();

			$request_ids = array_unique($this->request->post['request_ids']);
			$total_amount = 0;
			$payment_description = array();

			foreach ($request_ids as $request_id) {
				$request_info = MsLoader::getInstance()->MsPgRequest->getRequests(array('request_id' => $request_id, 'single' => 1));
				$seller_id = $request_info['seller_id'];

				if($seller_id) {
					$seller_info = MsLoader::getInstance()->MsSeller->getSeller($seller_id);
					if (!$seller_info) continue;

					$seller_settings = MsLoader::getInstance()->MsSetting->getSellerSettings(
						array(
							'seller_id' => $seller_id,
							'code' => $this->data['seller_setting_full_prefix']
						)
					);

					if(empty($seller_settings) || !$this->validateSellerSettings($seller_settings)) {
						$this->data['errors'][] = sprintf($this->data['error_seller_info'], $this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $seller_id), $seller_info['ms.nickname']);
						continue;
					}

					foreach ($seller_settings as $key => $value) {
						$this->data['sellers'][$seller_id][str_replace(MsPgPayment::SELLER_SETTING_PREFIX . $this->name . '_', '', $key)] = $value;
					}

					$this->data['sellers'][$seller_id]['ms.nickname'] = $seller_info['ms.nickname'];

					$total_amount += $request_info['amount'];
					$payment_description[$request_id] = $request_info['description'];

//					$this->data['sellers'][$seller_id]['amount'] = $this->MsLoader->MsBalance->getSellerBalance($seller_id) - $this->MsLoader->MsBalance->getReservedSellerFunds($seller_id);
					$this->data['sellers'][$seller_id]['request_id'] = $request_info['request_id'];
					$this->data['sellers'][$seller_id]['amount'] = $request_info['amount'];
					$this->data['sellers'][$seller_id]['amount_formatted'] = $this->currency->format(abs($request_info['amount']), $this->config->get('config_currency'));
				}
			}

			$this->data['total_amount'] = $total_amount;
			$this->data['total_amount_formatted'] = $this->currency->format($total_amount, $this->config->get('config_currency'));
			$this->data['payment_description'] = htmlspecialchars(json_encode($payment_description));
		}

		$this->response->setOutput(json_encode($this->load->view('multimerch/payment/' . $this->name . '_payment_form.tpl', $this->data)));
	}

	public function jxCompletePayment() {
		$json = array();
		$data = $this->request->post;

		if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
			$this->_log->write('Starting Admin PayPal payment flow...');

		if(!isset($data['payment_method']) || !isset($data['payment_type']) || !isset($data['total_amount']) || !isset($data['payment_description'])) {
			$json['errors'][] = $this->language->get('ms_pg_payment_error_payment');

			if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
				$this->_log->write($this->language->get('ms_pg_payment_error_payment'));
		}

		if(!isset($data['receiver_data'])) {
			$json['errors'][] = $this->language->get('ms_pg_payment_error_receiver_data');

			if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
				$this->_log->write($this->language->get('ms_pg_payment_error_receiver_data'));
		}

		if(!isset($json['errors'])) {
			require_once(DIR_SYSTEM . 'library/multimerch/payment/paypal.php');

			$pp_paymentParams = array();

			if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
				$this->_log->write('Creating PP request params...');

			$pp_requestParams = array(
				'RECEIVERTYPE' => 'EmailAddress',
				'CURRENCYCODE' => $this->config->get('config_currency')
			);

			if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
				$this->_log->write('PP request params: '  . print_r($pp_requestParams, true));

			$i = 0;
			$receiver_data = array();

			if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
				$this->_log->write('Creating PP payment params...');

			foreach ($data['receiver_data'] as $seller_id => $seller_data) {
				$receiver_data[$seller_id]['pp_address'] = $seller_data['pp_address'];

				$pp_paymentParams['L_EMAIL' . $i] = $seller_data['pp_address'];
				$pp_paymentParams['L_AMT' . $i] = abs($seller_data['amount']);
				$i++;
			}

			if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
				$this->_log->write('PP request params: '  . print_r($pp_paymentParams, true));

			if(count($data['receiver_data']) > 1) { // If there are more than one receiver, use PayPal Masspay
				if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
					$this->_log->write('Using PayPal MassPay for ' . count($data['receiver_data']) . ' sellers');

				// Create payment record with incomplete status
				if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
					$this->_log->write('Creating MM Payment with status "Incomplete"...');

				$payment_id = $this->MsLoader->MsPgPayment->createPayment(array(
					'seller_id' => MsPgPayment::ADMIN_ID,
					'payment_type' => $data['payment_type'],
					'payment_code' => $data['payment_method'],
					'payment_status' => MsPgPayment::STATUS_INCOMPLETE,
					'amount' => $data['total_amount'],
					'currency_id' => $this->currency->getId($this->config->get('config_currency')),
					'currency_code' => $this->config->get('config_currency'),
					'sender_data' => array('pp_address' => $this->config->get($this->data['admin_setting_full_prefix'] . '_pp_address')),
					'receiver_data' => $receiver_data,
					'description' => html_entity_decode($data['payment_description'])
				));

				if($payment_id) {
					if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
						$this->_log->write('Payment #' . $payment_id . ' was successfully created');

					// Bind payment request to payment
					foreach ($data['receiver_data'] as $seller_id => $seller_data) {
						// Update payment request data
						$this->MsLoader->MsPgRequest->updateRequest($seller_data['request_id'], array(
							'seller_id' => $seller_id,
							'payment_id' => $payment_id,
						));

						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write('Payment Request #' . $seller_data['request_id'] . ' for seller # ' . $seller_id . ' was successfully updated');
					}

					if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
						$this->_log->write('Sending request to PayPal');

					// Send paypal request
					$paypal = new PayPal($this->config->get($this->data['admin_setting_full_prefix'] . '_api_username'), $this->config->get($this->data['admin_setting_full_prefix'] . '_api_password'), $this->config->get($this->data['admin_setting_full_prefix'] . '_api_signature'), $this->config->get($this->data['admin_setting_full_prefix'] . '_mp_sandbox'));
					$response = $paypal->request('MassPay', $pp_requestParams + $pp_paymentParams);

					// Process paypal response
					if (!$response) {
						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write("PayPal Request Error. Payment # " . $payment_id . ": " . $paypal->getErrors());

						$json['errors'][] = $this->language->get('ms_error_withdraw_response');
						$json['response'] = print_r($paypal->getErrors(), true);
					} else if ($response['ACK'] != 'Success') {
						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write("PayPal Request Error: " . print_r($response, true));

						$json['errors'][] = $this->language->get('ms_error_withdraw_status') . (isset($response['L_LONGMESSAGE0']) ? ('. ' . $response['L_LONGMESSAGE0']) : '');
						$json['response'] = print_r($response, true);
					} else {
						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write('Received PayPal Response: ' . print_r($response, true));

						$json['success'] = $this->language->get('ms_success_transactions');
						$json['response'] = print_r($response, true);

						// Update payment
						$this->MsLoader->MsPgPayment->updatePayment($payment_id, array(
							'payment_status' => MsPgPayment::STATUS_COMPLETE
						));

						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write('MM Payment # ' . $payment_id . ' was successfully updated');

						// Update payment requests
						foreach ($data['receiver_data'] as $seller_id => $seller_data) {
							$this->MsLoader->MsPgRequest->updateRequest($seller_data['request_id'], array(
								'request_status' => MsPgRequest::STATUS_PAID,
								'date_modified' => 1
							));

							if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
								$this->_log->write('MM Payment Request # ' . $seller_data['request_id'] . ' for seller # ' . $seller_id . ' was successfully updated');

							$seller = $this->MsLoader->MsSeller->getSeller($seller_id);

							$this->MsLoader->MsBalance->addBalanceEntry(
								$seller_id,
								array(
									'withdrawal_id' => $payment_id,
									'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
									'amount' => -$seller_data['amount'],
									'description' => sprintf($this->language->get('ms_payment_royalty_payout'), $seller['name'], $this->config->get('config_name'))
								)
							);

							if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
								$this->_log->write('Balance entries for seller # ' . $seller_id . ' were successfully created');

						}
					}
				} else {
					if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
						$this->_log->write('MM Payment was not created');

					$json['errors'][] = $this->language->get('ms_pg_payment_error_payment');
				}
			} else { // If there is only one receiver, use PayPal Standard
				if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
					$this->_log->write('Using PayPal Standard for ' . count($data['receiver_data']) . ' seller');

				$seller_id = key($data['receiver_data']);

				$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
				if (!$seller) {
					if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
						$this->_log->write('Error PP Standard: ' . $this->language->get('ms_pg_payment_error_receiver_data'));

					$json['errors'][] = $this->language->get('ms_pg_payment_error_receiver_data');
				}

				if(empty($json['errors'])) {
					// Create payment record with incomplete status
					$payment_id = $this->MsLoader->MsPgPayment->createPayment(array(
						'seller_id' => MsPgPayment::ADMIN_ID,
						'payment_type' => $data['payment_type'],
						'payment_code' => $data['payment_method'],
						'payment_status' => MsPgPayment::STATUS_INCOMPLETE,
						'amount' => $data['total_amount'],
						'currency_id' => $this->currency->getId($this->config->get('config_currency')),
						'currency_code' => $this->config->get('config_currency'),
						'sender_data' => array('pp_address' => $this->config->get($this->data['admin_setting_full_prefix'] . '_pp_address')),
						'receiver_data' => $receiver_data,
						'description' => html_entity_decode($data['payment_description'])
					));

					if($payment_id) {
						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write('Payment #' . $payment_id . ' was successfully created');

						// Bind payment request with payment id
						$this->MsLoader->MsPgRequest->updateRequest($seller_data['request_id'], array(
							'seller_id' => $seller_id,
							'payment_id' => $payment_id,
						));

						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write('Payment Request #' . $seller_data['request_id'] . ' for seller # ' . $seller_id . ' was successfully updated');

						// render paypal form
						$this->data['payment_data'] = array(
							'sandbox' => $this->config->get($this->data['admin_setting_full_prefix'] . '_s_sandbox'),
							'action' => $this->config->get($this->data['admin_setting_full_prefix'] . '_s_sandbox') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
							'business' => $data['receiver_data'][$seller_id]['pp_address'],
							'item_name' => sprintf($this->language->get('ms_payment_royalty_payout'), $seller['name'], $this->config->get('config_name')),
							'amount' => $this->currency->format($data['total_amount'], $this->config->get('config_currency'), '', FALSE),
							'currency_code' => $this->config->get('config_currency'),
							'return' => $this->url->link('multimerch/payment', 'token=' . $this->session->data['token']),
							'cancel_return' => $this->url->link('multimerch/payment-request', 'token=' . $this->session->data['token']),
							'notify_url' => HTTP_CATALOG . 'index.php?route=multimerch/payment/paypal/paypalIPN',
							'custom' => $payment_id
						);

						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write('PayPal Standard payment form data: ' . print_r($this->data['payment_data'], true));

						list($template, $children) = $this->MsLoader->MsHelper->admLoadTemplate('multimerch/payment/paypal_standard_payment_form');
						$json['pp_form'] = $this->load->view($template, $this->data);
						$json['success'] = 1;
					} else {
						if($this->config->get($this->data['admin_setting_full_prefix'] . '_debug'))
							$this->_log->write('MM Payment was not created');

						$json['errors'][] = $this->language->get('ms_pg_payment_error_payment');
					}
				}
			}
		}

		return $this->response->setOutput(json_encode($json));
	}

	public function jxGetPgSettingsForm() {
		// Get seller settings
		foreach ($this->seller_settings as $setting_name) {
			if (isset($this->request->get['seller_id'])) {
				$setting_data = MsLoader::getInstance()->MsSetting->getSellerSettings(
					array(
						'seller_id' => $this->request->get['seller_id'],
						'name' => $this->data['seller_setting_full_prefix'] . '_' . $setting_name,
						'single' => 1
					)
				);
			}

			$this->data[$setting_name] = !empty($setting_data) ? $setting_data : '';
		}

		return $this->load->view('multimerch/payment/' . $this->name . '_settings_form.tpl', $this->data);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAccess()) {
			$settings = array();
			foreach ($this->request->post as $key => $value) {
				$settings[$this->data['admin_setting_full_prefix'] . '_' . $key] = $value;
			}
			$this->model_setting_setting->editSetting($this->data['admin_setting_full_prefix'], $settings);
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('multimerch/payment-gateway', 'token=' . $this->session->data['token'], true));
		}

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		$this->data['breadcrumbs'] = MsLoader::getInstance()->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('module/multimerch', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_pg_heading'),
				'href' => $this->url->link('multimerch/payment-gateway', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('multimerch/payment/' . $this->name, '', 'SSL'),
			)
		));

		$this->data['action'] = $this->url->link('multimerch/payment/' . $this->name, 'token=' . $this->session->data['token'], true);
		$this->data['cancel'] = $this->url->link('multimerch/payment-gateway', 'token=' . $this->session->data['token'], true);

		//default log filename
		$this->settings['log_filename'] = 'pg_paypal.'.uniqid().'.log';

		foreach ($this->settings as $setting_name => $value) {
			if (isset($this->request->post[$setting_name])) {
				$this->data[$setting_name] = $this->request->post[$setting_name];
			} else if($this->config->get($this->data['admin_setting_full_prefix'] . '_' . $setting_name )!== null) {
				$this->data[$setting_name] = $this->config->get($this->data['admin_setting_full_prefix'] . '_' . $setting_name);
			}else{
				$this->data[$setting_name] = $value;
			}
		}

		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('multimerch/payment/' . $this->name, $this->data));
	}

	protected function validateAccess() {
		if (!$this->user->hasPermission('modify', 'multimerch/payment/' . $this->name)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateSellerSettings($data) {
		$errors = array();
		foreach ($this->seller_settings as $setting_name) {
			if(!$data[$this->data['seller_setting_full_prefix'] . '_' . $setting_name]) {
				$errors[] = 'Error ' . $setting_name;
			}
		}
		return !$errors;
	}
}