<?php

class ControllerMultimerchPaymentPaypal extends Controller {
	private $name = 'paypal';
	private $version = '1.0.0.1';
	private $_log;

	private $data = array();
	private $error = array();

	private $settings = array(
		'pp_address'
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

		if ($this->config->get($this->data['admin_setting_full_prefix'] . '_log_filename')){
			$this->_log = new Log($this->config->get($this->data['admin_setting_full_prefix'] . '_log_filename'));
		}else{
			$this->_log = new Log("pg_paypal.log");
		}
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

		// todo make amounts for sender and receiver
		if(!isset($this->data['errors'])) {
			$admin_settings = $this->model_setting_setting->getSetting($this->data['admin_setting_full_prefix']);
			if(!empty($admin_settings)) {
				foreach ($admin_settings as $key => $value) {
					if(!(strpos($key, 'payout_enabled') !== false || strpos($key, 'fee_enabled') !== false)) {
						$this->data['receiver'][str_replace($this->data['admin_setting_full_prefix'] . '_', '', $key)] = $value;
					}
				}

				if(!isset($this->data['receiver']) || !isset($this->data['receiver']['pp_address'])) {
					$this->data['errors'][] = sprintf($this->data['error_admin_info'], $this->url->link('information/contact', '', 'SSL'));
				}
			}

			$seller_settings = MsLoader::getInstance()->MsSetting->getSellerSettings(array(
				'seller_id' => $this->data['seller_id'],
				'code' => $this->data['seller_setting_full_prefix']
			));

			if(empty($seller_settings)) {
				$this->data['errors'][] = sprintf($this->data['error_sender_data'], $this->url->link('seller/account-setting', '', 'SSL'));
			} else {
				foreach ($seller_settings as $key => $value) {
					$this->data['sender'][str_replace($this->data['seller_setting_full_prefix'] . '_', '', $key)] = $value;
				}

				if(!isset($this->data['sender']) || !$this->_validate($this->data['sender'])) {
					$this->data['errors'][] = sprintf($this->data['error_sender_data'], $this->url->link('seller/account-setting', '', 'SSL'));
				}
			}
		}

		$this->response->setOutput(json_encode($this->load->view('multimerch/payment/' . $this->name . '_payment_form.tpl', $this->data)));
	}

