<?php
class ControllerCustomerReview extends Controller
{
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$order_id = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
		$product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;

		$review_status = $this->config->get('msconf_reviews_enable');
		$order_created_by_customer = $this->MsLoader->MsOrderData->isOrderCreatedByCustomer($order_id, $this->customer->getId());

		if(!$review_status || !$order_created_by_customer) {
			$this->response->redirect($this->url->link('account/order', '', 'SSL'));
			return;
		}

		$data = array_merge($this->load->language('multiseller/multiseller'), $this->load->language('account/account'));

		$this->document->setTitle($this->language->get('heading_title'));

		// Script and style for star ratings
		$this->MsLoader->MsHelper->addStyle('star-rating');
		$this->document->addScript('catalog/view/javascript/star-rating.js');
		$this->document->addScript('catalog/view/javascript/multimerch/account-customer-product-review.js');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('ms_account_order_history'),
			'href' => $this->url->link('account/order', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('ms_order_feedback'),
			'href' => $this->url->link('customer/review', 'product_id=' . $product_id . '&order_id=' . $order_id, 'SSL')
		);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		$this->load->model('account/order');
		$this->load->model('tool/image');

		$data['order_id'] = $order_id;
		$data['product_id'] = $product_id;

		$products = $this->model_account_order->getOrderProducts($order_id);
		$order_products_ids = array();

		foreach($products as $key => $product) {
			$order_products_ids[] = $product['product_id'];

			$products[$key]['options'] = $this->model_account_order->getOrderOptions($product['order_id'], $product['order_product_id']);
			$products[$key]['seller'] = $this->MsLoader->MsSeller->getSeller($this->MsLoader->MsProduct->getSellerId($product['product_id']));
			$products[$key]['price'] = $this->currency->format($this->tax->calculate($products[$key]['price'], $this->config->get('config_tax')));
			$products[$key]['product'] = $this->model_catalog_product->getProduct($product['product_id']);
			if($products[$key]['product']['image']) {
				$products[$key]['product']['image'] = $this->model_tool_image->resize($products[$key]['product']['image'], $this->config->get('msconf_seller_avatar_seller_profile_image_width'), $this->config->get('msconf_seller_avatar_seller_profile_image_height'));
			} else {
				$products[$key]['product']['image'] = $this->model_tool_image->resize('no_image.png', $this->config->get('msconf_seller_avatar_seller_profile_image_width'), $this->config->get('msconf_seller_avatar_seller_profile_image_height'));
			}
			$products[$key]['review'] = $this->MsLoader->MsReview->getReviews(array('order_product_id' => $product['order_product_id'], 'author_id' => $this->customer->getId()));
		}

		if(!in_array($product_id, $order_products_ids)) {
			$this->response->redirect($this->url->link('account/order', '', 'SSL'));
			return;
		}

		$data['products'] = $products;

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/customer/review.tpl')) {
			$this->response->setOutput($this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/customer/review.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/customer/review.tpl', $data));
		}
	}

	public function createOrUpdate() {
		$this->_validatePost();

		$this->load->language('multiseller/multiseller');

		$review_data = array();

		$review_data['author_id'] = $this->customer->getId();
		$review_data['product_id'] = $this->request->post['product_id'];
		$review_data['order_product_id'] = $this->request->post['order_product_id'];
		$review_data['order_id'] = $this->request->post['order_id'];
		$review_data['rating'] = $this->request->post['rating'];
		$review_data['title'] = '';
		$review_data['comment'] = $this->request->post['rating_comment'];
		$review_data['description_accurate'] = isset($this->request->post['prod_desc_accurate']) ? $this->request->post['prod_desc_accurate'] : 1;

		$review_exists = $this->MsLoader->MsReview->getReviews($review_data);
		if(!empty($review_exists)) {
			$review_id = $review_exists[0]['review_id'];
			$this->MsLoader->MsReview->updateReview($review_id, $review_data);
		} else {
			$this->MsLoader->MsReview->createReview($review_data);

			$default_language = $this->config->get('config_language_id');
			$product = $this->MsLoader->MsProduct->getProduct($review_data['product_id']);
			$product['href'] = $this->url->link('product/product', 'product_id=' . $review_data['product_id']);

			$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
			$mailTransport = $serviceLocator->get('MailTransport');
			$mails = new \MultiMerch\Mail\Message\MessageCollection();

			$MailProductReviewedAdmin = $serviceLocator->get('MailProductReviewed', false)
				->setTo($this->config->get('config_email'))
				->setData(array(
					'product_name' => $product['languages'][$default_language]['name'],
					'product_href' => $product['href']
				));
			$mails->add($MailProductReviewedAdmin);

			if($product['seller_id']) {
				$seller = $this->MsLoader->MsSeller->getSeller($product['seller_id']);

				$MailProductReviewedSeller = clone $MailProductReviewedAdmin;
				$MailProductReviewedSeller->setTo($seller['c.email']);
				$mails->add($MailProductReviewedSeller);
			}

			$mailTransport->sendMails($mails);
		}

		$this->load->language('multiseller/multiseller');
		$this->session->data['success'] = $this->language->get('mm_review_submit_success');

		$this->response->redirect($this->url->link('account/order', '', 'SSL'));
	}

	protected function _validatePost() {
		if(empty($this->request->post)) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}
}