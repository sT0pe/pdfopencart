<?php

class ControllerSellerAccountOrder extends ControllerSellerAccount {
	public function getTableData() {
		$colMap = array(
			'customer_name' => 'firstname',
			'date_created' => 'o.date_added',
		);
		
		$sorts = array('order_id', 'customer_name', 'date_created', 'total_amount');
		$filters = array_merge($sorts, array('products'));
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);
		
		$seller_id = $this->customer->getId();
		$this->load->model('account/order');
		$orders = $this->MsLoader->MsOrderData->getOrders(
			array(
				'seller_id' => $seller_id,
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength'],
				'filters' => $filterParams
			),
			array(
				'total_amount' => 1,
				'products' => 1,
			)
		);
		$total_orders = isset($orders[0]) ? $orders[0]['total_rows'] : 0;
		$this->load->model('tool/upload');
		$columns = array();
		foreach ($orders as $order) {
			$order_products = $this->MsLoader->MsOrderData->getOrderProducts(array('order_id' => $order['order_id'], 'seller_id' => $seller_id));
			
			if ($this->config->get('msconf_hide_customer_email')) {
				$customer_name = "{$order['firstname']} {$order['lastname']}";
			} else {
				$customer_name = "{$order['firstname']} {$order['lastname']} ({$order['email']})";
			}
			
			$products = "";
			foreach ($order_products as $p) {
                $products .= "<p style='text-align:left'>";
				$products .= "<span class='name'>" . ($p['quantity'] > 1 ? "{$p['quantity']} x " : "") . "<a href='" . $this->url->link('product/product', 'product_id=' . $p['product_id'], 'SSL') . "'>{$p['name']}</a></span>";
				$options   = $this->model_account_order->getOrderOptions($order['order_id'], $p['order_product_id']);
				foreach ($options as $option)
                {
	                if ($option['type'] != 'file') {
		                $value = $option['value'];
		                $option['value']	=  utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value;
	                } else {
		                $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
		                $option['value'] = '';
		                if ($upload_info) {
			                $value = $upload_info['name'];
			                $option['value']	=  utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value;
			                $option['value'] = '<a href="'.$this->url->link('account/msconversation/downloadAttachment', 'code=' . $upload_info['code'], true).'">'.$option['value'].'</a>';
		                }
	                }
                    $products .= "<br />";
                    $products .= "<small> - {$option['name']} : {$option['value']} </small>";
                }

                $products .= "<span class='total'>" . $this->currency->format($p['seller_net_amt'] + $p['shipping_cost'], $order['currency_code'], $order['currency_value']) . "</span>";
				$products .= "</p>";
			}

			$suborder_status_id = $this->MsLoader->MsSuborder->getSuborderStatus(array(
				'order_id' => $order['order_id'],
				'seller_id' => $this->customer->getId()
			));

			$status_name = $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $suborder_status_id));

			$actions = '<a class="icon-view" href="' . $this->url->link('seller/account-order/viewOrder', 'order_id=' . $order['order_id'], 'SSL') . '" title="' . $this->language->get('ms_view_modify') . '"><i class="fa fa-search"></i></a>';
			$actions .= '<a class="icon-invoice" target="_blank" href="' . $this->url->link('seller/account-order/invoice', 'order_id=' . $order['order_id'], 'SSL') . '" title="' . $this->language->get('ms_view_invoice') . '"><i class="fa fa-file-text-o"></i></a>';

			$shipping_total = $this->MsLoader->MsOrderData->getOrderShippingTotal($order['order_id'], array('seller_id' => $this->customer->getId()));
			$order_total = $order['total_amount'] + $shipping_total;

			$columns[] = array_merge(
				$order,
				array(
					'order_id' => $order['order_id'],
					'customer_name' => $customer_name,
					'products' => $products,
					'suborder_status' => $status_name,
					'date_created' => date($this->language->get('date_format_short'), strtotime($order['date_added'])),
					'total_amount' => $this->currency->format($order_total, $order['currency_code'], $order['currency_value']),
					'view_order' => $actions
				)
			);
		}
		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total_orders,
			'iTotalDisplayRecords' => $total_orders,
			'aaData' => $columns
		)));
	}

	public function viewOrder() {
		$order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;
		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id, 'seller');

		$order_products = $this->_getOrderProducts($order_id,$this->customer->getId());

		// stop if no order or no products belonging to seller
		if (!$order_info || empty($order_products['products'])) {
			$this->response->redirect($this->url->link('seller/account-order', '', 'SSL'));
		}

		// load default OC language file for orders
		$this->data = array_merge($this->data, $this->load->language('account/order'));

		// order statuses
		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$suborder = $this->MsLoader->MsSuborder->getSuborders(array(
			'order_id' => $order_id,
			'seller_id' => $this->customer->getId(),
			'single' => 1
		));

		$this->data['suborder_status_id'] = $suborder ? $suborder['order_status_id'] : 0;
		$this->data['suborder_id'] = isset($suborder['suborder_id']) ? $suborder['suborder_id'] : '';

		// OC way of displaying addresses and invoices
		$this->data['invoice_no'] = isset($order_info['invoice_no']) ? $order_info['invoice_prefix'] . $order_info['invoice_no'] : '';

		$this->data['order_status_id'] = $order_info['order_status_id'];
		$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
		$this->data['order_id'] = $this->request->get['order_id'];

		$this->data['order_info'] = $order_info;

		$types = array("payment", "shipping");

		$this->_loadAddressData($types, $order_info);

		// sub-order transactions
		$this->data['suborder_transactions'] = $this->MsLoader->MsBalance->getBalanceEntries(array(
			'seller_id' => $this->customer->getId(),
			'order_id' => $this->data['order_id']
		));

		// sub-order history entries
		$this->data['order_history'] = $this->MsLoader->MsSuborder->getSuborderHistory(array(
			'suborder_id' => $this->data['suborder_id']
		));

		$this->data['products'] = $order_products['products'];
		$this->data['totals'] = $this->_getOrderTotals($order_id,$order_products['initial_order_total']);

		// render
		$this->data['link_back'] = $this->url->link('seller/account-order', '', 'SSL');
		$this->data['continue'] = $this->url->link('account/order', '', 'SSL');

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
				'text' => $this->language->get('ms_account_orders_breadcrumbs'),
				'href' => $this->url->link('seller/account-order', '', 'SSL'),
			)
		));

		$this->document->setTitle($this->language->get('text_order'));
		$this->document->addScript('catalog/view/javascript/multimerch/account-message.js');
		$this->MsLoader->MsHelper->addStyle('multimerch_messaging');

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-order-info');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
	
	public function invoice() {
		// check order details
		$customer_id = $this->customer->getId();
		$order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;
		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id, 'seller');

		$order_products = $this->_getOrderProducts($order_id,$this->customer->getId());

		// stop if no order or no products belonging to seller
		if (!$order_info || empty($order_products['products'])) $this->response->redirect($this->url->link('seller/account-order', '', 'SSL'));

        //get seller settings
		$seller_settings = $this->MsLoader->MsSetting->getSellerSettings(array('seller_id' => $customer_id));
		$defaults = $this->MsLoader->MsSetting->getSellerDefaults();
		$this->data['settings'] = array_merge($defaults, $seller_settings);

		$server = $this->request->server['HTTPS'] ? $this->config->get('config_ssl') : $this->config->get('config_url');

        $this->load->model('localisation/country');
        $this->data['settings']['slr_country'] = $this->model_localisation_country->getCountry($this->data['settings']['slr_country']);

		$this->load->model('tool/image');
		if (is_file(DIR_IMAGE . $this->data['settings']['slr_logo'])) {
			$this->data['logo'] = $this->MsLoader->MsFile->resizeImage($this->data['settings']['slr_logo'], 80, 80);
		} else {
			$this->data['logo'] = '';
		}

		// load default OC language file for orders
		$this->data = array_merge($this->data, $this->load->language('account/order'));

		// order statuses
		$this->load->model('localisation/order_status');
		$this->load->model('extension/total/shipping');

		// OC way of displaying addresses and invoices
		$this->data['invoice_no'] = isset($order_info['invoice_no']) ? $order_info['invoice_prefix'] . $order_info['invoice_no'] : '';
		$this->data['order_status_id'] = $order_info['order_status_id'];
		$this->data['order_id'] = $this->request->get['order_id'];

		$types = array("payment");
		$this->_loadAddressData($types, $order_info);

		// order info
		$this->data['order_info'] = $order_info;

		// products
		$this->data['products'] = $order_products['products'];

		//totals
		$this->data['totals'] = $this->_getOrderTotals($order_id,$order_products['initial_order_total']);

