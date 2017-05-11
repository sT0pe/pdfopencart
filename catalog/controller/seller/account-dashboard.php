<?php

class ControllerSellerAccountDashboard extends ControllerSellerAccount {
	public function index() {
		// paypal listing payment confirmation
		if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
			$this->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
		}
		
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
        $this->load->model('account/order');
		
		$seller_id = $this->customer->getId();
		
		$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
		$seller_group_names = $this->MsLoader->MsSellerGroup->getSellerGroupDescriptions($seller['ms.seller_group']);
		$my_first_day = date('Y-m-d H:i:s', mktime(0, 0, 0, date("n"), 1));

		$orders = $this->MsLoader->MsSuborder->getSuborders(array(
			'seller_id' => $seller_id
		));
		$total_orders = isset($orders[0]['total_rows']) ? $orders[0]['total_rows'] : 0;

		$orders_month = $this->MsLoader->MsSuborder->getSuborders(array(
			'seller_id' => $seller_id,
			'period_start' => $my_first_day
		));
		$total_orders_month = isset($orders_month[0]['total_rows']) ? $orders_month[0]['total_rows'] : 0;

		$this->data['seller'] = array_merge(
			$seller,
			array('balance' => $this->currency->format($this->MsLoader->MsBalance->getSellerBalance($seller_id), $this->config->get('config_currency'))),
			array('commission_rates' => $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $seller_id))),
			array('total_earnings' => $this->currency->format($this->MsLoader->MsSeller->getTotalEarnings($seller_id), $this->config->get('config_currency'))),
			array('earnings_month' => $this->currency->format($this->MsLoader->MsSeller->getTotalEarnings($seller_id, array('period_start' => $my_first_day)), $this->config->get('config_currency'))),
			array('total_orders' => $total_orders),
			array('sales_month' => $total_orders_month),
			array('seller_group' => $seller_group_names[$this->config->get('config_language_id')]['name']),
			array('date_created' => date($this->language->get('date_format_short'), strtotime($seller['ms.date_created'])))
			//array('total_products' => $this->MsLoader->MsProduct->getTotalProducts(array(
				//'seller_id' => $seller_id,
				//'enabled' => ))
		);
		
		if ($seller['ms.avatar'] && file_exists(DIR_IMAGE . $seller['ms.avatar'])) {
			$this->data['seller']['avatar'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_seller_avatar_dashboard_image_width'), $this->config->get('msconf_seller_avatar_dashboard_image_height'));
		} else {
			$this->data['seller']['avatar'] = $this->MsLoader->MsFile->resizeImage('ms_no_image.jpg', $this->config->get('msconf_seller_avatar_dashboard_image_width'), $this->config->get('msconf_seller_avatar_dashboard_image_height'));
		}
		 
		$orders = $this->MsLoader->MsOrderData->getOrders(
			array(
				'seller_id' => $seller_id,
			),
			array(
				'order_by'  => 'date_added',
				'order_way' => 'DESC',
				'offset' => 0,
				'limit' => 5
			)
		);		

		$this->load->model('localisation/order_status');
		$this->load->model('checkout/order');
		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
		$this->load->model('tool/upload');
    	foreach ($orders as $order) {
			$suborder = $this->MsLoader->MsSuborder->getSuborders(array(
				'order_id' => $order['order_id'],
				'seller_id' => $this->customer->getId(),
				'single' => 1
			));

			$status_name = $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $order['order_status_id']));

			if (isset($suborder['order_status_id']) && $suborder['order_status_id'] && $order['order_status_id'] != $suborder['order_status_id']) {
				$status_name .= ' (' . $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $suborder['order_status_id'])) . ')';
			}

            $products = $this->MsLoader->MsOrderData->getOrderProducts(array('order_id' => $order['order_id'], 'seller_id' => $seller_id));

            foreach($products as $key=>$p) {
	            $options = $this->model_account_order->getOrderOptions($order['order_id'], $p['order_product_id']);
	            $option_data = array();
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
		            $option_data[] = $option;
	            }
	            $products[$key]['options'] = $option_data;
            }
			$order_total = $this->MsLoader->MsSuborder->getSuborderTotal($order['order_id'], array('seller_id' => $seller_id));
			$shipping_total = $this->MsLoader->MsOrderData->getOrderShippingTotal($order['order_id'], array('seller_id' => $seller_id));
    		$this->data['orders'][] = array(
    			'order_id' => $order['order_id'],
    			'customer' => "{$order['firstname']} {$order['lastname']} ({$order['email']})",
				'status' => $status_name,
    			'products' => $products,
    			'date_created' => date($this->language->get('date_format_short'), strtotime($order['date_added'])),
				'total' => $this->currency->format($order_total['total'] + $shipping_total, $this->config->get('config_currency'))
   			);
   		}

//		$this->data['seller']['total_orders'] = isset($this->data['orders']) ? count($this->data['orders']) : 0;
		$this->data['seller']['total_views'] = $this->MsLoader->MsProduct->getTotalProductViews(array(
			'seller_id' => $this->customer->getId()
		));

		
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_dashboard_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			)
		));
		
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-dashboard');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
}

?>
