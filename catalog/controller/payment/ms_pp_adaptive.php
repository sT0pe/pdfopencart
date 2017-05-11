<?php
class ControllerPaymentMSPPAdaptive extends Controller {
	private $data = array();
	private $_log;
	private $_paypal;

	public function __construct($registry) {
		parent::__construct($registry);
		if ($this->config->get('msppaconf_ppa_log_filename')){
			$this->_log = new Log($this->config->get('msppaconf_ppa_log_filename'));
		}else{
			$this->_log = new Log("ppa_paypal.log");
		}

		require_once(DIR_SYSTEM . 'library/multimerch/payment/paypal.php');
		
		if ($this->config->get('msppaconf_sandbox')) {
			$endPoint = "https://svcs.sandbox.paypal.com/AdaptivePayments/";
		} else {
			$endPoint = "https://svcs.paypal.com/AdaptivePayments/";
		}

		$this->_paypal = new PayPal($this->config->get('msppaconf_api_username'), $this->encryption->decrypt($this->config->get('msppaconf_api_password')), $this->encryption->decrypt($this->config->get('msppaconf_api_signature')), $this->config->get('msppaconf_sandbox'), $endPoint, $this->config->get('msppaconf_api_appid'));
		//$this->_paypal = new PayPal($this->config->get('msppaconf_api_username'), $this->encryption->decrypt($this->config->get('msppaconf_api_password')), $this->encryption->decrypt($this->config->get('msppaconf_api_signature')), $this->config->get('msppaconf_api_appid'), $this->config->get('msppaconf_sandbox'));
	}
	
	public function index() {
		$this->data = array_merge($this->data, $this->load->language('payment/ms_pp_adaptive'));
		$this->data['sandbox'] = $this->config->get('msppaconf_sandbox');		
		$this->data['button_confirm'] = $this->language->get('button_confirm');

		if($this->config->get('msppaconf_payment_type') == 'PREAPPROVAL'){
			$this->data['continue'] = $this->url->link('payment/ms_pp_adaptive/preapproval_call');
		}else{
			$this->data['continue'] = $this->url->link('payment/ms_pp_adaptive/send');
		}


		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/payment/ms_pp_adaptive.tpl')) {
			$template = MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/payment/ms_pp_adaptive.tpl';
		} else {
			$template = 'default/template/payment/ms_pp_adaptive.tpl';
		}