	public function jxSavePayment() {
		if($this->config->get('ms_pg_paypal_debug'))
			$this->_log->write('Start Seller PayPal payment flow...');

		$json = array();
		$data = $this->request->post;

		if (!$data['receiver_data']) {
			if($this->config->get('ms_pg_paypal_debug'))
				$this->_log->write($this->language->get('ms_pg_payment_error_receiver_data'));

			$json['errors'][] = $this->language->get('ms_pg_payment_error_receiver_data');
		}

		if(empty($json['errors'])) {
			$payment_description = html_entity_decode($data['payment_description']);

			// Create payment record with incomplete status
			$payment_id = $this->MsLoader->MsPgPayment->createPayment(array(
				'seller_id' => $data['seller_id'],
				'payment_type' => MsPgPayment::TYPE_PAID_REQUESTS,
				'payment_code' => $data['payment_method'],
				'payment_status' => MsPgPayment::STATUS_INCOMPLETE,
				'amount' => $data['total_amount'],
				'currency_id' => $this->currency->getId($this->config->get('config_currency')),
				'currency_code' => $this->config->get('config_currency'),
				'sender_data' => $data['sender_data'],
				'receiver_data' => array(MsPgPayment::ADMIN_ID => $data['receiver_data']),
				'description' => $payment_description
			));

			if($payment_id) {
				if($this->config->get('ms_pg_paypal_debug'))
					$this->_log->write('Payment #' . $payment_id . ' was successfully created');

				$payment_description = (array)json_decode($payment_description);
				$item_name = '';
				foreach ($payment_description as $request_id => $value) {
					$item_name .= strip_tags($value);

					// Bind payment_id to requests
					$this->MsLoader->MsPgRequest->updateRequest($request_id, array(
						'payment_id' => $payment_id
					));

					if($this->config->get('ms_pg_paypal_debug'))
						$this->_log->write('Payment Request #' . $request_id . ' was successfully updated');
				}

				// render paypal form
				$this->data['payment_data'] = array(
					'sandbox' => $this->config->get($this->data['admin_setting_full_prefix'] . '_s_sandbox'),
					'action' => $this->config->get($this->data['admin_setting_full_prefix'] . '_s_sandbox') ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
					'business' => $data['receiver_data']['pp_address'],
					'item_name' => $item_name,
					'amount' => $this->currency->format($data['total_amount'], $this->config->get('config_currency'), '', FALSE),
					'currency_code' => $this->config->get('config_currency'),
					'return' => $this->url->link('seller/account-payment'),
					'cancel_return' => $this->url->link('seller/account-payment-request'),
					'notify_url' => HTTP_SERVER . 'index.php?route=multimerch/payment/paypal/paypalIPN',
					'custom' => $payment_id
				);

				if($this->config->get('ms_pg_paypal_debug'))
					$this->_log->write('PayPal Standard payment form data: ' . print_r($this->data['payment_data'], true));

				list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multimerch/payment/paypal_standard_payment_form');
				$json['pp_form'] = $this->load->view($template, $this->data);
				$json['success'] = 1;
			} else {
				if($this->config->get('ms_pg_paypal_debug'))
					$this->_log->write($this->language->get('ms_pg_payment_error_payment'));

				$json['errors'][] = $this->language->get('ms_pg_payment_error_payment');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function paypalIPN() {
		if($this->config->get('ms_pg_paypal_debug')) {
			$paypalResponse = @file_get_contents('php://input');

			$this->_log->write('PayPal IPN received: ' . print_r($paypalResponse, true));
		}

		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$payment_id = isset($this->request->post['custom']) ? (int)$this->request->post['custom'] : 0;

		if ($payment_id <= 0) {
			if($this->config->get('ms_pg_paypal_debug'))
				$this->_log->write("MMERCH PP PAYMENT #$payment_id: Invalid or no payment id received");

			return $this->log->write("MMERCH PP PAYMENT #$payment_id: Invalid or no payment id received");
		}

		$payment = $this->MsLoader->MsPgPayment->getPayments(array(
			'payment_id' => $payment_id,
			'single' => 1
		));

		if (!$payment) {
			if($this->config->get('ms_pg_paypal_debug'))
				$this->_log->write("MMERCH PP PAYMENT #$payment_id: Invalid payment id received");

			return $this->log->write("MMERCH PP PAYMENT #$payment_id: Invalid payment id received");
		}

		if($this->config->get('ms_pg_paypal_debug'))
			$this->_log->write('Validating response...');
		$response = $this->_validateResponse();

		if (!$response) {
			if($this->config->get('ms_pg_paypal_debug'))
				$this->_log->write("MMERCH PP PAYMENT #$payment_id: CURL failed");

			return $this->log->write("MMERCH PP PAYMENT #$payment_id: CURL failed");
		}

		if ($response == 'INVALID') {
			if($this->config->get('ms_pg_paypal_debug'))
				$this->_log->write("MMERCH PP PAYMENT #$payment_id: IPN response INVALID");

			return $this->log->write("MMERCH PP PAYMENT #$payment_id: IPN response INVALID");
		}

		if ($response == 'VERIFIED' && isset($this->request->post['payment_status'])) {
			if($this->config->get('ms_pg_paypal_debug'))
				$this->_log->write("MMERCH PP PAYMENT #$payment_id: Response verified. Payment status is " . $this->request->post['payment_status']);

			switch($this->request->post['payment_status']) {
				case 'Completed':
					if($this->config->get('ms_pg_paypal_debug'))
						$this->_log->write("Proccessing Completed payment...");

					// check amount
					if ((float)$this->request->post['mc_gross'] != $this->currency->format($payment['amount'], $payment['currency_code'], 1, false)) {
						if($this->config->get('ms_pg_paypal_debug'))
							$this->_log->write("MMERCH PP PAYMENT #$payment_id:  IPN amount mismatch");

						return $this->log->write("MMERCH PP PAYMENT #$payment_id: IPN amount mismatch");
					}

					$requests = $this->MsLoader->MsPgRequest->getRequests(array(
						'payment_id' => $payment_id
					));

					foreach ($requests as $request) {
						switch($request['request_type']) {
							case MsPgRequest::TYPE_SIGNUP:
								$receiver_address = strtolower($this->config->get($this->data['admin_setting_full_prefix'] . '_pp_address'));
								if(!$receiver_address) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP PAYMENT #$payment_id: No seller PayPal address");

									return $this->log->write("MMERCH PP PAYMENT #$payment_id: No seller PayPal address");
								}

								if ((strtolower($this->request->post['receiver_email']) != $receiver_address)) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP PAYMENT #$payment_id: IPN receiver email mismatch");

									return $this->log->write("MMERCH PP PAYMENT #$payment_id: IPN receiver email mismatch");
								}

								$seller_id = $request['seller_id'];
								if ($seller_id <= 0) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP SIGNUP #$payment_id: Invalid or no seller id for this payment");

									return $this->log->write("MMERCH PP SIGNUP #$payment_id: Invalid or no seller id for this payment");
								}

								if($this->config->get('ms_pg_paypal_debug'))
									$this->_log->write("Updating signup request for seller # " . $seller_id);

								switch ($this->config->get('msconf_seller_validation')) {
									case MsSeller::MS_SELLER_VALIDATION_APPROVAL:
										if($this->config->get('ms_pg_paypal_debug'))
											$this->_log->write("Seller # " . $seller_id . " is avaiting moderation!");

										$MailSellerAwaitingModeration = $serviceLocator->get('MailSellerAwaitingModeration', false)
											->setTo($this->registry->get('customer')->getEmail())
											->setData(array('addressee' => $this->registry->get('customer')->getFirstname()));
										$mails->add($MailSellerAwaitingModeration);

										$this->MsLoader->MsSeller->changeStatus($request['seller_id'], MsSeller::STATUS_INACTIVE);
										$this->MsLoader->MsSeller->changeApproval($seller_id, 0);
										break;

									case MsSeller::MS_SELLER_VALIDATION_NONE:
									default:
										if($this->config->get('ms_pg_paypal_debug'))
											$this->_log->write("Seller # " . $seller_id . " was validated!");

										$MailSellerAccountCreated = $serviceLocator->get('MailSellerAccountCreated', false)
											->setTo($this->registry->get('customer')->getEmail())
											->setData(array('addressee' => $this->registry->get('customer')->getFirstname()));
										$mails->add($MailSellerAccountCreated);

										$this->MsLoader->MsSeller->changeStatus($request['seller_id'], MsSeller::STATUS_ACTIVE);
										$this->MsLoader->MsSeller->changeApproval($request['seller_id'], 1);
										break;
								}

								if($this->config->get('ms_pg_paypal_debug'))
									$this->_log->write("Sending emails...");

								$mailTransport->sendMails($mails);
								break;

							case MsPgRequest::TYPE_LISTING:
								$receiver_address = strtolower($this->config->get($this->data['admin_setting_full_prefix'] . '_pp_address'));
								if(!$receiver_address) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP PAYMENT #$payment_id: No seller PayPal address");

									return $this->log->write("MMERCH PP PAYMENT #$payment_id: No seller PayPal address");
								}

								if ((strtolower($this->request->post['receiver_email']) != $receiver_address)) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP PAYMENT #$payment_id: IPN receiver email mismatch");

									return $this->log->write("MMERCH PP PAYMENT #$payment_id: IPN receiver email mismatch");
								}

								$product_id = $request['product_id'];
								if ($product_id <= 0) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP LISTING PAYMENT #$payment_id: Invalid or no product id for this payment");

									return $this->log->write("MMERCH PP LISTING PAYMENT #$payment_id: Invalid or no product id for this payment");
								}

								if($this->config->get('ms_pg_paypal_debug'))
									$this->_log->write("Product listing # " . $product_id);

								// change product status
								$seller = $this->MsLoader->MsSeller->getSeller($this->MsLoader->MsProduct->getSellerId($product_id));
								$product = $this->MsLoader->MsProduct->getProduct($product_id);
								$defaultLanguageId = $this->config->get('config_language_id');
								switch ($seller['ms.product_validation']) {
									case MsProduct::MS_PRODUCT_VALIDATION_APPROVAL:
										if($this->config->get('ms_pg_paypal_debug'))
											$this->_log->write("Product # " . $product_id . " is awaiting the approval");

										if ($product['product_approved']) {
											$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
										} else {

											$MailProductAwaitingModeration = $serviceLocator->get('MailProductAwaitingModeration', false)
												->setTo($this->registry->get('customer')->getEmail())
												->setData(array(
													'addressee' => $this->registry->get('customer')->getFirstname(),
													'product_name' => $product['languages'][$defaultLanguageId]['name'],
												));
											$mails->add($MailProductAwaitingModeration);

											if($this->config->get('ms_pg_paypal_debug'))
												$this->_log->write("Sending emails...");

											$mailTransport->sendMails($mails);
										}
										break;

									case MsProduct::MS_PRODUCT_VALIDATION_NONE:
									default:
										if($this->config->get('ms_pg_paypal_debug'))
											$this->_log->write("Product # " . $product_id . " not needed to be approved");

										$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
										$this->MsLoader->MsProduct->approve($product_id);
										break;
								}
								break;

							case MsPgRequest::TYPE_PAYOUT:
								$seller_id = $request['seller_id'];
								if ($seller_id <= 0) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP PAYOUT #$payment_id: Invalid or no seller id for this payment");

									return $this->log->write("MMERCH PP PAYOUT #$payment_id: Invalid or no seller id for this payment");
								}

								if($this->config->get('ms_pg_paypal_debug'))
									$this->_log->write("Payout to seller # " . $seller_id);

								$receiver_address = $this->MsLoader->MsSetting->getSellerSettings(array(
									'seller_id' => $seller_id,
									'name' => $this->data['seller_setting_full_prefix'] . '_pp_address',
									'single' => 1
								));
								if(!$receiver_address) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP PAYMENT #$payment_id: No seller PayPal address");

									return $this->log->write("MMERCH PP PAYMENT #$payment_id: No seller PayPal address");
								}

