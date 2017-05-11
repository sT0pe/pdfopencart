<?php
class ControllerMultimerchPaymentBankTransfer extends ControllerMultimerchBase {
	private $version = '1.0';
	private $name = 'bank_transfer';

	private $error = array();

	private $seller_settings = array(
		'fname',
		'lname',
		'bank_name',
		'bank_country',
		'bic',
		'iban'
	);

	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model('setting/setting');

		$this->data = array_merge($this->data, $this->load->language('multimerch/payment/' . $this->name));

		$this->data['name'] = $this->name;
		$this->data['admin_setting_full_prefix'] = MsPgPayment::ADMIN_SETTING_PREFIX . $this->name;
		$this->data['seller_setting_full_prefix'] = MsPgPayment::SELLER_SETTING_PREFIX . $this->name;
	}

	public function jxGetPaymentForm() {
		if(empty($this->request->post) || !isset($this->request->post['pg_code']) || $this->request->post['pg_code'] !== $this->name) {
			$this->data['errors'][] = $this->language->get('ms_pg_payment_error_no_method');
		}

		if(!isset($this->request->post['request_ids'])) {
			$this->data['errors'][] = $this->language->get('ms_pg_payment_error_no_requests');
		}

		if(!isset($this->data['errors'])) {
			$admin_settings = $this->model_setting_setting->getSetting($this->data['admin_setting_full_prefix']);
			if(!empty($admin_settings)) {
				foreach ($admin_settings as $key => $value) {
					if(!(strpos($key, 'payout_enabled') !== false || strpos($key, 'fee_enabled') !== false)) {
						$this->data['admin'][str_replace(MsPgPayment::ADMIN_SETTING_PREFIX . $this->name . '_', '', $key)] = $value;
					}
				}

				if(isset($this->data['admin']['fname']) && isset($this->data['admin']['lname'])) {
					$this->data['admin']['full_name'] = sprintf($this->data['text_full_name'], $this->data['admin']['fname'], $this->data['admin']['lname']);
				}

				if(isset($this->data['admin']['bank_name']) && isset($this->data['admin']['bank_country'])) {
					$this->data['admin']['full_bank_name'] = sprintf($this->data['text_full_bank_name'], $this->data['admin']['bank_name'], $this->data['admin']['bank_country']);
				}
			}

			if(!isset($this->data['admin'])) {
				$this->data['errors'][] = sprintf($this->data['error_admin_info'], $this->url->link('multimerch/payment/' . $this->name, 'token=' . $this->session->data['token'], 'SSL'));
			}

			$request_ids = array_unique($this->request->post['request_ids']);
			$total_amount = 0;
			$payment_description = array();

			$this->data['text_receiver'] .= count($request_ids) > 1 ? 's' : '';

			foreach ($request_ids as $request_id) {
				$request_info = MsLoader::getInstance()->MsPgRequest->getRequests(array('request_id' => $request_id, 'single' => 1));
				$seller_id = $request_info['seller_id'];

				if($seller_id) {
					$seller_info = MsLoader::getInstance()->MsSeller->getSeller($seller_id);

					$seller_settings = MsLoader::getInstance()->MsSetting->getSellerSettings(
						array(
							'seller_id' => $seller_id,
							'code' => $this->data['seller_setting_full_prefix']
						)
					);

					if(empty($seller_settings)) {
						$this->data['errors'][] = sprintf($this->data['error_seller_info'], $this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $seller_id), $seller_info['ms.nickname']);
						continue;
					}

					foreach ($seller_settings as $key => $value) {
						$this->data['sellers'][$seller_id][str_replace(MsPgPayment::SELLER_SETTING_PREFIX . $this->name . '_', '', $key)] = $value;
					}

					foreach ($this->data['sellers'] as &$seller) {
						if(isset($seller['fname']) && isset($seller['lname'])) {
							$seller['full_name'] = sprintf($this->data['text_full_name'], $seller['fname'], $seller['lname']);
						}

						if(isset($seller['bank_name']) && isset($seller['bank_country'])) {
							$seller['full_bank_name'] = sprintf($this->data['text_full_bank_name'], $seller['bank_name'], $seller['bank_country']);
						}
					}

					$total_amount += $request_info['amount'];
					$payment_description[$request_id] = $request_info['description'];

					$this->data['sellers'][$seller_id]['amount'] = $request_info['amount'];
					$this->data['sellers'][$seller_id]['amount_formatted'] = $this->currency->format(abs($request_info['amount']), $this->config->get('config_currency'));
					$this->data['sellers'][$seller_id]['request_id'] = $request_id;
					$this->data['sellers'][$seller_id]['nickname'] = $seller_info['ms.nickname'];
				}
			}

			$this->data['total_amount'] = $total_amount;
			$this->data['total_amount_formatted'] = $this->currency->format(abs($total_amount), $this->config->get('config_currency'));
			$this->data['payment_description'] = htmlspecialchars(json_encode($payment_description));
		}
		$this->response->setOutput(json_encode($this->load->view('multimerch/payment/' . $this->name . '_payment_form.tpl', $this->data)));
	}

	public function jxSave() {
		$json = array();
		$data = $this->request->post;

		if(!isset($data['payment_method']) || !isset($data['payment_type']) || !isset($data['total_amount']) || !isset($data['payment_description'])) {
			$json['errors'][] = $this->language->get('ms_pg_payment_error_payment');
		}

		if(!isset($data['sender_data'])) {
			$json['errors'][] = $this->language->get('ms_pg_payment_error_sender_data');
		}

		if(!isset($data['receiver_data'])) {
			$json['errors'][] = $this->language->get('ms_pg_payment_error_receiver_data');
		}

		if (!isset($json['errors'])) {
			$payment_id = $this->MsLoader->MsPgPayment->createPayment(array(
				'seller_id' => MsPgPayment::ADMIN_ID,
				'payment_type' => $data['payment_type'],
				'payment_code' => $data['payment_method'],
				'payment_status' => MsPgPayment::STATUS_INCOMPLETE,
				'amount' => $data['total_amount'],
				'currency_id' => $this->currency->getId($this->config->get('config_currency')),
				'currency_code' => $this->config->get('config_currency'),
				'sender_data' => $data['sender_data'],
				'receiver_data' => $data['receiver_data'],
				'description' => html_entity_decode($data['payment_description'])
			));

			if ($payment_id) {
				foreach ($data['receiver_data'] as $seller_id => $seller_data) {
					$this->MsLoader->MsPgRequest->updateRequest(
						$seller_data['request_id'],
						array(
							'payment_id' => $payment_id,
							'request_status' => MsPgRequest::STATUS_UNPAID,
							'date_modified' => 1
						)
					);
				}
			}

			$this->session->data['success'] = $this->language->get('ms_success_payment_created');
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxConfirmPayment() {
		$json = array();
		$payment_id = $this->request->post['payment_id'];

		if ($payment_id) {
			// Update payment status
			$this->MsLoader->MsPgPayment->updatePayment($payment_id, array(
				'payment_status' => MsPgPayment::STATUS_COMPLETE,
				'date_created' => 1
			));

			// Get requests
			$requests = $this->MsLoader->MsPgRequest->getRequests(array('payment_id' => $payment_id));

			foreach ($requests as $request) {
				$this->MsLoader->MsPgRequest->updateRequest(
					$request['request_id'],
					array(
						'request_status' => MsPgRequest::STATUS_PAID,
						'date_modified' => 1
					)
				);

				$seller = $this->MsLoader->MsSeller->getSeller($request['seller_id']);

				$balance_type = 0;
				if($request['request_type'] == MsPgRequest::TYPE_SIGNUP) {
					$balance_type = MsBalance::MS_BALANCE_TYPE_SIGNUP;
				} else if($request['request_type'] == MsPgRequest::TYPE_LISTING) {
					$balance_type = MsBalance::MS_BALANCE_TYPE_LISTING;
				} else if($request['request_type'] == MsPgRequest::TYPE_PAYOUT) {
					$balance_type = MsBalance::MS_BALANCE_TYPE_WITHDRAWAL;
				}

				$this->MsLoader->MsBalance->addBalanceEntry(
					$request['seller_id'],
					array(
						'withdrawal_id' => $payment_id,
						'balance_type' => $balance_type,
						'amount' => -$request['amount'],
						'description' => sprintf($this->language->get('ms_payment_royalty_payout'), $seller['ms.nickname'], $this->config->get('config_name'))
					)
				);

				if($request['request_type'] == MsPgRequest::TYPE_SIGNUP) {
					$this->MsLoader->MsSeller->changeStatus($request['seller_id'], MsSeller::STATUS_ACTIVE);
				} else if ($request['request_type'] == MsPgRequest::TYPE_LISTING) {
					$this->MsLoader->MsProduct->changeStatus($request['product_id'], MsSeller::STATUS_ACTIVE);
				}
			}

			$json['success'] = $this->language->get('ms_pg_payment_status_' . MsPgPayment::STATUS_COMPLETE);
		} else {
			$json['error'] = $this->language->get('error_no_payment_id');
		}

		$this->response->setOutput(json_encode($json));
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

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateAccess()) {
			$settings = array();
			foreach ($this->request->post as $key => $value) {
				$settings[$this->data['admin_setting_full_prefix'] . '_' . $key] = $value;
			}
			$this->model_setting_setting->editSetting($this->data['admin_setting_full_prefix'], $settings);
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('multimerch/payment-gateway', 'token=' . $this->session->data['token'], true));
		}

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$this->data['error_email'] = isset($this->error['email']) ? $this->error['email'] : '';

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

		// General
		if (isset($this->request->post['fname'])) {
			$this->data['fname'] = $this->request->post['fname'];
		} else {
			$this->data['fname'] = $this->config->get($this->data['admin_setting_full_prefix'] . '_fname');
		}

		if (isset($this->request->post['lname'])) {
			$this->data['lname'] = $this->request->post['lname'];
		} else {
			$this->data['lname'] = $this->config->get($this->data['admin_setting_full_prefix'] . '_lname');
		}

		if (isset($this->request->post['bank_name'])) {
			$this->data['bank_name'] = $this->request->post['bank_name'];
		} else {
			$this->data['bank_name'] = $this->config->get($this->data['admin_setting_full_prefix'] . '_bank_name');
		}

		if (isset($this->request->post['bic'])) {
			$this->data['bic'] = $this->request->post['bic'];
		} else {
			$this->data['bic'] = $this->config->get($this->data['admin_setting_full_prefix'] . '_bic');
		}

		if (isset($this->request->post['iban'])) {
			$this->data['iban'] = $this->request->post['iban'];
		} else {
			$this->data['iban'] = $this->config->get($this->data['admin_setting_full_prefix'] . '_iban');
		}

		// Заменить на селект из ос_кантри
		if (isset($this->request->post['bank_country'])) {
			$this->data['bank_country'] = $this->request->post['bank_country'];
		} else {
			$this->data['bank_country'] = $this->config->get($this->data['admin_setting_full_prefix'] . '_bank_country');
		}

		// Fee
		if (isset($this->request->post['fee_enabled'])) {
			$this->data['fee_enabled'] = $this->request->post['fee_enabled'];
		} else {
			$this->data['fee_enabled'] = $this->config->get($this->data['admin_setting_full_prefix'] . '_fee_enabled');
		}

		// Payout
		if (isset($this->request->post['payout_enabled'])) {
			$this->data['payout_enabled'] = $this->request->post['payout_enabled'];
		} else {
			$this->data['payout_enabled'] = $this->config->get($this->data['admin_setting_full_prefix'] . '_payout_enabled');
		}

		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('multimerch/payment/' . $this->name, $this->data));
	}

	protected function _validateAccess() {
		if (!$this->user->hasPermission('modify', 'multimerch/payment/' . $this->name)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}