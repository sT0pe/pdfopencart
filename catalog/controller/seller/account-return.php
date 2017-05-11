<?php

class ControllerSellerAccountReturn extends ControllerSellerAccount {

	public function __construct($registry) {
		parent::__construct($registry);
		// Temporary block all seller-return methods
		$this->response->redirect($this->url->link('account/account', '', 'SSL'));
	}

	public function index() {
		// Links
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_returns_heading'));
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
				'text' => $this->language->get('ms_account_returns_breadcrumbs'),
				'href' => $this->url->link('seller/account-return', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-return');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function getTableData() {
//		$colMap = array(
//			'product_name' => 'pd.name',
//			'product_status' => '`mp.product_status`',
//			'date_added' => '`p.date_added`',
//			'list_until' => 'mp.list_until',
//			'number_sold' => 'number_sold',
//			'product_price' => 'p.price',
//		);
		$colMap = [];
		
		$sorts = array('return_id', 'order_id', 'customer', 'status', 'date_added');
		$filters = array_diff($sorts, array('status'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$seller_id = $this->customer->getId();
		$returns = $this->MsLoader->MsReturn->getReturnsList(
			array(
				'seller_id' => $seller_id
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			),
			array(

			)
		);
		
		$total = isset($returns[0]) ? $returns[0]['total_rows'] : 0;

		$columns = array();
		foreach ($returns as $return) {
			// actions
			$actions = "";
			if ($return['return_status_id'] != MsReturn::STATUS_CLOSED) {
				$actions .= "<a class='icon-edit' href='" . $this->url->link('seller/account-return/update', 'return_id=' . $return['return_id'], 'SSL') ."' title='" . $this->language->get('ms_edit') . "'><i class='fa fa-pencil'></i></a>";
				$actions .= "<a class='icon-remove' href='" . $this->url->link('seller/account-return/delete', 'return_id=' . $return['return_id'], 'SSL') ."' title='" . $this->language->get('ms_delete') . "'><i class='fa fa-times'></i></a>";
			} else {
				if ($this->config->get('msconf_allow_relisting')) {
					$actions .= "<a href='" . $this->url->link('seller/account-return/update', 'return_id=' . $product['return_id'] . "&relist=1", 'SSL') ."' class='ms-button ms-button-relist' title='" . $this->language->get('ms_relist') . "'></a>";
				}
			}
			
			$columns[] = array_merge(
				$return,
				array(
					'return_id' => "#" . $return['return_id'],
					'order_id' => "#" . $return['order_id'],
					'customer' => $return['firstname'] . " " . $return['lastname'],
					'status' => $return['return_status_id'],
					'date_added' => date($this->language->get('date_format_short'), strtotime($return['date_added'])),
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

	// Product Return form for customer to create return entity
	public function initCustomerForm() {
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('localisation/currency');
		$this->load->model('localisation/language');
		$this->load->model('account/customer_group');

		$this->document->addScript('catalog/view/javascript/multimerch/Sortable.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.full.min.js');
		$this->document->addScript('http://protonet.github.io/plupload/src/javascript/plupload.js');
		$this->document->addScript('http://protonet.github.io/plupload/src/javascript/plupload.html5.js');
		$this->document->addScript('catalog/view/javascript/account-product-form.js');
		$this->document->addScript('catalog/view/javascript/plupload/jquery.plupload.queue/jquery.plupload.queue.js');
		$this->document->addScript('catalog/view/javascript/multimerch/account-product-form-options.js');

		$this->MsLoader->MsHelper->addStyle('multimerch/flags');

		// rte
		if ($this->config->get('msconf_enable_rte')) {
			$this->document->addScript('catalog/view/javascript/multimerch/ckeditor/ckeditor.js');
		}

		$this->data['seller'] = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		$this->data['seller_group'] = $this->MsLoader->MsSellerGroup->getSellerGroup($this->data['seller']['ms.seller_group']);

		$this->data['customer_groups'] = $this->model_account_customer_group->getCustomerGroups();
		$this->data['hide_customer_groups'] = (count($this->data['customer_groups']) < 2) ? true : false;

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		if ($product_id) $product_status = $this->MsLoader->MsProduct->getStatus($product_id);

		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($this->customer->getId());
		$this->data['categories'] = $this->MsLoader->MsProduct->getOcCategories();
		$this->data['date_available'] = date('Y-m-d', time() - 86400);
		$this->data['tax_classes'] = $this->MsLoader->MsHelper->getTaxClasses();
		$this->data['stock_statuses'] = $this->MsLoader->MsHelper->getStockStatuses();

		// product_info
		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['back'] = $this->url->link('seller/account-return', '', 'SSL');
		$this->data['product_attributes'] = FALSE;
		$this->data['product'] = FALSE;
		$this->data['heading'] = $this->language->get('ms_account_returns_heading');
		$this->document->setTitle($this->language->get('ms_account_returns_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-return-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	// Product Return form for seller to view return details
	public function viewReturn() {
		$return_id = isset($this->request->get['return_id']) ? (int)$this->request->get['return_id'] : 0;

		$return_info = $this->MsLoader->MsReturn->getReturnData(array(
			'return_id' => $return_id
		));

		// stop if no order or no products belonging to seller
		if (!$return_info) {
			$this->response->redirect($this->url->link('seller/account-return', '', 'SSL'));
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

		// products
		$this->data['products'] = array();
		foreach ($products as $product) {
			$this->data['products'][] = array(
				'product_id' => $product['product_id'],
				'name'     => $product['name'],
				'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL'),
				'model'    => $product['model'],
				'option'     => $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']),
				'quantity' => $product['quantity'],
				'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
				'return'   => $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], 'SSL')
			);
		}

		// sub-order history entries
		$this->data['order_history'] = $this->MsLoader->MsSuborder->getSuborderHistory(array(
			'suborder_id' => $this->data['suborder_id']
		));

		// totals @todo
		$subordertotal = $this->currency->format(
			$this->MsLoader->MsOrderData->getOrderTotal($order_id, array('seller_id' => $this->customer->getId())),
			$order_info['currency_code'], $order_info['currency_value']
		);

		//$this->data['totals'] = $this->model_account_order->getOrderTotals($this->request->get['order_id']);
		$this->data['totals'][0] = array('text' => $subordertotal, 'title' => 'Total');

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

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-return-info');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxAddHistory() {
		if(!isset($this->request->post['seller_comment']) || !isset($this->request->post['return_status']) || !isset($this->request->post['return_id'])) return false;
		if(empty($this->request->post['seller_comment']) && !$this->request->post['return_status']) return false;

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

	public function jxOrderInfo() {
		if(!isset($this->request->post['return_order_id']) || empty($this->request->post['return_order_id'])) return false;

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->request->post['return_order_id']);

		foreach ($order_info as $item) {
			
		}

//		$orderData = $this->MsLoader->MsSuborder->getSuborders(array(
//			'suborder_id' => (int)$this->request->post['suborder_id'],
//			'single' => 1
//		));

		return json_encode($orderData);
	}

}
?>
