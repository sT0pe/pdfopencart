<?php
use Dompdf\Dompdf;
class ControllerMultimerchAccountOffer extends Controller
{
	private $error = array();

	public function index() {

		$this->document->addScript('catalog/view/javascript/ms-common.js');
		$this->document->addScript('catalog/view/javascript/account-settings.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.html5.js');

		$this->load->model('catalog/product');
		$this->load->language('checkout/cart');
		$this->document->setTitle($this->language->get('offer_title'));

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('common/home'),
			'text' => $this->language->get('text_home')
		);
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('multimerch/account_offer'),
			'text' => $this->language->get('offer_title')
		);


		$data['heading_title'] = $this->language->get('offer_title');

		$data['text_recurring_item'] = $this->language->get('text_recurring_item');
		$data['text_next'] = $this->language->get('text_next');
		$data['text_next_choice'] = $this->language->get('text_next_choice');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');

		$data['button_update'] = $this->language->get('button_update');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_shopping'] = $this->language->get('button_shopping');
		$data['button_checkout'] = $this->language->get('button_checkout');
		$data['button_offer'] = $this->language->get('button_offer');

			if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$data['error_warning'] = $this->language->get('error_stock');
			} elseif (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
			} else {
				$data['attention'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			$data['action'] = $this->url->link('checkout/cart/edit', '', true);

			if ($this->config->get('config_cart_weight')) {
				$data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
			} else {
				$data['weight'] = '';
			}

			$this->load->model('tool/image');
			$this->load->model('tool/upload');

			$data['products'] = array();

			$products = $this->cart->getProducts();

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}

				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_cart_width'), $this->config->get($this->config->get('config_theme') . '_image_cart_height'));
				} else {
					$image = '';
				}

				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				// Display prices
				$unit_price = 0;
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

					$price = $this->currency->format($unit_price, $this->session->data['currency']);
					$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
				} else {
					$price = false;
					$total = false;
				}

				$recurring = '';

				if ($product['recurring']) {
					$frequencies = array(
						'day'        => $this->language->get('text_day'),
						'week'       => $this->language->get('text_week'),
						'semi_month' => $this->language->get('text_semi_month'),
						'month'      => $this->language->get('text_month'),
						'year'       => $this->language->get('text_year'),
					);

					if ($product['recurring']['trial']) {
						$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
					}

					if ($product['recurring']['duration']) {
						$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					} else {
						$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					}
				}

				$data['products'][] = array(
					'product_id'=> $product['product_id'],
					'cart_id'   => $product['cart_id'],
					'thumb'     => $image,
					'name'      => $product['name'],
					'model'     => $product['model'],
					'option'    => $option_data,
					'recurring' => $recurring,
					'quantity'  => $product['quantity'],
					'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'reward'    => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
					'price'     => $price,
					'unit_price'=> $unit_price,
					'total'     => $total,
					'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
				);
			}

			// Totals
			$this->load->model('extension/extension');

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$sort_order = array();

				$results = $this->model_extension_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);

						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totals);
			}

			$data['totals'] = array();

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
				);
			}

			$data['continue'] = $this->url->link('common/home');
			$data['checkout'] = $this->url->link('checkout/checkout', '', true);
			$data['offer']      = $this->url->link('multimerch/account_offer', '', true);
			$data['offer_add'] = $this->url->link('multimerch/account_offer/submit', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('multimerch/account/offer', $data));
	}


	public function uploadImage() {
		$json = array();
		$file = array();

		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}

		foreach ($_FILES as $file) {
			$errors = $this->MsLoader->MsFile->checkImage($file);

			if ($errors) {
				$json['errors'] = array_merge($json['errors'], $errors);
			} else {
				$fileName = $this->MsLoader->MsFile->uploadImage($file);
				$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
				$json['files'][] = array(
					'name' => $fileName,
					'thumb' => $thumbUrl
				);
			}
		}

		return $this->response->setOutput(json_encode($json));
	}


	public function submit(){

		if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()){

			if(isset($_POST['offer_id']) && $_POST['offer_id'] != ''){
				$offer_id = $_POST['offer_id'];
			}

			if(isset($_POST['submit']) && $_POST['submit'] == 'save'){

				if( isset($offer_id) ){

					$this->MsLoader->MsOffer->saveOffer( $_POST );
					$this->session->data['success'] = 'Offer <a href="' . $this->url->link("seller/account-offer/viewOffer", "offer_id=" . $offer_id, "SSL") . '">' . $_POST['name'] . '</a> have been saved!';
					$this->response->redirect($this->url->link('seller/account-offer'));

				} else {

					$this->newOffer();
				}

			} elseif(isset($_POST['submit']) && $_POST['submit'] == 'new'){

				$this->newOffer();

			} elseif(isset($_POST['submit']) && $_POST['submit'] == 'pdf'){

				if(isset($offer_id)){

					$this->MsLoader->MsOffer->saveOffer( $_POST );
					$offer_id = $_POST['offer_id'];

				} else {
					$offer_id = $this->newOffer();
				}

				require_once(DIR_SYSTEM . 'library/dompdf/autoload.inc.php');

				$dompdf = new Dompdf();
				$html = '';
				ob_start();
				$this->pdf($offer_id);
				$html .= ob_get_clean();

				$dompdf->loadHtml($html);
				$dompdf->setPaper('A4', 'portrait');
				$dompdf->render();
				$dompdf->stream();

			} elseif(isset($_POST['submit']) && $_POST['submit'] == 'mail'){
				//send mail
			}

		} else {

			$this->document->addScript('catalog/view/javascript/ms-common.js');
			$this->document->addScript('catalog/view/javascript/account-settings.js');
			$this->document->addScript('catalog/view/javascript/plupload/plupload.js');
			$this->document->addScript('catalog/view/javascript/plupload/plupload.html5.js');

			$this->load->model('catalog/product');
			$this->load->language('checkout/cart');
			$this->document->setTitle($this->language->get('offer_title'));

			$data['breadcrumbs'] = array();
			$data['breadcrumbs'][] = array(
				'href' => $this->url->link('common/home'),
				'text' => $this->language->get('text_home')
			);
			$data['breadcrumbs'][] = array(
				'href' => $this->url->link('multimerch/account_offer'),
				'text' => $this->language->get('offer_title')
			);


			$data['heading_title'] = $this->language->get('offer_title');

			$data['text_recurring_item'] = $this->language->get('text_recurring_item');
			$data['text_next'] = $this->language->get('text_next');
			$data['text_next_choice'] = $this->language->get('text_next_choice');

			$data['column_image'] = $this->language->get('column_image');
			$data['column_name'] = $this->language->get('column_name');
			$data['column_model'] = $this->language->get('column_model');
			$data['column_quantity'] = $this->language->get('column_quantity');
			$data['column_price'] = $this->language->get('column_price');
			$data['column_total'] = $this->language->get('column_total');

			$data['button_update'] = $this->language->get('button_update');
			$data['button_remove'] = $this->language->get('button_remove');
			$data['button_shopping'] = $this->language->get('button_shopping');
			$data['button_checkout'] = $this->language->get('button_checkout');
			$data['button_offer'] = $this->language->get('button_offer');

			if (isset($this->error['name'])) {
				$data['error_name'] = $this->error['name'];
			}

			if (isset($this->error['date_start'])) {
				$data['error_date_start'] = $this->error['date_start'];
			}

			if (isset($this->error['date_end'])) {
				$data['error_date_end'] = $this->error['date_end'];
			}

			if (isset($this->error['by_name'])) {
				$data['error_by_name'] = $this->error['by_name'];
			}

			if (isset($this->error['for_name'])) {
				$data['error_for_name'] = $this->error['for_name'];
			}

			if (isset($this->error['by_nip'])) {
				$data['error_by_nip'] = $this->error['by_nip'];
			}

			if (isset($this->error['for_nip'])) {
				$data['error_for_nip'] = $this->error['for_nip'];
			}

			if (isset($this->error['for_address'])) {
				$data['error_for_address'] = $this->error['for_address'];
			}

			if (isset($this->error['by_address'])) {
				$data['error_by_address'] = $this->error['by_address'];
			}

			if (isset($this->error['by_phone'])) {
				$data['error_by_phone'] = $this->error['by_phone'];
			}

			if (isset($this->error['for_phone'])) {
				$data['error_for_phone'] = $this->error['for_phone'];
			}

			if (isset($this->request->post['name'])) {
				$data['offer_info']['offer_name'] = $this->request->post['name'];
			}

			if (isset($this->request->post['date_start'])) {
				$data['offer_info']['date_start'] = $this->request->post['date_start'];
			}

			if (isset($this->request->post['date_end'])) {
				$data['offer_info']['date_end'] = $this->request->post['date_end'];
			}

			if (isset($this->request->post['delivery'])) {
				$data['offer_info']['delivery_cost'] = $this->request->post['delivery'];
			}

			if (isset($this->request->post['prepayment'])) {
				$data['offer_info']['prepayment'] = $this->request->post['prepayment'];
			}

			if (isset($this->request->post['service_cost'])) {
				$data['offer_info']['service_cost'] = $this->request->post['service_cost'];
			}

			if (isset($this->request->post['work_cost']) && $this->request->post['work_cost'] == 'on') {
				$data['offer_info']['show_work_cost'] = 1;
			}

			if (isset($this->request->post['by_name'])) {
				$data['offer_info']['offer_by_name'] = $this->request->post['by_name'];
			}

			if (isset($this->request->post['by_nip'])) {
				$data['offer_info']['offer_by_nip'] = $this->request->post['by_nip'];
			}

			if (isset($this->request->post['by_address'])) {
				$data['offer_info']['offer_by_address'] = $this->request->post['by_address'];
			}

			if (isset($this->request->post['by_phone'])) {
				$data['offer_info']['offer_by_phone'] = $this->request->post['by_phone'];
			}

			if (isset($this->request->post['for_name'])) {
				$data['offer_info']['offer_for_name'] = $this->request->post['for_name'];
			}

			if (isset($this->request->post['for_nip'])) {
				$data['offer_info']['offer_for_nip'] = $this->request->post['for_nip'];
			}

			if (isset($this->request->post['for_address'])) {
				$data['offer_info']['offer_for_address'] = $this->request->post['for_address'];
			}

			if (isset($this->request->post['for_phone'])) {
				$data['offer_info']['offer_for_phone'] = $this->request->post['for_phone'];
			}


			if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$data['error_warning'] = $this->language->get('error_stock');
			} elseif (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
			} else {
				$data['attention'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			$data['action'] = $this->url->link('checkout/cart/edit', '', true);

			if ($this->config->get('config_cart_weight')) {
				$data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
			} else {
				$data['weight'] = '';
			}

			$this->load->model('tool/image');
			$this->load->model('tool/upload');

			$data['products'] = array();
			$this->load->model('catalog/product');

			$products = array();
			foreach (isset($_POST['product_id']) ? $_POST['product_id'] : $products as $product_id){
				$products[] = $this->model_catalog_product->getProduct($product_id);
			}

			foreach ($products as $product) {

				$product_total = 0;
				$stock = true;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}

				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_cart_width'), $this->config->get($this->config->get('config_theme') . '_image_cart_height'));
				} else {
					$image = '';
				}

				// Display prices
				$unit_price = 0;
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

					$price = $this->currency->format($unit_price, $this->session->data['currency']);
					$total = $this->currency->format($_POST['final_price'][$product['product_id']] * $_POST['quantity'][$product['product_id']], $this->session->data['currency']);
				} else {
					$price = false;
					$total = false;
				}

				if ($product['subtract'] && (!$product['quantity'] || ($product['quantity'] < $_POST['quantity'][$product['product_id']]))) {
					$stock = false;
				}

				$data['products'][] = array(
					'product_id'  => $product['product_id'],
					'thumb'       => $image,
					'name'        => $product['name'],
					'model'       => $product['model'],
					'option'      => false,
					'quantity'    => $_POST['quantity'][$product['product_id']],
					'stock'       => $stock ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'reward'      => false,
					'price'       => $price,
					'recurring'   => false,
					'unit_price'  => $unit_price,
					'retail_price'=> $_POST['retail_price'][$product['product_id']],
					'discount'    => $_POST['discount'][$product['product_id']],
					'price_ex_tax'=> $_POST['seller_price'][$product['product_id']],
					'price_inc_tax'=>$_POST['final_price'][$product['product_id']],
					'total'       => $total,
					'href'        => $this->url->link('product/product', 'product_id=' . $product['product_id'])
				);
			}


			// Totals
			$this->load->model('extension/extension');

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$sort_order = array();

				$results = $this->model_extension_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);

						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totals);
			}

			$data['totals'] = array();

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
				);
			}

			$data['continue'] = $this->url->link('common/home');
			$data['checkout'] = $this->url->link('checkout/checkout', '', true);
			$data['offer']      = $this->url->link('multimerch/account_offer', '', true);
			$data['offer_add'] = $this->url->link('multimerch/account_offer/submit', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');


			$this->response->setOutput($this->load->view('multimerch/account/offer', $data));
		}

	}

	private function validate(){

		if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
			$this->error['name'] = 'Caption must be between 1 and 32 characters!';
		}

		if ($this->request->post['date_start'] == '') {
			$this->error['date_start'] = 'Date does not appear to be valid!';
		}

		if ($this->request->post['date_end'] == '') {
			$this->error['date_end'] = 'Date does not appear to be valid!';
		}

		if ((utf8_strlen(trim($this->request->post['by_name'])) < 1) || (utf8_strlen(trim($this->request->post['by_name'])) > 32)) {
			$this->error['by_name'] = 'Name must be between 1 and 32 characters!';
		}

		if ((utf8_strlen(trim($this->request->post['for_name'])) < 1) || (utf8_strlen(trim($this->request->post['for_name'])) > 32)) {
			$this->error['for_name'] = 'Name must be between 1 and 32 characters!';
		}

		if ($this->request->post['by_nip'] == '') {
			$this->error['by_nip'] = 'NIP does not appear to be valid!';
		}

		if ($this->request->post['for_nip'] == '') {
			$this->error['for_nip'] = 'NIP does not appear to be valid!';
		}

		if ((utf8_strlen(trim($this->request->post['by_address'])) < 3) || (utf8_strlen(trim($this->request->post['by_address'])) > 128)) {
			$this->error['by_address'] = 'Address must be between 3 and 128 characters!';
		}

		if ((utf8_strlen(trim($this->request->post['for_address'])) < 3) || (utf8_strlen(trim($this->request->post['for_address'])) > 128)) {
			$this->error['for_address'] = 'Address must be between 3 and 128 characters!';
		}

		if ((utf8_strlen($this->request->post['by_phone']) < 3) || (utf8_strlen($this->request->post['by_phone']) > 32)) {
			$this->error['by_phone'] = 'Telephone must be between 3 and 32 characters!';
		}

		if ((utf8_strlen($this->request->post['for_phone']) < 3) || (utf8_strlen($this->request->post['for_phone']) > 32)) {
			$this->error['for_phone'] = 'Telephone must be between 3 and 32 characters!';
		}

		return !$this->error;
	}

	public function addToCart(){

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];

			foreach ($quantity as $key => $value) {
				$this->cart->add($key, $value);
			}
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);

			$json['success'] = 'Products was added to the cart!';

			$this->load->language('checkout/cart');

			// Totals
			$this->load->model('extension/extension');

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$sort_order = array();

				$results = $this->model_extension_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);

						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totals);
			}

			$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));

		} else {
			$json['error'] = 'Nothing to add!';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function newOffer(){

		$this->session->data['success'] = 'Offer was successfully created!';

		$offer_id = $this->MsLoader->MsOffer->addOffer( $this->customer->getId(), $_POST );
		$this->MsLoader->MsOffer->addOfferProducts( $offer_id, $_POST );

		$this->response->redirect($this->url->link('seller/account-offer'));

		return $offer_id;
	}

	public function addProduct(){

		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('tool/upload');

		$product = $this->model_catalog_product->getProduct($_POST['product_id']);
		$href = $this->url->link('product/product', 'product_id=' . $product['product_id']);

		$quantity = $_POST['quantity'];
		$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

		if ((float)$product['special']) {
			$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			$total = $this->currency->format($product['special']* 1.2 * $quantity, $this->session->data['currency']);
		} else {
			$special = false;
			$total = $this->currency->format($product['price'] * 1.2 * $quantity, $this->session->data['currency']);
		}

		if ($product['image']) {
			$image = $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_cart_width'), $this->config->get($this->config->get('config_theme') . '_image_cart_height'));
		} else {
			$image = '';
		}

		$html = '';
		$html .= '<tr>';
		$html .= '<td class="text-center"><input name="remove-row" type="checkbox"/></td>';
		$html .= '<td class="text-center"><input type="hidden" name="product_id[]" value="'. $product["product_id"] .'" /></td>';

		$html .= '<td class="text-center">';

		if ($image) {
			$html .= '<a href="' . $href . '"><img src="'. $image .'" alt="'. $product["name"] .'" title="'. $product["name"] .'" class="img-thumbnail" /></a>';
		}

		$html .= '</td>';
		$html .= '<td class="text-left"><a href="' . $href . '">' . $product["name"] . '</a></td>';
		$html .= '<td class="text-left">' . $product["model"] . '</td>';

		if($special){
			$html .= '<td class="text-right">' . $special . '</td>';
			$html .= '<td class="text-center"><input id="retail-price-'.$product["product_id"].'" name="retail_price['.$product["product_id"].']" value="' . sprintf("%.2f", $product['special']) . '" type="number" min="0" step="any" class="form-control" onchange="newRetailPrice('. $product["product_id"] .');" /></td>';
			$html .= '<td class="text-center"><input id="discount-'.$product["product_id"].'" name="discount['.$product["product_id"].']" value="0" type="number" min="0" max="100" step="any" class="form-control" onchange="newRetailPrice('.$product["product_id"].');" /></td>';
			$html .= '<td class="text-center"><input id="seller-price-'.$product["product_id"].'" name="seller_price['.$product["product_id"].']" value="' . sprintf("%.2f", $product['special']) . '" type="number" min="0" step="any" class="form-control" onchange="newSellerPrice('.$product["product_id"].');" /></td>';
			$html .= '<td class="text-center"><span id="tax-'.$product["product_id"].'">20</span></td>';
			$html .= '<td class="text-center"><input id="final-price-'.$product["product_id"].'" name="final_price['.$product["product_id"].']" type="number" step="any" class="form-control" value="'. sprintf("%.2f", $product['special'] * 1.2) .'" onchange="newFinalPrice('.$product["product_id"].')" /></td>';
		} else {
			$html .= '<td class="text-right">' . $price . '</td>';
			$html .= '<td class="text-center"><input id="retail-price-'.$product["product_id"].'" name="retail_price['.$product["product_id"].']" value="' . sprintf("%.2f", $product["price"]) . '" type="number" min="0" step="any" class="form-control" onchange="newRetailPrice('. $product["product_id"] .');" /></td>';
			$html .= '<td class="text-center"><input id="discount-'.$product["product_id"].'" name="discount['.$product["product_id"].']" value="0" type="number" min="0" max="100" step="any" class="form-control" onchange="newRetailPrice('.$product["product_id"].');" /></td>';
			$html .= '<td class="text-center"><input id="seller-price-'.$product["product_id"].'" name="seller_price['.$product["product_id"].']" value="' . sprintf("%.2f", $product["price"]) . '" type="number" min="0" step="any" class="form-control" onchange="newSellerPrice('.$product["product_id"].');" /></td>';
			$html .= '<td class="text-center"><span id="tax-'.$product["product_id"].'">20</span></td>';
			$html .= '<td class="text-center"><input id="final-price-'.$product["product_id"].'" name="final_price['.$product["product_id"].']" type="number" step="any" class="form-control" value="'. sprintf("%.2f", $product["price"] * 1.2) .'" onchange="newFinalPrice('.$product["product_id"].')" /></td>';
		}

		$html .= '<td class="text-left"><input id="quantity-' . $product["product_id"] . '" name="quantity[' . $product["product_id"] . ']" value="'. $_POST["quantity"] . '" type="number" step="any" min="1" onchange="newQuantity(' . $product["product_id"] . ');" size="1" class="form-control" /></td>';
		$html .= '<td class="text-right"><span id="total-' . $product["product_id"] . '">' . $total . '</span></td>';
		$html .= '<td class="text-center text-danger"><a class="btn btn-danger" onclick="removeProduct(this);"><i class="fa fa-times-circle"></i></a></td>';
		$html .= '</tr>';

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($html));
	}


	private function pdf($offer_id){

		$this->load->model('account/offer');
		$offer_info = $this->model_account_offer->getOffer($offer_id);

		$this->load->model('tool/image');
		$this->load->model('tool/upload');

		$products = array();

		$products = $this->MsLoader->MsOffer->getProducts($offer_id);
		$result = array();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_cart_width'), $this->config->get($this->config->get('config_theme') . '_image_cart_height'));
			} else {
				$image = '';
			}

			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
				);
			}

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

				$price = $this->currency->format($unit_price, $this->session->data['currency']);
				$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
			} else {
				$price = false;
				$total = false;
			}

			$recurring = '';

			if ($product['recurring']) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($product['recurring']['trial']) {
					$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
				}

				if ($product['recurring']['duration']) {
					$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
				} else {
					$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
				}
			}

			if(isset($product['retail_price']) && $product['retail_price'] != ''){
				$price_ex_tax  = $product['retail_price']-$product['retail_price']*$product['discount']/100;
				$price_inc_tax = ($product['retail_price']-$product['retail_price']*$product['discount']/100)*1.20;
			} else {
				$price_ex_tax  = 0;
				$price_inc_tax = 0;
			}

			$result[] = array(
				'product_id'=> $product['product_id'],
				'offer_id'  => $product['offer_id'],
				'thumb'     => $image,
				'name'      => $product['name'],
				'model'     => $product['model'],
				'option'    => $option_data,
				'recurring' => $recurring,
				'quantity'  => $product['quantity'],
				'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				'reward'    => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
				'price'     => $price,
				'discount'      => $product['discount'],
				'price_netto'  => $this->currency->format($price_ex_tax,$this->session->data['currency']),
				'price_brutto' => $this->currency->format($price_inc_tax,$this->session->data['currency']),
				'total'     =>  $this->currency->format(($price_inc_tax != 0 ? $price_inc_tax : substr($product['price'], 1)) * $product['quantity'], $this->session->data['currency']),
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

		include DIR_TEMPLATE . 'default/template/multimerch/account/offer_pdf.tpl' ;
	}
}