		return $this->load->view($template, $this->data);
	}
	
	public function callback() {
		$response = @file_get_contents('php://input');

		$this->_log->write('IPN callback received: ' . $response . print_r($this->request->get,true));
		
		$key = $this->encryption->encrypt($this->config->get('msppaconf_secret_key'));
		$value = $this->encryption->encrypt($this->config->get('msppaconf_secret_value'));

		//$this->_log->write($key . ' ' . $value);		
		//$this->_log->write($this->encryption->decrypt($this->request->get[$this->encryption->encrypt('secre  t')]));
		
		if ($this->config->get('msppaconf_debug'))
			$this->_log->write('IPN callback received: ' . $response);

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {
			$message = '';
			if (!isset($this->request->get[$key]) || $this->request->get[$key] != $value) {
				$this->_log->write('IPN callback error: shared secret validation failed');
				$message = $response;
				//$this->model_checkout_order->update($order_id, $this->config->get('msppaconf_error_status_id'), $message, false);
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('msppaconf_error_status_id'), $message, false);
			} else if ($this->_paypal->validateIPN($this->config->get('msppaconf_ppa_log_filename'))) {
				if ($this->config->get('msppaconf_debug'))
					$this->_log->write('PayPal IPN Verified for order ID' . $order_id . ', payment status: ' . $this->request->post['status']);

				$order_status_id = $this->config->get('config_order_status_id');

				switch(strtolower($this->request->post['status'])) {
					case 'completed':
						$err = false;
						$paypalResponse = $this->_paypal->decodePayPalIPN(file_get_contents('php://input'));

						$ms_requests = $this->MsLoader->MsPgRequest->getRequests(array('order_id' => $order_id));
						$payments = array();
						foreach ($ms_requests as $request) {
							$payments[] = $this->MsLoader->MsPgPayment->getPayments(array(
								'payment_id' => $request['payment_id'],
								'single' => 1
							));
						}

						$this->_log->write('Payment requests: ' . print_r($ms_requests, true));
						$this->_log->write('Payments: ' . print_r($payments, true));

						$p = array();
						foreach ($payments as $payment) {
							$receiver = json_decode($payment['receiver_data']);
							$receiver = (array)$receiver;
							$p[strtolower($receiver['pp_address'])] = $payment;
						}

						$this->_log->write('Order data: ' . print_r($paypalResponse, true) . print_r($order_info, true) . print_r($p, true));

						foreach ($paypalResponse['transaction'] as $trn) {
							$payment = isset($p[strtolower($trn['receiver'])]) ? $p[strtolower($trn['receiver'])] : false; 
							
							$this->_log->write('Order debug: ' . print_r($trn, true) . print_r($payment, true));
							
							if (!$payment) {
								$this->_log->write('Payment receiver validation error');
								$err = true;
								break;
							} else {
								// required since pp returns it as a string
								preg_match('!\d+(?:\.\d+)?!', $trn['amount'], $matches);
								$this->_log->write('Amount debug: ' . print_r($matches, true) . print_r($payment, true) . $this->currency->format($payment['amount'], $payment['currency_code'], $order_info['currency_value'], false));
								
								// primary chained payment must equal order total
								if ($trn['is_primary_receiver'] == 'true') $payment['amount'] = $order_info['total'];
								
								if ((float)$matches[0] != $this->currency->format($payment['amount'], $payment['currency_code'], $order_info['currency_value'], false)) {
									$this->_log->write('Payment amount validation error');
									$err = true;
									break;
								}
							}
						}
						
						if ($err) {
							$order_status_id = $this->config->get('msppaconf_error_status_id');
							break;
						} else {
							foreach($paypalResponse['transaction'] as $trn) {
								$payment = $p[strtolower($trn['receiver'])];

								// Get requests binded to the payment and update their status
								$requests_to_update = $this->MsLoader->MsPgRequest->getRequests(array('payment_id' => $payment['payment_id']));
								foreach ($requests_to_update as $request) {
									$this->MsLoader->MsPgRequest->updateRequest($request['request_id'], array(
										'request_status' => MsPgRequest::STATUS_PAID,
										'date_modified' => 1
									));
								}

								$this->MsLoader->MsPgPayment->updatePayment($payment['payment_id'], array(
									'payment_status' => MsPgPayment::STATUS_COMPLETE,
									'receiver_data' => print_r($trn),
									'date_created' => 1
								));
							}
							
							$order_status_id = $this->config->get('msppaconf_completed_status_id');
						}
						
						break;
					case 'pending':
					case 'processing':
					case 'incomplete':					
						$order_status_id = $this->config->get('msppaconf_pending_status_id');
						$message = $response;
						break;

					case 'error':
					case 'reversal error':
					default:
						$order_status_id = $this->config->get('msppaconf_error_status_id');
						$message = $response;
						break;
				}
				
				if (!$order_info['order_status_id']) {
					if ($this->config->get('msppaconf_debug'))
						$this->_log->write('Confirming order #' . $order_id . ' with status id ' . $order_status_id);
						
					//$this->model_checkout_order->confirm($order_id, $order_status_id, $message, false);
					$this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $message, false);
				} else {
					if ($this->config->get('msppaconf_debug'))
						$this->_log->write('Updating order #' . $order_id . ' with status id ' . $order_status_id);
						
					//$this->model_checkout_order->update($order_id, $order_status_id, $message, false);
					$this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $message, false);
				}
			} else {
				$this->_log->write('IPN callback error: PayPal IPN Validation failed');
				$message = $response;
				$this->model_checkout_order->update($order_id, $this->config->get('msppaconf_error_status_id'), $message, false);
			}
		} else {
			$this->_log->write('IPN callback error: No order ID or wrong order ID specified');
		}
	}

	public function preapproval_call() {
		$json = array();
		if ($this->customer->isLogged()) {
			$pp_preapprovalkey = $this->MsLoader->MsHelper->getPpreapprovalkey((int)$this->customer->getId());
			if($pp_preapprovalkey AND $pp_preapprovalkey['preapprovalkey']){
				$json['redirect'] = $this->url->link('payment/ms_pp_adaptive/preapproval_send') . '&preapprovalkey=' . $pp_preapprovalkey['preapprovalkey'];
			}else{
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
				$requestParams = array(
					'startingDate' =>  date('Y-m-d'),
					'currencyCode' => $order_info['currency_code'],
					'returnUrl' => $this->url->link('checkout/success'),
					'cancelUrl' => $this->url->link('checkout/checkout'),
					'ipnNotificationUrl' => $this->url->link('payment/ms_pp_adaptive/preapproval_callback', '', 'SSL') . '&order_id=' . $order_info['order_id'] . '&customer_id=' . (int)$this->customer->getId(),
					'requestEnvelope' => 'en_US'
				);

				if ($this->config->get('msppaconf_sandbox')) {
					$request_url = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Preapproval';
				} else {
					$request_url = 'https://svcs.paypal.com/AdaptivePayments/Preapproval';
				}

				$response = $this->_paypal->preapproval_request($requestParams,$request_url);
				if ($this->config->get('msppaconf_debug'))
					$this->_log->write('Received Preapproval PayPal preapprovalkey Response: ' . print_r($response, true));
				if (isset($response['preapprovalKey']) AND $response['preapprovalKey']){
					$json['redirect'] = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_ap-preapproval&preapprovalkey=".$response['preapprovalKey'];
				}else{
					$json['error'] = "PayPal Preapproval Request Error. Order ID {$order_info['order_id']}";
					$this->_log->write("PayPal Preapproval Request Error: " . print_r($response, true));
				}
			}
		}else{
			$json['redirect'] = $this->url->link('account/login');
		}

		return $this->response->setOutput(json_encode($json));
	}

	public function preapproval_send() {
		$this->language->load('payment/ms_pp_adaptive');
		$this->load->model('checkout/order');
		$this->load->model('account/order');

		if (!$this->customer->isLogged()){
			$this->_log->write('PayPal Preapproval Adaptive error: customer is not logged');
			$this->response->redirect($this->url->link('checkout/checkout'));
		}

		if (!isset($this->session->data['order_id'])) {
			$this->_log->write('PayPal Preapproval Adaptive error: No order ID specified');
			$this->response->redirect($this->url->link('checkout/checkout'));
		}

		if (!isset($this->request->get['preapprovalkey'])) {
			$this->_log->write('PayPal Preapproval Adaptive error: No preapprovalkey specified');
			$this->response->redirect($this->url->link('checkout/checkout'));
		}

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if (!$order_info) {
			$this->_log->write('PayPal Preapproval Adaptive error: Invalid order ID');
			$this->response->redirect($this->url->link('checkout/checkout'));
		}

		if ($this->config->get('msppaconf_debug'))
			$this->_log->write("Initializing preapproval payment for order ID {$order_info['order_id']}");

		$requestParams = array(
			'actionType' => 'PAY',
			'currencyCode' => $order_info['currency_code'],
			'feesPayer' => 'EACHRECEIVER',
			'returnUrl' => $this->url->link('checkout/success'),
			'cancelUrl' => $this->url->link('checkout/checkout', '', 'SSL'),
			'requestEnvelope.errorLanguage' => 'en_US',
			'preapprovalKey' => $this->request->get['preapprovalkey']
		);

		$paymentParams = $this->_paymentParams($requestParams,$order_info);

		if(isset($paymentParams['receivers']) AND $paymentParams['receivers']){
			$paymentData = $this->_createPaymentData($paymentParams['receivers'],$order_info);
			$pg_requests = $paymentData['pg_requests'];
			$pg_payments = $paymentData['pg_payments'];
			unset($paymentParams['receivers']);
		}else{
			$this->_log->write('PayPal Preapproval Adaptive error: receivers not found');
			$this->response->redirect($this->url->link('checkout/checkout'));
		}

		$response = $this->_paypal->request('Pay',$requestParams + $paymentParams);

		if (!$response) {
			$this->_log->write("PayPal Preapproval Request Error. Order ID {$order_info['order_id']}: " . $this->_paypal->getErrors());
			$this->response->redirect($this->url->link('checkout/checkout'));
		} else if (isset($response['responseEnvelope_ack']) && $response['responseEnvelope_ack'] != 'Success') {
			$this->_log->write("PayPal Preapproval Request Error: " . print_r($response, true));

			// Delete created MM payment requests and payments
			foreach ($pg_requests as $request_id) {
				$this->MsLoader->MsPgRequest->deleteRequest($request_id);
			}
			foreach ($pg_payments as $payment_id) {
				$this->MsLoader->MsPgPayment->deletePayment($payment_id);
			}
			$this->response->redirect($this->url->link('checkout/checkout'));

		} else if (isset($response['paymentExecStatus'])) {

			if ($this->config->get('msppaconf_debug'))
				$this->_log->write('Received Preapproval PayPal Response: ' . print_r($response, true));

			switch ($response['paymentExecStatus']) {
				case "CREATED":
				case "COMPLETED":
				case "PROCESSING":
				case "PENDING":

					$message = 'payKey: ' . $response['payKey'] . "\n";
					$message .= 'Envelope_correlationId: ' . $response['responseEnvelope_correlationId'] . "\n";
					$message .= 'paymentExecStatus: ' . $response['paymentExecStatus'] . "\n";

					$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('msppaconf_pending_status_id'), $message, false);
					$this->_log->write('Updating order #' . $order_info['order_id'] . ' with status id ' . $this->config->get('msppaconf_pending_status_id'));
					break;

				case "INCOMPLETE":
				case "ERROR":
				case "REVERSALERROR":
				default:
					$this->_log->write("PayPal Preapproval Response Error: " . print_r($response, true));
					$this->response->redirect($this->url->link('checkout/checkout'));
			}
		} else {
			$this->_log->write("PayPal Preapproval Request Error: " . print_r($response, true));

			// Delete created MM payment requests and payments
			foreach ($pg_requests as $request_id) {
				$this->MsLoader->MsPgRequest->deleteRequest($request_id);
			}
			foreach ($pg_payments as $payment_id) {
				$this->MsLoader->MsPgPayment->deletePayment($payment_id);
			}

			$this->response->redirect($this->url->link('checkout/checkout'));
		}

		$this->response->redirect($this->url->link('checkout/success'));
	}

	public function preapproval_callback() {
		$response = @file_get_contents('php://input');
		if ($this->config->get('msppaconf_debug'))
		$this->_log->write('IPN callback received: ' . $response . print_r($this->request->get,true) .  print_r($this->request->post,true));

		$this->language->load('payment/ms_pp_adaptive');
		$this->load->model('checkout/order');

		if (!isset($this->request->get['order_id'])) {
			$this->_log->write('PayPal Preapproval Adaptive error: No order ID specified');
		}

		if (!isset($this->request->get['customer_id']) OR !$this->request->get['customer_id']) {
			$this->_log->write('PayPal Preapproval Adaptive error: No customer ID specified');
		}

		if (!isset($this->request->post['preapproval_key'])) {
			$this->_log->write('PayPal Preapproval Adaptive error: No order ID specified');
		}

		$order_info = $this->model_checkout_order->getOrder($this->request->get['order_id']);

		if (!$order_info) {
			$this->_log->write('PayPal Preapproval Adaptive error: Invalid order ID');
		}

		if ($this->config->get('msppaconf_debug'))
			$this->_log->write("Initializing preapproval payment for order ID {$order_info['order_id']}");

		$requestParams = array(
			'actionType' => 'PAY',
			'currencyCode' => $order_info['currency_code'],
			'feesPayer' => 'EACHRECEIVER',
			'returnUrl' => $this->url->link('checkout/success'),
			'cancelUrl' => $this->url->link('checkout/checkout', '', 'SSL'),
			'requestEnvelope.errorLanguage' => 'en_US',
			'preapprovalKey' => $this->request->post['preapproval_key']
		);

		if ($this->config->get('msppaconf_debug')) $this->_log->write("PayPal Preapproval Request Params: " . print_r($requestParams, true));

		$paymentParams = $this->_paymentParams($requestParams,$order_info);

		if ($this->config->get('msppaconf_debug')) $this->_log->write("PayPal Preapproval Payment Params crated: " . print_r($paymentParams, true));

		if(isset($paymentParams['receivers']) AND $paymentParams['receivers']){
			$paymentData = $this->_createPaymentData($paymentParams['receivers'],$order_info);
			if ($this->config->get('msppaconf_debug')) $this->_log->write("PayPal Preapproval Payment Data crated: " . print_r($paymentData, true));
			$pg_requests = $paymentData['pg_requests'];
			$pg_payments = $paymentData['pg_payments'];
			unset($paymentParams['receivers']);
		}else{
			$this->_log->write('PayPal Preapproval Adaptive error: receivers not found');
		}
		$response = $this->_paypal->request('Pay',$requestParams + $paymentParams,$this->config->get('msppaconf_ppa_log_filename'));
		if (!$response) {
			$this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('msppaconf_error_status_id'), 'no response', false);
			$this->_log->write("PayPal Preapproval Request Error. Order ID {$order_info['order_id']}: " . $this->_paypal->getErrors());
		} else if (isset($response['responseEnvelope_ack']) && $response['responseEnvelope_ack'] != 'Success') {
			$this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('msppaconf_error_status_id'), 'response no Success', false);
			$this->_log->write("PayPal Preapproval Request Error: " . print_r($response, true));

			// Delete created MM payment requests and payments
			foreach ($pg_requests as $request_id) {
				$this->MsLoader->MsPgRequest->deleteRequest($request_id);
			}
			foreach ($pg_payments as $payment_id) {
				$this->MsLoader->MsPgPayment->deletePayment($payment_id);
			}

		} else if (isset($response['paymentExecStatus'])) {

			if ($this->config->get('msppaconf_debug'))
				$this->_log->write('Received Preapproval PayPal Response: ' . print_r($response, true));

			switch ($response['paymentExecStatus']) {
				case "CREATED":
				case "COMPLETED":
				case "PROCESSING":
				case "PENDING":

					$message = 'payKey: ' . $response['payKey'] . "\n";
					$message .= 'Envelope_correlationId: ' . $response['responseEnvelope_correlationId'] . "\n";
					$message .= 'paymentExecStatus: ' . $response['paymentExecStatus'] . "\n";

					$this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('msppaconf_pending_status_id'), $message, false);
					$this->MsLoader->MsHelper->addPpreapprovalkey($this->request->post['preapproval_key'], $this->request->get['customer_id']);
					$this->_log->write('Updating order #' . $order_info['order_id'] . ' with status id ' . $this->config->get('msppaconf_pending_status_id'));
					break;

				case "INCOMPLETE":
				case "ERROR":
				case "REVERSALERROR":
				default:
					$this->_log->write("PayPal Preapproval Response Error: " . print_r($response, true));
					$message="Payment Exec Status: " . $response['paymentExecStatus'];
					$this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('msppaconf_error_status_id'), $message, false);
					$this->_log->write('Updating order #' . $order_info['order_id'] . ' with status id ' . $this->config->get('msppaconf_error_status_id'));
			}
		} else {
			$this->_log->write("PayPal Preapproval Request Error: " . print_r($response, true));
			$this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('msppaconf_error_status_id'), '', false);
			// Delete created MM payment requests and payments
			foreach ($pg_requests as $request_id) {
				$this->MsLoader->MsPgRequest->deleteRequest($request_id);
			}
			foreach ($pg_payments as $payment_id) {
				$this->MsLoader->MsPgPayment->deletePayment($payment_id);
			}
		}
	}


	public function send() {
		$json = array();
		$this->language->load('payment/ms_pp_adaptive');
		$this->load->model('checkout/order');
		$this->load->model('account/order');
		
		if (!isset($this->session->data['order_id'])) {
			$this->_log->write('PayPal Adaptive error: No order ID specified');
			return;
		}

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		if (!$order_info) {
			$this->_log->write('PayPal Adaptive error: Invalid order ID');
			return;
		}
		
		if ($this->config->get('msppaconf_debug'))
			$this->_log->write("Initializing payment for order ID {$order_info['order_id']}");
		
		$requestParams = array(
			'actionType' => 'PAY',
			'feesPayer' => $this->config->get('msppaconf_feespayer'),
			'reverseAllParallelPaymentsOnError' => 'true', 			
			'currencyCode' => $order_info['currency_code'],
			'returnUrl' => $this->url->link('checkout/success'),
			'ipnNotificationUrl' => $this->url->link('payment/ms_pp_adaptive/callback', '', 'SSL') . '&order_id=' . $order_info['order_id'] . '&' . $this->encryption->encrypt($this->config->get('msppaconf_secret_key')) . '=' . $this->encryption->encrypt($this->config->get('msppaconf_secret_value')),
			'cancelUrl' => $this->url->link('checkout/checkout', '', 'SSL'),
			'requestEnvelope.errorLanguage' => 'en_US',
		);

		$paymentParams = $this->_paymentParams($requestParams,$order_info);
		$paymentData = $this->_createPaymentData($paymentParams['receivers'],$order_info);
		$pg_requests = $paymentData['pg_requests'];
		$pg_payments = $paymentData['pg_payments'];
		unset($paymentParams['receivers']);

		$response = $this->_paypal->request('Pay',$requestParams + $paymentParams);

		if (!$response) {
			$this->_log->write("PayPal Request Error. Order ID {$order_info['order_id']}: " . $this->_paypal->getErrors());
			$json['error'] = "PayPal Request Error. Order ID {$order_info['order_id']}";			
		} else if (isset($response['responseEnvelope_ack']) && $response['responseEnvelope_ack'] != 'Success') {
			$this->_log->write("PayPal Request Error: " . print_r($response, true));

			// Delete created MM payment requests and payments
			foreach ($pg_requests as $request_id) {
				$this->MsLoader->MsPgRequest->deleteRequest($request_id);
			}
			foreach ($pg_payments as $payment_id) {
				$this->MsLoader->MsPgPayment->deletePayment($payment_id);
			}

			$json['error'] = sprintf($this->language->get('ppa_error_request'), $response['responseEnvelope_correlationId']);
		} else if (isset($response['paymentExecStatus'])) {
			if ($this->config->get('msppaconf_debug'))
				$this->_log->write('Received PayPal Response: ' . print_r($response, true));
				
			switch ($response['paymentExecStatus']) {
				case "CREATED":
				case "COMPLETED":
				case "PROCESSING":
				case "PENDING":
					if (!$this->config->get('msppaconf_sandbox')) {
						$json['redirect'] = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey={$response['payKey']}";
					} else {
						$json['redirect'] = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey={$response['payKey']}";
					}
					break;
				case "INCOMPLETE":
				case "ERROR":
				case "REVERSALERROR":
				default:
					$this->_log->write("PayPal Response Error: " . print_r($response, true));
					$json['error'] = sprintf($this->language->get('ppa_error_response'), $response['paymentExecStatus'], $response['responseEnvelope_correlationId']);
					break;
			}
		} else {
			$this->_log->write("PayPal Request Error: " . print_r($response, true));

			// Delete created MM payment requests and payments
			foreach ($pg_requests as $request_id) {
				$this->MsLoader->MsPgRequest->deleteRequest($request_id);
			}
			foreach ($pg_payments as $payment_id) {
				$this->MsLoader->MsPgPayment->deletePayment($payment_id);
			}

			$json['error'] = sprintf($this->language->get('ppa_error_request'), 'unknown error' . $response['responseEnvelope_correlationId']);
		}

		$this->response->setOutput(json_encode($json));
	}

	private function _paymentParams($requestParams,$order_info){
		$paymentParams = array();
		$receivers = array();

		$this->load->model('account/order');
		// If default opencart shipping is used
		$order_totals = $this->model_account_order->getOrderTotals($order_info['order_id']);
		$oc_shipping_cost = 0;
		$store_shipping = false;
		foreach ($order_totals as $order_total) {
			if($order_total['code'] == 'shipping') {
				$store_shipping = true;
			}
			if($order_total['code'] == 'shipping' || $order_total['code'] == 'tax') {
				$oc_shipping_cost += $order_total['value'];
			}
		}
		$receivers_data = $this->MsLoader->MsTransaction->createMsOrderDataEntries($order_info['order_id']);

		// primary (store)
		$receivers[0] = array();

		//Store Shipping
		if ($store_shipping){
			$receivers[0]['amount'] = $this->currency->format($receivers_data['commission_total'] + $oc_shipping_cost, $order_info['currency_code'], false, false);
			//Vendor Shipping
		}else{
			$receivers[0]['amount'] = $this->currency->format($receivers_data['commission_total'], $order_info['currency_code'], false, false);
		}

		$receivers[0]['ms.paypal'] = $this->config->get('msppaconf_receiver');

		foreach ($receivers_data['sellers'] as $seller_id=>$seller_data){
			$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
			// Get paypal address from "Paypal" payment gateway settings
			$paypal_address = $this->MsLoader->MsSetting->getSellerSettings(array(
				'seller_id' => $seller_id,
				'name' => MsPgPayment::SELLER_SETTING_PREFIX . 'paypal_pp_address',
				'single' => 1
			));
			$receivers[$seller_id] = $seller;
			$receivers[$seller_id]['amount'] = $this->currency->format($seller_data["seller_net_amt_total"]+$seller_data["shipping_cost_total"], $order_info['currency_code'], false, false);
			$receivers[$seller_id]['ms.paypal'] = $paypal_address;
		}



		$toPay = $total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
		$payableAmount = $this->currency->format(0, $order_info['currency_code'], false, false);

		if ($this->config->get('msppaconf_debug')) {
			$this->_log->write("Generating amounts for order ID {$order_info['order_id']}");
			$this->_log->write("Total: $total");
			$this->_log->write("Receivers: " . print_r($receivers, true));
		}

		$msppaconf_payment_type = $this->config->get('msppaconf_payment_type');
		$i = 0;
		if ($msppaconf_payment_type == "CHAINED" OR $msppaconf_payment_type == "PREAPPROVAL") {
			foreach ($receivers as $seller_id => $receiver) {
				if ($seller_id == 0) {
					// primary receiver (store)
					$paymentParams["receiverList.receiver($i).email"] = $this->config->get('msppaconf_receiver');
					$paymentParams["receiverList.receiver($i).amount"] = $total;
					$paymentParams["receiverList.receiver($i).primary"] = "true";
					$toPay -= $total;
					$i++;
				} else {
					// secondary receivers
					if (!empty($receiver['ms.paypal']) && $receiver['amount'] > 0 && filter_var($receiver['ms.paypal'], FILTER_VALIDATE_EMAIL)) {
						$payableAmount += $receiver['amount'];
						$toPay -= $receiver['amount'];
						$paymentParams["receiverList.receiver($i).email"] = $receiver['ms.paypal'];
						$paymentParams["receiverList.receiver($i).amount"] = $receiver['amount'];
						$i++;
					}
				}
			}
		} else if ($msppaconf_payment_type == "PARALLEL") {
			// PARALLEL
			foreach ($receivers as $seller_id => $receiver) {
				if (!empty($receiver['ms.paypal']) && $receiver['amount'] > 0 && filter_var($receiver['ms.paypal'], FILTER_VALIDATE_EMAIL)) {
					$payableAmount += $receiver['amount'];
					$toPay -= $receiver['amount'];
					$paymentParams["receiverList.receiver($i).email"] = $receiver['ms.paypal'];
					$paymentParams["receiverList.receiver($i).amount"] = $receiver['amount'];
					$i++;
				}
			}
		} else if ($msppaconf_payment_type == "SIMPLE") {
			// SIMPLE
		} else {
			//error and log
		}

		if (count($receivers) == 1) $paymentParams["receiverList.receiver(0).primary"] = "true";

		$toPay = $this->currency->format($toPay, $order_info['currency_code'], false, false);

		if ($toPay > 0) {
			if ($this->config->get('msppaconf_debug')) {
				$this->_log->write("Amount distribution error. Please check the receiver addresses. Leftover amount: " . $toPay);
				foreach ($receivers as $seller_id => $receiver) {
					$this->_log->write("Receiver " . $receiver['ms.paypal'] . ": " . $receiver['amount']);
				}
			}

			$json['error'] = sprintf($this->language->get('ppa_error_generic'), $order_info['order_id']);
			return $this->response->setOutput(json_encode($json));
			/*
			$was = $paymentParams["receiverList.receiver(0).amount"];
			$paymentParams["receiverList.receiver(0).amount"] = round(abs($paymentParams["receiverList.receiver(0).amount"] + $toPay), '2', PHP_ROUND_HALF_DOWN);
			$now = $paymentParams["receiverList.receiver(0).amount"];
			if ($this->config->get('msppaconf_debug'))
				$this->_log->write("Adding leftover {$toPay} to the first receiver's amount, was {$was} now {$now}. Order ID {$order_info['order_id']}");
			*/
		}

		if ($this->config->get('msppaconf_debug'))
			$this->_log->write("Final transaction amounts: " . print_r($paymentParams, true));

		if ($this->config->get('msppaconf_debug'))
			$this->_log->write("Creating PayPal Request, Order ID {$order_info['order_id']}: " . print_r($requestParams + $paymentParams, true));

		if ($payableAmount > $total) {
			$this->_log->write("Configuration Error: Invalid Amount Distribution. Order ID: {$order_info['order_id']} Order total: {$total} Payable amount: {$payableAmount}");
			$json['error'] = sprintf($this->language->get('ppa_error_distribution'), $order_info['order_id']);
			$this->response->setOutput(json_encode($json));
			return;
		}

		if (empty($paymentParams)) {
			$this->_log->write("Configuration Error: No valid receivers. Order ID: {$order_info['order_id']}");
			$json['error'] = sprintf($this->language->get('ppa_error_noreceivers'), $order_info['order_id']);
			$this->response->setOutput(json_encode($json));
			return;
		}

		//check unique mails
		$receivers_mails = array();
		$accounts_unique = true;
		foreach ($receivers as $receiver) {
			if (in_array($receiver["ms.paypal"],$receivers_mails)){
				$accounts_unique = false;
			}
			$receivers_mails[] = $receiver["ms.paypal"];
		}
		if (!$accounts_unique) {
			$this->_log->write("Configuration Error: Two or more receivers have the same PayPal address specified. Order ID: {$order_info['order_id']}");
			$json['error'] = sprintf($this->language->get('ppa_error_no_unique_mails'), $order_info['order_id']);
			$this->response->setOutput(json_encode($json));
			return;
		}

		//var_dump($receivers); exit;

		$paymentParams['receivers'] = $receivers;

		return $paymentParams;
	}

	private function _createPaymentData($receivers,$order_info){
		$result['pg_requests'] = array();
		$result['pg_payments'] = array();

		foreach ($receivers as $seller_id => $receiver) {
			// Create payment request with type 'sale' and status 'unpaid'
			$request_id = $this->MsLoader->MsPgRequest->createRequest(array(
				'seller_id' => $seller_id,
				'order_id' => $order_info['order_id'],
				'request_type' => MsPgRequest::TYPE_SALE,
				'request_status' => MsPgRequest::STATUS_UNPAID,
				'description' => sprintf($this->language->get('ms_transaction_order'), $order_info['order_id']),
				'amount' => $receiver['amount'],
				'currency_id' => $this->currency->getId($this->config->get('config_currency')),
				'currency_code' => $this->currency->getCode($this->config->get('config_currency'))
			));
			$result['pg_requests'][] = $request_id;

			// Create payment record with incomplete status
			$payment_id = $this->MsLoader->MsPgPayment->createPayment(array(
				'seller_id' => $seller_id,
				'payment_type' => MsPgPayment::TYPE_SALE,
				'payment_code' => 'ms_pp_adaptive',
				'payment_status' => MsPgPayment::STATUS_INCOMPLETE,
				'amount' => $receiver['amount'],
				'currency_id' => $this->currency->getId($this->config->get('config_currency')),
				'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
				'sender_data' => array(),
				'receiver_data' => array('pp_address' => $receiver['ms.paypal']),
				'description' => array($request_id => sprintf($this->language->get('ms_transaction_order'), $order_info['order_id']))
			));
			$result['pg_payments'][] = $payment_id;

			// Bind payment request to payment
			$this->MsLoader->MsPgRequest->updateRequest(
				$request_id,
				array(
					'payment_id' => $payment_id
				)
			);
		}

		return $result;
	}


}
?>