//		$total = $this->currency->format($total);
//		$shipping_total = $this->currency->format($shipping_total);
//
//		$this->data['totals'][0] = array('text' => $shipping_total, 'title' => 'Shipping');
//		$this->data['totals'][1] = array('text' => $total, 'title' => 'Total');

		// custom styles
		$this->MsLoader->MsHelper->addStyle('multimerch/invoice/default');
		$this->MsLoader->MsHelper->addStyle('stylesheet');

		// OC's default header things
		$this->data['base'] = $server;
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['title'] = $this->language->get('heading_invoice_title');

		// load template parts
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/invoice/header');
		$head = $this->load->view($template, array_merge($this->data, $children));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/invoice/body-default');
		$body = $this->load->view($template, array_merge($this->data, $children));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/invoice/footer');
		$foot = $this->load->view($template, array_merge($this->data, $children));

		// render
		$this->response->setOutput($head . $body . $foot);
	}
		
	public function index() {
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_order_information'));
		
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
				'text' => $this->language->get('ms_account_orders_breadcrumbs'),
				'href' => $this->url->link('seller/account-order', '', 'SSL'),
			)
		));
		
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-order');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxAddHistory() {
		if(!isset($this->request->post['order_comment']) || !isset($this->request->post['order_status']) || !isset($this->request->post['suborder_id'])) return false;
		if(empty($this->request->post['order_comment']) && !$this->request->post['order_status']) return false;

		if(!$this->_validate($this->request->post['suborder_id'])) return false;

		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();


		// keep current status if not changing explicitly
		$suborderData = $this->MsLoader->MsSuborder->getSuborders(array(
			'suborder_id' => (int)$this->request->post['suborder_id'],
			'single' => 1
		));

		$suborder_status_id = $this->request->post['order_status'] ? (int)$this->request->post['order_status'] : $suborderData['order_status_id'];

		$this->MsLoader->MsSuborder->updateSuborderStatus(array(
			'suborder_id' => (int)$this->request->post['suborder_id'],
			'order_status_id' => $suborder_status_id
		));

		$this->MsLoader->MsSuborder->addSuborderHistory(array(
			'suborder_id' => (int)$this->request->post['suborder_id'],
			'comment' => $this->request->post['order_comment'],
			'order_status_id' => $suborder_status_id
		));

		// get customer information
		$this->load->model('checkout/order');
		$this->load->model('account/order');
		$order_info = $this->model_checkout_order->getOrder($suborderData['order_id']);

		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		$MailOrderUpdated = $serviceLocator->get('MailOrderUpdated', false)
			->setTo($order_info['email'])
			->setData(array(
				//'addressee' => $this->registry->get('customer')->getFirstname(),
				'order_status' => $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $suborder_status_id)),
				'order_comment' => $this->request->post['order_comment'],
				'order_id' => $suborderData['order_id'],
				'seller_nickname' => $seller['ms.nickname'],
				'order_products' => $this->MsLoader->MsOrderData->getOrderProducts(array('order_id' => $suborderData['order_id'], 'seller_id' => $this->customer->getId()))
			));
		$mails->add($MailOrderUpdated);

		$mailTransport->sendMails($mails);
	}

	private function _loadAddressData($types, $order_info) {
		foreach ($types as $key => $type) {

			$address_data_keys = array(
				'_firstname',
				'_lastname',
				'_company',
				'_address_1',
				'_address_2',
				'_city',
				'_postcode',
				'_zone',
				'_zone_code',
				'_country',
			);

			foreach ($address_data_keys as $address_data_key) {
				$this->data[$type . $address_data_key] = $order_info[$type . $address_data_key];
			}


			$this->data[$type . '_method'] = $order_info[$type . '_method'];
		}

		$this->data['telephone'] = $order_info['telephone'];
	}

	private function _validate($suborder_id) {
		return $this->MsLoader->MsSuborder->isValidSeller($suborder_id, $this->customer->getId());
	}

	private function _getOrderTotals($order_id,$initial_order_total) {
		// totals @todo
		$order_info = $this->model_account_order->getOrder($order_id, 'seller');
		$suborder_sub_total = $this->MsLoader->MsOrderData->getOrderTotal($order_id, array('seller_id' => $this->customer->getId()));
		$suborder_shipping_total = $this->MsLoader->MsOrderData->getOrderShippingTotal($order_id, array('seller_id' => $this->customer->getId()));
		$suborder_total = $suborder_sub_total + $suborder_shipping_total;

		// Get oc order totals. Needed to keep sort_order in totals
		$order_totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);
		foreach ($order_totals as $key => &$total) {
			if($total['code'] == 'mm_shipping_total') $total['value'] = $suborder_shipping_total;
			if($total['code'] == 'total') $total['value'] = $suborder_total;

			if($total['code'] == 'sub_total') {
				if((float)$total['value'] !== (float)$initial_order_total)
					$total['title'] .= '<span data-toggle="tooltip" title="Store commission deducted"> <i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>';

				$total['value'] = $suborder_sub_total;
			}

			// if total is for taxes - unset it, because taxes are counted in product's price
			// if total is for oc shipping - unset it, because this information is not related to seller
			if($total['code'] == 'tax' || $total['code'] == 'shipping' || $total['code'] == 'coupon') {
				unset($order_totals[$key]);
				continue;
			}
			$total['text'] = $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']);
		}

		return $order_totals;
	}

	private function _getOrderProducts($order_id, $seller_id) {
		$order_info = $this->model_account_order->getOrder($order_id, 'seller');
		$products = $this->MsLoader->MsOrderData->getOrderProducts(array(
			'order_id' => $order_id,
			'seller_id' => $seller_id
		));
		// products
		$result['products'] = array();
		$this->data['mm_shipping_flag'] = 0;

		// Needed to clarify the store commission for seller
		$initial_order_total = 0;
		$this->load->model('tool/upload');
		foreach ($products as $product) {
			// Check if any of order products has MM shipping data
			if(isset($product['shipping_cost']) && $product['shipping_cost'] > 0) {
				$this->data['mm_shipping_flag'] += 1;
			}
			$product_shipping_cost = isset($product['shipping_cost']) ? $product['shipping_cost'] : 0;
			$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);
			$option_data = array();
			foreach ($options as $option) {
				if ($option['type'] != 'file') {
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => $option['value'],
						'type'  => $option['type']
					);
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $upload_info['name'],
							'type'  => $option['type'],
							'href'  => $this->url->link('account/msconversation/downloadAttachment', 'code=' . $upload_info['code'], true)
						);
					}
				}
			}
			$result['products'][] = array(
				'product_id' => $product['product_id'],
				'name'     => $product['name'],
				'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL'),
				'model'    => $product['model'],
				'option'     => $option_data,
				'quantity' => $product['quantity'],
				'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'shipping_cost'    => $this->currency->format($product_shipping_cost, $order_info['currency_code'], $order_info['currency_value']),
				'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0) + $product_shipping_cost, $order_info['currency_code'], $order_info['currency_value']),
				'return'   => $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], 'SSL')
			);

			$initial_order_total += $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0) + $product_shipping_cost;
		}
		$result['initial_order_total'] = $initial_order_total;
		return $result;
	}


}

?>
