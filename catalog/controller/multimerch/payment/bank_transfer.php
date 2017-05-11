<?php

class ControllerMultimerchPaymentBankTransfer extends Controller {
	private $name = 'bank_transfer';
	private $version = '1.0.0.0';

	private $data = array();
	private $error = array();

	private $settings = array(
		'fname',
		'lname',
		'bank_name',
		'bank_country',
		'bic',
		'iban'
	);

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
		$this->data = array_merge($this->data, $this->load->language('multimerch/payment/' . $this->name), $this->load->language('multiseller/multiseller'));

		$this->load->model('setting/setting');

		$this->data['seller_id'] = $this->customer->getId();
		$this->data['pg_name'] = $this->name;

		$this->data['admin_setting_full_prefix'] = MsPgPayment::ADMIN_SETTING_PREFIX . $this->name;
		$this->data['seller_setting_full_prefix'] = MsPgPayment::SELLER_SETTING_PREFIX . $this->name;
	}

	public function jxSaveSettings() {
		$json = array();
		$data['settings'] = $this->request->post;
		$data['seller_id'] = $this->data['seller_id'];

		if(!$this->_validate($data['settings'])) {
			$json['error'] = $this->error;
		} else {
			foreach ($data['settings'] as $key => $value) {
				$data['settings'][$this->data['seller_setting_full_prefix'] . '_' . $key] = $value;
				unset($data['settings'][$key]);
			}

			MsLoader::getInstance()->MsSetting->createSellerSetting($data);
			$json['success'] = $this->data['text_success'];
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxGetPaymentForm() {
		if(empty($this->request->post) || !isset($this->request->post['pg_code']) || $this->request->post['pg_code'] !== $this->name) {
			$this->data['errors'][] = $this->language->get('ms_pg_payment_error_no_method');
		}

		if(!isset($this->data['errors'])) {
			// Get admin (receiver) information
			$admin_settings = $this->model_setting_setting->getSetting($this->data['admin_setting_full_prefix']);
			if(!empty($admin_settings)) {
				foreach ($admin_settings as $key => $value) {
					if(!(strpos($key, 'payout_enabled') !== false || strpos($key, 'fee_enabled') !== false)) {
						$this->data['admin_info'][str_replace($this->data['admin_setting_full_prefix'] . '_', '', $key)] = $value;
					}
				}
			}
			if(!isset($this->data['admin_info'])) {
				$this->data['errors'][] = sprintf($this->data['error_receiver_data'], $this->url->link('information/contact', '', 'SSL'));
			}

			// Get seller (sender) information
			$seller_settings = MsLoader::getInstance()->MsSetting->getSellerSettings(
				array(
					'seller_id' => $this->data['seller_id'],
					'code' => $this->data['seller_setting_full_prefix']
				)
			);
			if(!empty($seller_settings)) {
				foreach ($seller_settings as $key => $value) {
					$this->data['seller_info'][str_replace($this->data['seller_setting_full_prefix'] . '_', '', $key)] = $value;
				}
			}
			if(!isset($this->data['seller_info']) || !$this->_validate($this->data['seller_info'])) {
				$this->data['errors'][] = sprintf($this->data['error_sender_data'], $this->url->link('seller/account-setting', '', 'SSL'));
			}
		}

		if(!isset($this->data['errors'])) {
			// Get admin's full name and full bank name
			if(isset($this->data['admin_info']['fname']) && isset($this->data['admin_info']['lname'])) {
				$this->data['admin_info']['full_name'] = sprintf($this->data['text_full_name'], $this->data['admin_info']['fname'], $this->data['admin_info']['lname']);
			}

			if(isset($this->data['admin_info']['bank_name']) && isset($this->data['admin_info']['bank_country'])) {
				$this->data['admin_info']['full_bank_name'] = sprintf($this->data['text_full_bank_name'], $this->data['admin_info']['bank_name'], $this->data['admin_info']['bank_country']);
			}

			// Get seller's full name and full bank name
			if(isset($this->data['seller_info']['fname']) && isset($this->data['seller_info']['lname'])) {
				$this->data['seller_info']['full_name'] = sprintf($this->data['text_full_name'], $this->data['seller_info']['fname'], $this->data['seller_info']['lname']);
			}

			if(isset($this->data['seller_info']['bank_name']) && isset($this->data['seller_info']['bank_country'])) {
				$this->data['seller_info']['full_bank_name'] = sprintf($this->data['text_full_bank_name'], $this->data['seller_info']['bank_name'], $this->data['seller_info']['bank_country']);
			}
		}

		$this->response->setOutput(json_encode($this->load->view('multimerch/payment/' . $this->name . '_payment_form.tpl', $this->data)));
	}

	public function jxSavePayment() {
		$json = array();
		$data = $this->request->post;

		if(!isset($data['payment_method'])) {
			$json['errors'][] = $this->language->get('ms_pg_payment_error_no_method');
		}

		if(!isset($data['receiver_data'])) {
			$json['errors'][] = $this->language->get('ms_pg_payment_error_receiver_data');
		}

		if(!isset($data['sender_data'])) {
			$json['errors'][] = $this->language->get('ms_pg_payment_error_sender_data');
		}

		if(!isset($json['errors'])) {
			$payment_description = htmlspecialchars_decode($data['payment_description']);

			$payment_id = $this->MsLoader->MsPgPayment->createPayment(array(
				'seller_id' => $this->customer->getId(),
				'payment_type' => MsPgPayment::TYPE_PAID_REQUESTS,
				'payment_code' => $data['payment_method'],
				'payment_status' => MsPgPayment::STATUS_INCOMPLETE, //todo change to waiting
				'amount' => $data['total_amount'],
				'currency_id' => $this->currency->getId($this->config->get('config_currency')),
				'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
				'sender_data' => (!is_array($data['sender_data']) ? array($data['sender_data']) : $data['sender_data']),
				'receiver_data' => (!is_array($data['receiver_data']) ? array($data['receiver_data']) : $data['receiver_data']),
				'description' => $payment_description
			));

			if($payment_id) {
				// Update payment-request information
				foreach (json_decode($payment_description) as $request_id => $value) {
					$request = $this->MsLoader->MsPgRequest->getRequests(
						array(
							'request_id' => $request_id,
							'single' => 1
						)
					);

					if(empty($request)) continue;

					$this->MsLoader->MsPgRequest->updateRequest(
						$request_id,
						array(
							'payment_id' => $payment_id,
							'request_status' => MsPgRequest::STATUS_PAID,
							'date_modified' => 1
						)
					);

					/*if($request['request_type'] == MsPgRequest::TYPE_SIGNUP) {
						$this->MsLoader->MsSeller->changeStatus($data['seller_id'], MsSeller::STATUS_ACTIVE);
					} else if ($request['request_type'] == MsPgRequest::TYPE_LISTING) {
						$this->MsLoader->MsProduct->changeStatus($request['product_id'], MsSeller::STATUS_ACTIVE);
					}*/
				}
				$json['success'] = 1;
			} else {
				$json['errors'][] = 'Can\'t create payment record!';
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		// Get seller settings
		foreach ($this->settings as $setting_name) {
			if (isset($this->request->post[$setting_name])) {
				$this->data[$setting_name] = $this->request->post[$setting_name];
			} else {
				$setting_data = MsLoader::getInstance()->MsSetting->getSellerSettings(
					array(
						'seller_id' => $this->data['seller_id'],
						'name' => $this->data['seller_setting_full_prefix'] . '_' . $setting_name,
						'single' => 1
					)
				);
				$this->data[$setting_name] = !empty($setting_data) ? $setting_data : '';
			}
		}

		return $this->load->view('multimerch/payment/' . $this->name. '.tpl', $this->data);
	}

	private function _validate($data) {
		foreach ($this->settings as $setting_name) {
			if(!isset($data[$setting_name]) || !$data[$setting_name]) {
				$this->error['error_' . $setting_name] = $this->data['error_' . $setting_name];
			}
		}
		return !$this->error;
	}
}