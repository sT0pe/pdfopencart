<?php

class ControllerMultimerchSeller extends ControllerMultimerchBase {
	public function __construct($registry) {
		parent::__construct($registry);
	}

	public function getTableData() {
		$colMap = array(
			'seller' => '`c.name`',
			'email' => 'c.email',
			'balance' => '`current_balance`',
			'date_created' => '`ms.date_created`',
			'status' => 'ms.seller_status'
		);

		$sorts = array('seller', 'email', 'total_sales', 'total_products', 'total_earnings', 'date_created', 'balance', 'status', 'date_created');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsSeller->getSellers(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			),
			array(
				'total_products' => 1,
				'total_earnings' => 1,
				'current_balance' => 1,
				'total_sales' => 1
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			$total_sales = $this->MsLoader->MsSuborder->getSuborders(array('seller_id' => $result['seller_id']));
			$result['total_sales'] = isset($total_sales[0]['total_rows']) ? $total_sales[0]['total_rows'] : 0;

			// available amount
			$available = $this->MsLoader->MsBalance->getSellerBalance($result['seller_id']) - $this->MsLoader->MsBalance->getReservedSellerFunds($result['seller_id']);

			// actions
			$actions = "";

			// payout
			if($available > 0) {
				$actions .= '<a class="btn btn-primary ms-pay-single" style="margin-right: 4px;" data-toggle="tooltip" title="' . $this->language->get('ms_pg_request_create') . '"><i class="fa fa-credit-card"></i></a>';
			}

			// login as seller
			$this->load->model('setting/store');
			$actions .= "<div class='btn-group' data-toggle='tooltip' title='" . $this->language->get('button_login') . "'>";
			$actions .= "<button type='button' data-toggle='dropdown' class='btn btn-info dropdown-toggle'><i class='fa fa-lock'></i></button>";
			$actions .= "<ul class='dropdown-menu pull-right'>";
			$actions .= "<li><a href='" . $this->url->link('customer/customer/login', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['seller_id'] . '&store_id=0', 'SSL') . "' target='_blank'>" . $this->language->get('text_default') . "</a></li>";
			foreach ($this->model_setting_store->getStores() as $store) {
				$actions .= "<li><a href='" . $this->url->link('customer/customer/login', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['seller_id'] . '&store_id=' . $store['store_id'], 'SSL') . "' target='_blank'>" . $store['name'] . "</a></li>";
			}
			$actions .= "</ul>";
			$actions .= "</div> ";

			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL') . "' data-toggle='tooltip' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil'></i></a> ";
			$actions .= "<a class='btn btn-danger ms-button-delete' style='background-image: none;' href='" . $this->url->link('multimerch/seller/delete', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL') . "' data-toggle='tooltip' title='".$this->language->get('button_delete')."'><i class='fa fa-trash-o'></i></a> ";

			// build table data
			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => ($available > 0 ? "<input type='checkbox' name='selected[]' value='{$result['seller_id']}' data-available='{$available}'/>" : ""),
					'seller' => "<a href='".$this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['seller_id'], 'SSL')."'>{$result['c.name']}({$result['ms.nickname']})</a>",
					'email' => $result['c.email'],
					'total_earnings' => $this->currency->format($this->MsLoader->MsSeller->getTotalEarnings($result['seller_id']), $this->config->get('config_currency')),
					'balance' => $this->currency->format($this->MsLoader->MsBalance->getSellerBalance($result['seller_id']), $this->config->get('config_currency')) . '/' . $this->currency->format($available > 0 ? $available : 0, $this->config->get('config_currency')),
					'status' => $this->language->get('ms_seller_status_' . $result['ms.seller_status']),
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['ms.date_created'])),
					'actions' => $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
  			'iTotalDisplayRecords' => $total, //count($results),
			'aaData' => $columns
		)));
	}

	public function getProductTableData() {
		$colMap = array(
			'id' => 'product_id',
			'name' => 'pd.name',
			'status' => 'mp.product_status',
			'seller' => 'ms.nickname',
			'date_added' => 'p.date_added',
			'date_modified' => 'p.date_modified'
		);

		$sorts = array('name', 'seller', 'date_added', 'date_modified', 'status');
		//		$filters = array_diff($sorts, array('status'));
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$sellers = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_status' => array(MsSeller::STATUS_ACTIVE, MsSeller::STATUS_INACTIVE)
			),
			array(
				'order_by'  => 'ms.nickname',
				'order_way' => 'ASC'
			)
		);

		$fetch_conditions = array();
		if (isset($this->request->get['seller_id']) AND $this->request->get['seller_id']){
			$fetch_conditions['seller_id'] = $this->request->get['seller_id'];
		}

		$results = $this->MsLoader->MsProduct->getProducts(
			$fetch_conditions,
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			),
			array(
				'product_sales' => 1
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			$shop_url = HTTP_CATALOG . "index.php?route=product/product&product_id=" . $result['product_id'];
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-info' target='_blank' class='ms-button' href='" . $shop_url . "' title='".$this->language->get('ms_view_in_store')."'><i class='fa fa-search'></i></a>";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<span class='btn btn-danger ms-button-delete' id='remove_button".$result['product_id']."' data-product_id='".$result['product_id']."' style='background-image: none;' title='".$this->language->get('ms_delete')."'><i class='fa fa-times'></i></span>";
			// seller select
			$sellerselect = "";
			$sellerselect .= "
			<select class='form-control'>
				<option value='0'>" . $this->language->get('ms_catalog_products_noseller') . "</option>";

			foreach($sellers as $s) {
				$sellerselect .= "<option value='{$s['seller_id']}'" . ($s['seller_id'] == $result['seller_id'] ? " selected='selected'" : "") . ">{$s['ms.nickname']}</option>";
			}

			$sellerselect .= "
			</optgroup>
			</select>
			<button type='button' data-toggle='tooltip' title='' class='ms-assign-seller btn btn-primary' data-original-title='" . $this->language->get('ms_apply') . "'><i class='fa  fa-check'></i></button>
			";
			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected_products[]' value='{$result['product_id']}' />",
					'name' => $result['pd.name'],
					'seller' => $sellerselect,
					'price' => $this->currency->format($result['p.price'], $this->config->get('config_currency')),
					'quantity' => $result['p.quantity'],
					'sales' => $result['number_sold'],
					'status' => $result['mp.product_status'] ? $this->language->get('ms_product_status_' . $result['mp.product_status']) : '',
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['p.date_added'])),
					'date_modified' => date($this->language->get('date_format_short'), strtotime($result['p.date_modified'])),
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

	public function getTransactionTableData() {
		$colMap = array(
			'id' => 'balance_id',
			'seller' => '`nickname`',
			'description' => 'mb.description',
			'date_created' => 'mb.date_created'
		);

		$sorts = array('id', 'seller', 'amount', 'description', 'date_created');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$fetch_conditions = array();
		if (isset($this->request->get['seller_id']) AND $this->request->get['seller_id']){
			$fetch_conditions['seller_id'] = $this->request->get['seller_id'];
		}

		$results = $this->MsLoader->MsBalance->getBalanceEntries(
			$fetch_conditions,
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
			$columns[] = array_merge(
				$result,
				array(
					'id' => $result['balance_id'],
					'seller' => $result['nickname'],
					'amount' => $this->currency->format($result['amount'], $this->config->get('config_currency')),
					'description' => (utf8_strlen($result['mb.description']) > 80 ? mb_substr($result['mb.description'], 0, 80) . '...' : $result['mb.description']),
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['mb.date_created'])),
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function getPaymentRequestTableData() {
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

		$fetch_conditions = array('request_type' => array(MsPgRequest::TYPE_PAYOUT));
		if (isset($this->request->get['seller_id']) AND $this->request->get['seller_id']){
			$fetch_conditions['seller_id'] = $this->request->get['seller_id'];
		}
		$results = $this->MsLoader->MsPgRequest->getRequests(
			$fetch_conditions,
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

	public function getPaymentTableData() {
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

		$fetch_conditions = array();
		if (isset($this->request->get['seller_id']) AND $this->request->get['seller_id']){
			$fetch_conditions['seller_id'] = $this->request->get['seller_id'];
		}

		$results = $this->MsLoader->MsPgPayment->getPayments(
			$fetch_conditions,
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

	public function jxSaveSellerInfo() {
		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$this->validate(__FUNCTION__);
		$data = $this->request->post;
		$seller = $this->MsLoader->MsSeller->getSeller($data['seller']['seller_id']);
		$json = array();
		$this->load->model('customer/customer');

		if (empty($data['seller']['seller_id'])) {
			// creating new seller
			if (empty($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_empty');
			} else if (utf8_strlen($data['seller']['nickname']) < 4 || utf8_strlen($data['seller']['nickname']) > 128 ) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_length');
			} else if ($this->MsLoader->MsSeller->nicknameTaken($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_taken');
			} else {
				switch($this->config->get('msconf_nickname_rules')) {
					case 1:
						// extended latin
						if(!preg_match("/^[a-zA-Z0-9_\-\s\x{00C0}-\x{017F}]+$/u", $data['seller']['nickname'])) {
							$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_latin');
						}
						break;

					case 2:
						// utf8
						if(!preg_match("/((?:[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})./x", $data['seller']['nickname'])) {
							$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_utf8');
						}
						break;

					case 0:
					default:
						// alnum
						if(!preg_match("/^[a-zA-Z0-9_\-\s]+$/", $data['seller']['nickname'])) {
							$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_alphanumeric');
						}
						break;
				}
			}
			if (empty($data['customer']['customer_id'])) {
				// creating new customer
				$this->language->load('customer/customer');
				if ((utf8_strlen($data['customer']['firstname']) < 1) || (utf8_strlen($data['customer']['firstname']) > 32)) {
			  		$json['errors']['customer[firstname]'] = $this->language->get('error_firstname');
				}

				if ((utf8_strlen($data['customer']['lastname']) < 1) || (utf8_strlen($data['customer']['lastname']) > 32)) {
			  		$json['errors']['customer[lastname]'] = $this->language->get('error_lastname');
				}

				if ((utf8_strlen($data['customer']['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['customer']['email'])) {
			  		$json['errors']['customer[email]'] = $this->language->get('error_email');
				}

				$customer_info = $this->model_customer_customer->getCustomerByEmail($data['customer']['email']);

				if (!isset($this->request->get['customer_id'])) {
					if ($customer_info) {
						$json['errors']['customer[email]'] = $this->language->get('error_exists');
					}
				} else {
					if ($customer_info && ($this->request->get['customer_id'] != $customer_info['customer_id'])) {
						$json['errors']['customer[email]'] = $this->language->get('error_exists');
					}
				}

				if ($data['customer']['password'] || (!isset($this->request->get['customer_id']))) {
			  		if ((utf8_strlen($data['customer']['password']) < 4) || (utf8_strlen($data['customer']['password']) > 20)) {
						$json['errors']['customer[password]'] = $this->language->get('error_password');
			  		}

			  		if ($data['customer']['password'] != $data['customer']['password_confirm']) {
						$json['errors']['customer[password_confirm]'] = $this->language->get('error_confirm');
			  		}
				}
			}
		}
		if (isset($data['seller']['company']) && strlen($data['seller']['company']) > 50 ) {
			$json['errors']['seller[company]'] = 'Company name cannot be longer than 50 characters';
		}
		if (empty($json['errors'])) {
			if (empty($data['seller']['seller_id'])) {
				// creating new seller
				if (empty($data['customer']['customer_id'])) {
					// creating new customer
					$this->model_customer_customer->addCustomer(
						array_merge(
							$data['customer'],
							array(
								'telephone' => '',
								'fax' => '',
								'customer_group_id' => $this->config->get('config_customer_group_id'),
								'newsletter' => 1,
								'status' => 1,
								'approved' => 1,
								'safe' => 1,
							)
						)
					);

					$customer_info = $this->model_customer_customer->getCustomerByEmail($data['customer']['email']);
					$this->db->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE customer_id = '" . (int)$customer_info['customer_id'] . "'");

					$data['seller']['seller_id'] = $customer_info['customer_id'];
				} else {
					$data['seller']['seller_id'] = $data['customer']['customer_id'];
				}
				$this->MsLoader->MsSeller->createSeller(
					array_merge(
						$data['seller'],
						array(
							'approved' => 1,
						),
						array('settings' => $data['seller_setting'])
					)
				);
			} else {
				// edit seller
				$MailSellerAccountModified = $serviceLocator->get('MailSellerAccountModified', false)
					->setTo($seller['c.email'])
					->setData(array(
						'addressee' => $seller['ms.nickname'],
						'ms_seller_status' => $data['seller']['status'],
						'message' => (isset($data['seller']['message']) ? $data['seller']['message'] : ''),
					));

				$mails->add($MailSellerAccountModified);

				switch ($data['seller']['status']) {
					case MsSeller::STATUS_INACTIVE:
					case MsSeller::STATUS_DISABLED:
					case MsSeller::STATUS_DELETED:
					case MsSeller::STATUS_INCOMPLETE:
						$products = $this->MsLoader->MsProduct->getProducts(array(
							'seller_id' => $seller['seller_id']
						));

						foreach ($products as $p) {
							$this->MsLoader->MsProduct->changeStatus($p['product_id'], $data['seller']['status']);
						}

						$data['seller']['approved'] = 0;
						break;
					case MsSeller::STATUS_ACTIVE:
						if ($seller['ms.seller_status'] == MsSeller::STATUS_INACTIVE && $this->config->get('msconf_allow_inactive_seller_products')) {
							$products = $this->MsLoader->MsProduct->getProducts(array(
								'seller_id' => $seller['seller_id']
							));

							foreach ($products as $p) {
								$this->MsLoader->MsProduct->changeStatus($p['product_id'], $data['seller']['status']);
								if ($this->config->get('msconf_product_validation') == MsProduct::MS_PRODUCT_VALIDATION_NONE) {
									$this->MsLoader->MsProduct->approve($p['product_id']);
								}
							}
						}

						$data['seller']['approved'] = 1;
						break;
				}
				$this->MsLoader->MsSeller->adminEditSeller(
					array_merge(
						$data['seller'],
						array(
							'approved' => 1,
						),
                        array('settings' => $data['seller_setting'])
					)
				);
			}

			if(isset($data['payment_gateways'])) {
				// add seller's payment gateways settings
				$pg_settings_data = array();
				foreach ($data['payment_gateways'] as $code => $setting) {
					$pg_settings_data['seller_id'] = $data['seller']['seller_id'];
					foreach ($setting as $name => $value) {
						$pg_settings_data['settings'][$code . '_' . $name] = $value;
					}
				}
				$this->MsLoader->MsSetting->createSellerSetting($pg_settings_data);
			}

			if (isset($data['seller']['notify']) && $data['seller']['notify']) {
				$mailTransport->sendMails($mails);
			}
			$this->session->data['success'] = 'Seller account data saved.';
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxSetPayoutAmount() {
		$json = array();
		$data = $this->request->post;

		if(!isset($data['seller_ids']) || empty($data['seller_ids'])) {
			$json['error'] = 'Something is empty!';
		} else {
			$sellers = array();
			foreach ($data['seller_ids'] as $seller) {
				$sellers[] = array(
					'info' => $this->MsLoader->MsSeller->getSeller($seller['id']),
					'available_amount' => $seller['available']
				);
			}

			$this->data['token'] = $this->session->data['token'];
			$this->data['sellers'] = $sellers;

			list($template, $children) = $this->MsLoader->MsHelper->admLoadTemplate('multiseller/seller-payout-amount-form');
			$json['html'] = $this->load->view($template, $this->data);
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteProduct() {
		$json = array();
		$product_ids = array();
		if(isset($this->request->post['selected_products'])){
			$product_ids = $this->request->post['selected_products'];
		} else if(isset($this->request->get['product_id'])) {
			$product_id =  $this->request->get['product_id'];
			$product_ids = array($product_id);
		}else{
			$json['errors'] = 'Something is empty!';
		}
		foreach($product_ids as $product_id) {
			$this->MsLoader->MsProduct->deleteProduct($product_id);
		}
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
//		$this->data = array_merge($this->data, $this->load->language("module/multimerch_masspay"));
		$this->document->addScript('//code.jquery.com/ui/1.11.2/jquery-ui.min.js');
		$this->document->addScript('view/javascript/multimerch/seller-payout.js');
		$this->document->addStyle('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');

		$this->validate(__FUNCTION__);

		// paypal listing payment confirmation
		if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
			$this->data['success'] = $this->language->get('ms_payment_completed');
		}

		$this->data['total_balance'] = sprintf($this->language->get('ms_catalog_sellers_total_balance'), $this->currency->format($this->MsLoader->MsBalance->getTotalBalanceAmount(), $this->config->get('config_currency')), $this->currency->format($this->MsLoader->MsBalance->getTotalBalanceAmount(array('seller_status' => array(MsSeller::STATUS_ACTIVE))), $this->config->get('config_currency')));

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_sellers_heading');
		$this->document->setTitle($this->language->get('ms_catalog_sellers_heading'));

//		$this->data['link_pay_seller'] = $this->url->link('multimerch/payment-request/create', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['link_create_seller'] = $this->url->link('multimerch/seller/create', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_sellers_breadcrumbs'),
				'href' => $this->url->link('multimerch/seller', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/seller.tpl', $this->data));
	}

	public function create() {
		$this->validate(__FUNCTION__);
		$this->load->model('localisation/country');
		$this->load->model('tool/image');
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		$this->data['customers'] = $this->MsLoader->MsSeller->getCustomers();
		$this->data['seller_groups'] =$this->MsLoader->MsSellerGroup->getSellerGroups();
		$this->data['seller'] = FALSE;

		// badges
		$badges = $this->MsLoader->MsBadge->getBadges();
		foreach($badges as &$badge) {
			$badge['image'] = $this->model_tool_image->resize($badge['image'], 30, 30);
		}
		$this->data['badges'] = $badges;

		$this->data['settings'] = $this->MsLoader->MsSetting->getSellerDefaults();
		$this->data['payment_gateways'] = $this->_getPaymentGateways();

		$this->data['currency_code'] = $this->config->get('config_currency');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_sellerinfo_heading');
		$this->document->setTitle($this->language->get('ms_catalog_sellerinfo_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_sellers_breadcrumbs'),
				'href' => $this->url->link('multimerch/seller', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_sellers_newseller'),
				'href' => $this->url->link('multimerch/seller/create', 'SSL'),
			)
		));

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/seller-form.tpl', $this->data));
	}

	public function update() {
		$this->validate(__FUNCTION__);

		$seller_id = (int)$this->request->get['seller_id'];

		if (!$seller_id) {
			return $this->response->redirect($this->url->link('multimerch/seller', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->load->model('localisation/country');
		$this->load->model('tool/image');
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		$seller = $this->MsLoader->MsSeller->getSeller($this->request->get['seller_id']);

		$seller_settings = $this->MsLoader->MsSetting->getSellerSettings(array('seller_id' => $this->request->get['seller_id']));
		$defaults = $this->MsLoader->MsSetting->getSellerDefaults();
		$this->data['settings'] = array_merge($defaults, $seller_settings);

        $this->data['seller_groups'] = $this->MsLoader->MsSellerGroup->getSellerGroups();

		if (!empty($seller)) {
			$rates = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->request->get['seller_id']));
			$actual_fees = '';
			foreach ($rates as $rate) {
				if ($rate['rate_type'] == MsCommission::RATE_SIGNUP) continue;
				$actual_fees .= '<span class="fee-rate-' . $rate['rate_type'] . '"><b>' . $this->language->get('ms_commission_short_' . $rate['rate_type']) . ':</b>' . $rate['percent'] . '%+' . $this->currency->getSymbolLeft() .  $this->currency->format($rate['flat'], $this->config->get('config_currency'), '', FALSE) . $this->currency->getSymbolRight() . '&nbsp;&nbsp;';
			}

			$this->data['seller'] = $seller;
			$this->data['seller']['actual_fees'] = $actual_fees;

			if (!empty($seller['ms.avatar'])) {
				$this->data['seller']['avatar']['name'] = $seller['ms.avatar'];
				$this->data['seller']['avatar']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
				//$this->session->data['multiseller']['files'][] = $seller['avatar'];
			}

			if (is_null($seller['ms.commission_id']))
				$rates = NULL;
			else
				$rates = $this->MsLoader->MsCommission->getCommissionRates($seller['ms.commission_id']);

			$this->data['seller']['commission_id'] = $seller['ms.commission_id'];
			$this->data['seller']['commission_rates'] = $rates;

			// badges
			$badges = $this->MsLoader->MsBadge->getBadges();
			foreach($badges as &$badge) {
				$badge['image'] = $this->model_tool_image->resize($badge['image'], 30, 30);
			}
			$this->data['badges'] = $badges;

			$seller_badges = $this->MsLoader->MsBadge->getSellerGroupBadges(array('seller_id' => $seller['seller_id']));
			$this->data['seller']['badges'] = array();
			foreach($seller_badges as $b) {
				$this->data['seller']['badges'][] = $b['badge_id'];
			}
			$this->data['seller']['badges'] = $this->data['seller']['badges'];
		}

		$this->data['payment_gateways'] = $this->_getPaymentGateways();

		$this->data['currency_code'] = $this->config->get('config_currency');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_sellerinfo_heading');
		$this->document->setTitle($this->language->get('ms_catalog_sellerinfo_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_sellers_breadcrumbs'),
				'href' => $this->url->link('multimerch/seller', '', 'SSL'),
			),
			array(
				'text' => $seller['ms.nickname'],
				'href' => $this->url->link('multimerch/seller/update', '&seller_id=' . $seller['seller_id'], 'SSL'),
			)
		));

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		foreach ($this->data['languages'] as $language){
			if(!isset($this->data['seller']['descriptions'][$language['language_id']]['description'])){
				$this->data['seller']['descriptions'][$language['language_id']]['description'] = '';
			}
		}

		$this->data['payout_requests']['amount_pending'] = $this->currency->format($this->MsLoader->MsPgRequest->getTotalAmount(array(
			'seller_id' => $seller['seller_id'],
			'request_type' => array(MsPgRequest::TYPE_PAYOUT_REQUEST),
			'request_status' => array(MsPgRequest::STATUS_UNPAID)
		)), $this->config->get('config_currency'));

		$this->data['payout_requests']['amount_paid'] = $this->currency->format($this->MsLoader->MsPgRequest->getTotalAmount(array(
			'seller_id' => $seller['seller_id'],
			'request_type' => array(MsPgRequest::TYPE_PAYOUT_REQUEST),
			'request_status' => array(MsPgRequest::STATUS_PAID)
		)), $this->config->get('config_currency'));

		$this->data['payouts']['amount_pending'] = $this->currency->format($this->MsLoader->MsPgRequest->getTotalAmount(array(
			'seller_id' => $seller['seller_id'],
			'request_type' => array(MsPgRequest::TYPE_PAYOUT),
			'request_status' => array(MsPgRequest::STATUS_UNPAID)
		)), $this->config->get('config_currency'));

		$this->data['payouts']['amount_paid'] = $this->currency->format($this->MsLoader->MsPgRequest->getTotalAmount(array(
			'seller_id' => $seller['seller_id'],
			'request_type' => array(MsPgRequest::TYPE_PAYOUT),
			'request_status' => array(MsPgRequest::STATUS_PAID)
		)), $this->config->get('config_currency'));

		$this->data['is_edit'] = true;

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/seller-form.tpl', $this->data));
	}

	public function delete() {
		$seller_id = isset($this->request->get['seller_id']) ? $this->request->get['seller_id'] : 0;
		$this->MsLoader->MsSeller->deleteSeller($seller_id);
		$this->response->redirect($this->url->link('multimerch/seller', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function _getPaymentGateways() {
		$this->load->model('extension/extension');
		$this->load->model('setting/setting');

		$payment_gateways = array();
		$extensions = $this->model_extension_extension->getInstalled('ms_payment');

		foreach ($extensions as $extension) {
			$extension_name = str_replace('ms_pg_', '', $extension);

			$this->load->language('multimerch/payment/' . $extension_name);

			$settings = $this->model_setting_setting->getSetting($extension);

			foreach ($settings as $name => $value) {
				if((strpos($name, 'payout_enabled') && $value) || (strpos($name, 'fee_enabled') && $value)) {
					$payment_gateways[] = array(
						'code' => $extension,
						'text_title' => $this->language->get('heading_title'),
						'view' => $this->load->controller('multimerch/payment/' . $extension_name . '/jxGetPgSettingsForm')
					);
					break;
				}
			}
		}

		return $payment_gateways;
	}
}
?>
