<?php
class ControllerMultimerchAccountOrder extends Controller
{
	private $data = array();
	const REFERRER_CUSTOMER = 1;
	const REFERRER_SELLER = 2;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->data = array_merge($this->load->language('multiseller/multiseller'), $this->load->language('account/order'));
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL')
		);

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['content_top'] = $this->load->controller('common/content_top');
		$this->data['content_bottom'] = $this->load->controller('common/content_bottom');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->load->model('account/order');
		$this->load->model('tool/image');
		
		$this->data['order_total'] = $this->model_account_order->getTotalOrders();
		$orders = $this->model_account_order->getOrders();
		$results = array(
			'orders' => array(),
			'products' => array(),
		);

		foreach($orders as $k => $order) {
			$orders[$k]['total'] = $this->currency->format($orders[$k]['total']);
			$orders[$k]['date_added'] = date('d F Y', strtotime($orders[$k]['date_added']));

			$products = $this->model_account_order->getOrderProducts($order['order_id']);

			foreach ($products as $key => $product) {
				$products[$key]['options'] = $this->model_account_order->getOrderOptions($product['order_id'], $product['order_product_id']);
				$products[$key]['seller'] = $this->MsLoader->MsSeller->getSeller($this->MsLoader->MsProduct->getSellerId($product['product_id']));
				$products[$key]['price'] = $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0));
				$products[$key]['product'] = $this->model_catalog_product->getProduct($product['product_id']);
				$products[$key]['product']['image'] = $this->model_tool_image->resize($products[$key]['product']['image'] ? $products[$key]['product']['image'] : 'no_image.png', $this->config->get('msconf_seller_avatar_seller_profile_image_width'), $this->config->get('msconf_seller_avatar_seller_profile_image_height'));
				$products[$key]['review'] = $this->MsLoader->MsReview->getReviews(array('order_product_id' => $product['order_product_id'], 'author_id' => $this->customer->getId()));
				$products[$key]['suborder_status'] = $this->MsLoader->MsSuborder->getSuborderStatus(array(
					'order_id' => $product['order_id'],
					'seller_id' => $this->MsLoader->MsProduct->getSellerId($product['product_id'])
				));
			}

			$results['products'][$order['order_id']] = $products;
		}

		$results['orders'] = $orders;

		$this->data['orders'] = $results;

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/account/order.tpl')) {
			$this->response->setOutput($this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/account/order.tpl', $this->data));
		} else {
			$this->response->setOutput($this->load->view('default/template/multimerch/account/order.tpl', $this->data));
		}
	}

	protected function order_conversation($cond = array()){
		$this->data['seller_histories'] = array();

		$this->data['active_tab'] = isset($this->request->get['tab']) ? $this->request->get['tab'] : false;
		$this->data['order_id'] = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;

		// Customer or seller
		$referrer = isset($cond['referrer']) ? (int)$cond['referrer'] : self::REFERRER_CUSTOMER;

		// Conditions for suborders
		$fetch_conditions = array('order_id' => $this->data['order_id']);

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->data['order_id']);

		// if customer is not customer - view only related suborder
		if ($this->customer->getId() != $order_info['customer_id']){
			$fetch_conditions['seller_id'] = $this->customer->getId();
		}

		$suborders = MsLoader::getInstance()->MsSuborder->getSuborders($fetch_conditions);

		foreach ($suborders as $key => $sub) {
			if (!$this->data['active_tab'] && $key == 0){
				$this->data['active_tab'] = $sub['suborder_id'];
			}

			$suborder_histories = array();

			$seller = MsLoader::getInstance()->MsSeller->getSeller($sub['seller_id']);

			$this->load->model('account/customer');
			$customer = $this->model_account_customer->getCustomer($order_info['customer_id']);

			if($referrer == self::REFERRER_CUSTOMER) {
				// fetch histories
				$histories = MsLoader::getInstance()->MsSuborder->getSuborderHistory(array(
					'suborder_id' => $sub['suborder_id']
				));

				// format histories
				foreach ($histories as $h) {
					$suborder_histories[] = array(
						'date_added' => date($this->language->get('date_format_short'), strtotime($h['date_added'])),
						'status' => $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $h['order_status_id'])),
						'comment' => $h['comment']
					);
				}
			}

			$order_messages = array();

			$participant = ($referrer == self::REFERRER_CUSTOMER) ? $seller['ms.nickname'] : $customer['firstname'] . ' ' . $customer['lastname'];
			$conversation_title = $this->language->get('ms_account_conversations_with') . ' ' . $participant;

			if ($this->config->get('mmess_conf_enable') == 1){
				$conversation = $this->MsLoader->MsConversation->getOrderConversation((int)$this->request->get['order_id'], (int)$sub['suborder_id']);

				if (isset($conversation['conversation_id'])){
					$order_messages = $this->MsLoader->MsMessage->getMessages(
						array(
							'conversation_id' => (int)$conversation['conversation_id']
						),
						array(
							'order_by'  => 'date_created',
							'order_way' => 'ASC',
						)
					);

					foreach ($order_messages as $omk => $m) {
						$sender_type_id = $m['from_admin'] ? MsConversation::SENDER_TYPE_ADMIN : ($m['from'] == $sub['seller_id'] ? MsConversation::SENDER_TYPE_SELLER : MsConversation::SENDER_TYPE_CUSTOMER);
						$sender = $m['from_admin'] ? $m['user_sender'] : ($m['seller_sender'] && $sender_type_id == MsConversation::SENDER_TYPE_SELLER ? $m['seller_sender'] : $m['customer_sender']);

						$order_messages[$omk] = array_merge(
							$m,
							array(
								'date_created' => date($this->language->get('datetime_format'), strtotime($m['date_created'])),
								'sender_type_id' => $sender_type_id,
								'sender' => ((utf8_strlen($sender) > 20) ? utf8_substr($sender, 0, 20) . '..' : $sender) . ($m['from_admin'] ? ' (' . $this->language->get('ms_account_conversations_sender_type_' . MsConversation::SENDER_TYPE_ADMIN) . ')': '')
							)
						);
					}
				}
			}

			// assign histories
			$this->data['seller_histories'][$sub['seller_id']] = array(
				'seller_id' => $sub['seller_id'],
				'suborder_id' => $sub['suborder_id'],
				'suborder_status' => $this->MsLoader->MsSuborder->getSuborderStatus(array(
					'order_id' => $this->data['order_id'],
					'seller_id' => $sub['seller_id'],
				)),
				'conversation_title' => $conversation_title,
				'order_messages' => $order_messages,
				'participant' => $participant,
				'entries' => $suborder_histories
			);
		}
	}

	public function customerOrderConversation() {
		$this->order_conversation(array('referrer' => self::REFERRER_CUSTOMER));

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/account/order_info.tpl')) {
			$this->response->setOutput($this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/account/order_info.tpl', $this->data));
		} else {
			$this->response->setOutput($this->load->view('default/template/multimerch/account/order_info.tpl', $this->data));
		}
	}

	public function sellerOrderConversation() {
		$this->order_conversation(array('referrer' => self::REFERRER_SELLER));

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multiseller/account-order-info-conversation.tpl')) {
			$this->response->setOutput($this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multiseller/account-order-info-conversation.tpl', $this->data));
		} else {
			$this->response->setOutput($this->load->view('default/template/multiseller/account-order-info-conversation.tpl', $this->data));
		}
	}

	public function jxUploadAttachment() {
		$this->load->language('tool/upload');
		$this->load->language('multiseller/multiseller');

		$json = array();

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Validate file extension
			$json['error'] = $this->MsLoader->MsFile->checkFile($this->request->files['file'], $this->config->get('msconf_msg_allowed_file_types'));

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (empty($json['error'])) {
			unset($json['error']);

			// Hide the uploaded file name so people can not link to it directly.
			$file = $filename . '.' . token(32);

			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);

			$this->load->model('tool/upload');
			$json['code'] = $this->model_tool_upload->addUpload($filename, $file);
			$json['filename'] = $filename;
			$json['success'] = $this->language->get('text_upload');
		}

		return $this->response->setOutput(json_encode($json));
	}
}