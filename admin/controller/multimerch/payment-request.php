<?php

class ControllerMultimerchPaymentRequest extends ControllerMultimerchBase {
	public function getTableData() {
		$colMap = array(
			'seller' => 'ms.nickname',
			'type' => 'request_type',
			'description' => 'mpr.description',
			'date_created' => 'mpr.date_created',
			'date_paid' => 'mpr.date_modified'
		);

		$sorts = array('request_type', 'seller', 'amount', 'description', 'request_status', 'date_created', 'date_modified');
		$filters = array_diff($sorts, array('request_status'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsPgRequest->getRequests(
			array(
				'request_type' => array(MsPgRequest::TYPE_PAYOUT)
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
			$columns[] = array(
				'checkbox' => ($result['request_status'] == MsPgRequest::STATUS_PAID ? "" : "<input type='checkbox' name='selected[]' value='{$result['request_id']}' />"),
				'request_id' => $result['request_id'],
				'request_type' => $this->language->get('ms_pg_request_type_' . $result['request_type']),
				'seller' => "<a href='".$this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL')."'>{$result['nickname']}</a>",
				'amount' => $this->currency->format(abs($result['amount']), $result['currency_code']),
				'description' => $result['description'],
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
				'request_status' => $this->language->get('ms_pg_request_status_' . $result['request_status']),
				'payment_id' => $result['payment_id'] ? $result['payment_id'] : '',
				'date_modified' => $result['date_modified'] ? date($this->language->get('date_format_short'), strtotime($result['date_modified'])) : '',
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function jxCreate() {
		$json = array();
		$data = $this->request->post['sellers'];

		// Array of request ids to be processed
		$requests = array();

		// todo VALIDATION
		if(!isset($data) || empty($data)) {
			$json['errors'][] = $this->language->get('ms_pg_request_error_empty');
		}

		if(!isset($json['errors'])) {
			foreach ($data as $seller_info) {
				$this->load->model('localisation/currency');

				if(isset($seller_info['id'])) $seller = $this->MsLoader->MsSeller->getSeller($seller_info['id']);
				$description = sprintf($this->language->get('ms_pg_request_desc_payout'), (isset($seller['ms.nickname']) ? $seller['ms.nickname'] : ''));
				$currency_id = isset($seller_info['currency_id']) ? $seller_info['currency_id'] : $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'))['currency_id'];
				$currency_code = isset($seller_info['currency_code']) ? $seller_info['currency_code'] : $this->config->get('config_currency');

				// Check if payout request already created
				$request_exists = $this->MsLoader->MsPgRequest->getRequests(
					array(
						'seller_id' => $seller_info['id'],
						'request_type' => array(MsPgRequest::TYPE_PAYOUT),
						'request_status' => array(MsPgRequest::STATUS_UNPAID),
//						'single' => 1
					)
				);

				if(!empty($request_exists)) {
					// For backwards compatibility issues, when payout requests were created each one separately, without updating latest record
					// Get last unpaid request without payment_id
					foreach ($request_exists as $key => $request) {
						if ($request['payment_id']) {
							$payment = $this->MsLoader->MsPgPayment->getPayments(array(
								'payment_id' => $request['payment_id'],
								'single' => 1
							));
							if($payment['payment_status'] == MsPgPayment::STATUS_WAITING_CONFIRMATION) {
								continue;
							} else {
								unset($request_exists[$key]);
							}
						}
					}

					if(!empty($request_exists)) {
						// If there are multiple same requests
						$request_exists = end($request_exists);

						$this->MsLoader->MsPgRequest->updateRequest(
							$request_exists['request_id'],
							array(
								'amount' => (float)$request_exists['amount'] + (isset($seller_info['amount']) ? (float)$seller_info['amount'] : 0),
								'currency_id' => $currency_id,
								'currency_code' => $currency_code,
								'date_created' => 1
							)
						);

						$json['success'] = $this->language->get('ms_success');
						$requests[] = (int)$request_exists['request_id'];
					} else {
						$request_id = $this->MsLoader->MsPgRequest->createRequest(
							array(
								'seller_id' => isset($seller_info['id']) ? $seller_info['id'] : 0,
								'request_type' => MsPgRequest::TYPE_PAYOUT,
								'request_status' => MsPgRequest::STATUS_UNPAID,
								'description' => $description,
								'amount' => isset($seller_info['amount']) ? $seller_info['amount'] : 0,
								'currency_id' => $currency_id,
								'currency_code' => $currency_code
							)
						);

						if($request_id) {
							$json['success'] = $this->language->get('ms_success');
							$requests[] = $request_id;
						} else {
							$json['errors'][] = $this->language->get('ms_pg_request_error_empty');
						}
					}
				} else {
					$request_id = $this->MsLoader->MsPgRequest->createRequest(
						array(
							'seller_id' => isset($seller_info['id']) ? $seller_info['id'] : 0,
							'request_type' => MsPgRequest::TYPE_PAYOUT,
							'request_status' => MsPgRequest::STATUS_UNPAID,
							'description' => $description,
							'amount' => isset($seller_info['amount']) ? $seller_info['amount'] : 0,
							'currency_id' => $currency_id,
							'currency_code' => $currency_code
						)
					);

					if($request_id) {
						$json['success'] = $this->language->get('ms_success');
						$requests[] = $request_id;
					} else {
						$json['errors'][] = $this->language->get('ms_pg_request_error_empty');
					}
				}
			}
		}

		$json['requests'] = $requests;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDelete() {
		$json = array();
		$data = $this->request->post['request_ids'];
		
		if(!isset($data) || empty($data)) {
			$json['errors'][] = 'Something is empty!';
		}

		if(!isset($json['errors'])) {
			foreach ($data as $request_id) {
				$this->MsLoader->MsPgRequest->deleteRequest($request_id);
			}

			$json['success'] = 'Success!';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function index() {
		$this->document->addScript('view/javascript/multimerch/payment-request.js');

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

		$this->data['payout_requests']['amount_pending'] = $this->currency->format($this->MsLoader->MsPgRequest->getTotalAmount(array(
			'request_type' => array(MsPgRequest::TYPE_PAYOUT_REQUEST),
			'request_status' => array(MsPgRequest::STATUS_UNPAID)
		)), $this->config->get('config_currency'));

		$this->data['payout_requests']['amount_paid'] = $this->currency->format($this->MsLoader->MsPgRequest->getTotalAmount(array(
			'request_type' => array(MsPgRequest::TYPE_PAYOUT_REQUEST),
			'request_status' => array(MsPgRequest::STATUS_PAID)
		)), $this->config->get('config_currency'));

		$this->data['payouts']['amount_pending'] = $this->currency->format($this->MsLoader->MsPgRequest->getTotalAmount(array(
			'request_type' => array(MsPgRequest::TYPE_PAYOUT),
			'request_status' => array(MsPgRequest::STATUS_UNPAID)
		)), $this->config->get('config_currency'));

		$this->data['payouts']['amount_paid'] = $this->currency->format($this->MsLoader->MsPgRequest->getTotalAmount(array(
			'request_type' => array(MsPgRequest::TYPE_PAYOUT),
			'request_status' => array(MsPgRequest::STATUS_PAID)
		)), $this->config->get('config_currency'));

		$this->data['action'] = $this->url->link('multimerch/payment/create', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_pg_request');
		$this->document->setTitle($this->language->get('ms_pg_request'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_pg_request'),
				'href' => $this->url->link('multimerch/payment-request', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/payment-request.tpl', $this->data));
	}
}