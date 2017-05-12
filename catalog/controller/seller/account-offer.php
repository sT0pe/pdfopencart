<?php

class ControllerSellerAccountOffer extends ControllerSellerAccount {

	public function index() {

		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_offer_information'));

		$this->data['offer'] = $this->url->link('multimerch/account_offer', '', true);
		
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
				'text' => $this->language->get('ms_account_offers_breadcrumbs'),
				'href' => $this->url->link('seller/account-offer', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-offer');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}


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
		$this->load->model('account/offer');
		$offers = $this->MsLoader->MsOffer->getOffers(
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

		$total_offers = isset($offers[0]) ? $offers[0]['total_rows'] : 0;
		$this->load->model('tool/upload');
		$columns = array();

		$i = 1;

		foreach ($offers as $offer) {
			$offer_products = $this->MsLoader->MsOffer->getOfferProducts(array('offer_id' => $offer['offer_id'], 'seller_id' => $seller_id));


			$products = "";
			$offer_total = 0;

			foreach ($offer_products as $p) {
				$products .= "<p style='text-align:left'>";
				$products .= "<span class='name'>" . ($p['quantity'] > 1 ? "{$p['quantity']} x " : "") . "<a href='" . $this->url->link('product/product', 'product_id=' . $p['product_id'], 'SSL') . "'>{$p['name']}</a></span>";

				$products .= "<span class='total'>" . $this->currency->format($p['retail_price'] * $p['quantity']) . "</span>";
				$products .= "</p>";

				$offer_total += $p['retail_price'] * $p['quantity'];
			}

			$actions  = '<a class="icon-view" href="' . $this->url->link('seller/account-offer/viewOffer', 'offer_id=' . $offer['offer_id'], 'SSL') . '" title="' . $this->language->get('ms_view_modify') . '"><i class="fa fa-search"></i></a>';
			$actions .= '<a class="icon-invoice" target="_blank" href="' . $this->url->link('multimerch/account_offer/pdf', '', 'SSL') . '" title="' . $this->language->get('ms_view_invoice') . '"><i class="fa fa-file-text-o"></i></a>';
			$actions .= '<a class="icon-remove" href="' . $this->url->link('seller/account-offer/removeOffer', 'offer_id=' . $offer['offer_id'], 'SSL') . '" title="' . $this->language->get('ms_remove_offer') . '"><i class="fa fa-trash-o"></i></a>';


			$columns[] = array_merge(
				$offer,
				array(
					'offer_id' => $i,
					'caption' => '<a href="' . $this->url->link('seller/account-offer/viewOffer', 'offer_id=' . $offer['offer_id'], 'SSL') . '">' .$offer['offer_name'] . '</a>',
					'products' => $products,
					'date_created' => date($this->language->get('date_format_short'), strtotime($offer['date_created'])),
					'total_amount' => $this->currency->format($offer_total),
					'view_order' => $actions
				)
			);
			$i++;
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total_offers,
			'iTotalDisplayRecords' => $total_offers,
			'aaData' => $columns
		)));
	}



	public function viewOffer() {

		$this->document->addScript('catalog/view/javascript/ms-common.js');
		$this->document->addScript('catalog/view/javascript/account-settings.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.html5.js');

		$offer_id = isset($this->request->get['offer_id']) ? (int)$this->request->get['offer_id'] : 0;
		$this->load->model('account/offer');

		$data['offer_info'] = $this->model_account_offer->getOffer($offer_id);

		$this->load->language('checkout/cart');
		$this->document->setTitle($this->language->get('offer_title_view'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('common/home'),
			'text' => $this->language->get('text_home')
		);
		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('seller/account-offer/viewOffer', 'offer_id=' . $offer_id, 'SSL'),
			'text' => $this->language->get('offer_title_view')
		);


			$data['heading_title'] = $this->language->get('offer_title_view');
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

			// pasha
			$data['button_offer'] = $this->language->get('button_offer');
			$data['offer']      = $this->url->link('multimerch/account_offer', '', true);
			$data['offer_add'] = $this->url->link('multimerch/account_offer/submit', '', true);

			$data['continue'] = $this->url->link('common/home');
			$data['checkout'] = $this->url->link('checkout/checkout', '', true);

			if (!$this->MsLoader->MsOffer->hasStock($offer_id) && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
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

			$this->load->model('tool/image');
			$this->load->model('tool/upload');

			$data['products'] = array();

			$products = $this->MsLoader->MsOffer->getProducts($offer_id);

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

				$data['products'][] = array(
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

					'retail_price'  => $product['retail_price'],
					'discount'      => $product['discount'],
					'price_ex_tax'  => $price_ex_tax,
					'price_inc_tax' => $price_inc_tax,

					'total'     =>  $this->currency->format(($price_inc_tax != 0 ? $price_inc_tax : substr($product['price'], 1)) * $product['quantity'], $this->session->data['currency']),
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


			$this->load->model('extension/extension');
			$data['modules'] = array();
			$files = glob(DIR_APPLICATION . '/controller/extension/total/*.php');

			if ($files) {
				foreach ($files as $file) {
					$result = $this->load->controller('extension/total/' . basename($file, '.php'));

					if ($result) {
						$data['modules'][] = $result;
					}
				}
			}


			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('multimerch/account/offer', $data));
	}


	public function removeOffer() {

		$offer_id = isset($this->request->get['offer_id']) ? (int)$this->request->get['offer_id'] : 0;

		$this->MsLoader->MsOffer->removeOffer($offer_id);

		$this->session->data['success'] = 'Offer have been removed!';

		$this->response->redirect($this->url->link('seller/account-offer'));

	}


}