								if ((strtolower($this->request->post['receiver_email']) != $receiver_address)) {
									if($this->config->get('ms_pg_paypal_debug'))
										$this->_log->write("MMERCH PP PAYMENT #$payment_id: IPN receiver email mismatch");

									return $this->log->write("MMERCH PP PAYMENT #$payment_id: IPN receiver email mismatch");
								}

								if($this->config->get('ms_pg_paypal_debug'))
									$this->_log->write("Adding balance entry...");

								$this->MsLoader->MsBalance->addBalanceEntry(
									$request['seller_id'],
									array(
										'withdrawal_id' => $payment['payment_id'],
										'balance_type' => MsBalance::MS_BALANCE_TYPE_WITHDRAWAL,
										'amount' => -$payment['amount'],
										'description' => $payment['description']
									)
								);
								break;
						}

						if($this->config->get('ms_pg_paypal_debug'))
							$this->_log->write("Updating payment # " . $payment_id);

						// Update payment
						$this->MsLoader->MsPgPayment->updatePayment($payment_id, array(
							'payment_status' => MsPgPayment::STATUS_COMPLETE
						));

						if($this->config->get('ms_pg_paypal_debug'))
							$this->_log->write("Updating request # " . $request['request_id']);

						// Update payment requests
						$this->MsLoader->MsPgRequest->updateRequest($request['request_id'],	array(
							'request_status' => MsPgRequest::STATUS_PAID,
							'date_modified' => 1
						));
					}
					break;

				default:
					if($this->config->get('ms_pg_paypal_debug'))
						$this->_log->write("MMERCH PP PAYMENT #$payment_id: Payment status is not Completed. No actions are applied.");
					break;
			}
		}
		if($this->config->get('ms_pg_paypal_debug'))
			$this->_log->write("End processing Payment # " . $payment_id);
	}

	public function index() {
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
			if(!$data[$setting_name]) {
				$this->error['error_' . $setting_name] = $this->data['error_' . $setting_name];
			}
		}
		return !$this->error;
	}

	private function _validateResponse() {
		$request = 'cmd=_notify-validate';

		foreach ($this->request->post as $key => $value) {
			$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
		}

		if (!$this->config->get($this->data['admin_setting_full_prefix'] . '_s_sandbox')) {
			$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
		} else {
			$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
		}

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		return curl_exec($curl);
	}
}