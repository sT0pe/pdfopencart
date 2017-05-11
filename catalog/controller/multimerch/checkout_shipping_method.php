<?php
class ControllerMultimerchCheckoutShippingMethod extends Controller {

	public function index()	{
		$data = array_merge($this->load->language('multiseller/multiseller'), $this->load->language('checkout/checkout'));

		if ($this->cart->hasShipping()) {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
		} else {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);
		}

		if (isset($this->session->data['shipping_address'])) {
			// Customer's shipping details
			$data['shipping_details'] = isset($this->session->data['shipping_address']) ? $this->session->data['shipping_address'] : '';
			$data['shipping_details']['customer_name'] = (isset($data['shipping_details']['firstname']) && isset($data['shipping_details']['lastname'])) ? ($data['shipping_details']['firstname'] . " " . $data['shipping_details']['lastname']) : '';
			$data['shipping_details']['customer_address'] = isset($data['shipping_details']['address_1']) ? ($data['shipping_details']['address_1'] . ($data['shipping_details']['address_2'] ? (',' . $data['shipping_details']['address_2']) : '')) : '';
			$shipping_country_id = isset($this->session->data['shipping_address']['country_id']) ? $this->session->data['shipping_address']['country_id'] : 0;
			$shipping_zone_id = isset($this->session->data['shipping_address']['zone_id']) ? $this->session->data['shipping_address']['zone_id'] : 0;

			// Customer's products in cart
			$cart_products = $this->cart->getProducts();

			// Get currency and prices formatting
			$currencies = $this->model_localisation_currency->getCurrencies();
			$decimal_place = $currencies[$this->session->data["currency"]]['decimal_place'];
			// Check whether product has appropriate shipping methods
			$data['error_no_shipping_methods'] = '';

			foreach ($cart_products as $cart_product) {
				// Detects which shipping will ba applied to each product
				$shipping_key = '';

				$seller_id = MsLoader::getInstance()->MsProduct->getSellerId($cart_product['product_id']);
				$seller_info = MsLoader::getInstance()->MsSeller->getSeller($seller_id);

				$this->load->model('tool/image');
				if ($cart_product['image']) {
					$image = $this->model_tool_image->resize($cart_product['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'));
				} else {
					$image = '';
				}

				$cart_product_info = array(
					'product_id' => $cart_product['product_id'],
					'name' => $cart_product['name'],
					'model' => $cart_product['model'],
					'price' => $cart_product['price'],
					'price_formatted' => $this->currency->format($cart_product['price'], $this->session->data["currency"]),
					'quantity' => $cart_product['quantity'],
					'weight' => $cart_product['weight'],
					'weight_class_id' => $cart_product['weight_class_id'],
					'image' => $image,
					'seller_name' => $seller_info ? $seller_info['ms.nickname'] : $this->language->get('ms_store_owner'),
					'shipping_required' => $cart_product['shipping'],
					'shipping_methods' => array()
				);

				// Search for fixed shipping rules for product
				$product_shipping_data = MsLoader::getInstance()->MsProduct->getProductShipping($cart_product['product_id'], array('language_id' => $this->config->get('config_language_id')));

				// If product is digital
				if(!$cart_product_info['shipping_required']) {
					$shipping_key = 'digital';
				} else {
					// If product has shipping data, `Fixed shipping` type is enabled or `Override combined shipping` option is enabled for a product
					if(!empty($product_shipping_data) && ($this->config->get('msconf_vendor_shipping_type') == 2 || ($this->config->get('msconf_vendor_shipping_type') == 3 && (int)$product_shipping_data['override'] == 1))) {
						// Get locations appropriate for customer's shipping country
						$appropriate_locations = array();
						foreach ($product_shipping_data['locations'] as $key => $location) {
							// Get location countries and cities in geo_zone_id
							$shipping_available = MsLoader::getInstance()->MsShippingMethod->getShippingAvailability(array(
								'country_id' => $shipping_country_id,
								'zone_id' => $shipping_zone_id,
								'geo_zone_id' => $location['to_geo_zone_id']
							));

							if ($shipping_available || (int)$location['to_geo_zone_id'] == 0) {
								$appropriate_locations[] = $location;
							}
						}

						// Calculations
						foreach ($appropriate_locations as $key => $appropriate_location) {
							$shipping_cost_calculated = round($appropriate_location['cost'], (int)$decimal_place);

							if ($cart_product['quantity'] > 1) {
								for ($i = 1; $i < $cart_product['quantity']; $i++) {
									$shipping_cost_calculated += round($appropriate_location['additional_cost'], (int)$decimal_place);
								}
							}

							$appropriate_locations[$key]['total_cost'] = $shipping_cost_calculated;
							$appropriate_locations[$key]['total_cost_formatted'] = $this->currency->format($shipping_cost_calculated, $this->session->data["currency"]);

							if ((int)$appropriate_location['delivery_time_id'] !== 0) {
								$delivery_time_name = MsLoader::getInstance()->MsShippingMethod->getShippingDeliveryTimes(array(
									'delivery_time_id' => $appropriate_location['delivery_time_id']
								));
								$appropriate_locations[$key]['delivery_time_name'] = isset($delivery_time_name[$appropriate_location['delivery_time_id']][$this->config->get('config_language_id')]) ? $delivery_time_name[$appropriate_location['delivery_time_id']][$this->config->get('config_language_id')] : $data['mm_not_specified'];
							} else {
								$appropriate_locations[$key]['delivery_time_name'] = $data['mm_checkout_shipping_ew_location_delivery_time_name'];
							}
						}

						$product_shipping_data['locations'] = $appropriate_locations;

						if (empty($product_shipping_data['locations']) && !$product_shipping_data['free_shipping'] && $cart_product['shipping']) {
							$data['error_no_shipping_methods'] = $this->language->get('mm_checkout_shipping_product_delete_warning');
						}

						$cart_product_info['shipping_methods'] = $product_shipping_data;
						$shipping_key = 'fixed_shipping';

						// If `Combined shipping` type or `Both` is enabled, and product doesn't have `Override combined shipping` option enabled
					} else if($this->config->get('msconf_vendor_shipping_type') == 1 || $this->config->get('msconf_vendor_shipping_type') == 3) {
						$shipping_key = 'combined_shipping';

						// If product doesn't have shipping data, and combined settings were not set
					} else {
						$shipping_key = 'no_shipping';
					}
				}

				$data['cart_products'][$seller_id][$shipping_key][] = $cart_product_info;
			}

			$data['cart_products'] = isset($data['cart_products']) ? $data['cart_products'] : array();
			$this->_calculateCombinedShipping($data['cart_products']);
		}

		$data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['button_continue'] = $this->language->get('button_continue');

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/checkout/shipping_method.tpl')) {
			$this->response->setOutput($this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/checkout/shipping_method.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/multimerch/checkout/shipping_method.tpl', $data));
		}
	}

	public function jxSave() {
		$this->load->language('checkout/checkout');
		$this->load->language('multiseller/multiseller');

		$json = array();

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate if shipping address has been set.
		if (!isset($this->session->data['shipping_address'])) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products, stock and shipping methods are selected for products.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}

			// Remove products that do not have shipping method
			if(isset($this->request->post['no_shipping_method'][$product['product_id']])) {
				$this->cart->remove($product['cart_id']);
			}
		}

		if(!isset($this->request->post['fixed_shipping_method']) && !isset($this->request->post['combined_shipping_method']) && !isset($this->request->post['free_shipping']) && !isset($this->request->post['no_shipping_method']) && !isset($this->request->post['digital'])) {
			$json['error']['warning'] = $this->language->get('mm_checkout_shipping_no_selected_methods');
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		if (!$json) {
			// Renew products array after validation
			$products = $this->cart->getProducts();

			// Get currency and prices formatting
			$currencies = $this->model_localisation_currency->getCurrencies();
			$decimal_place = $currencies[$this->config->get('config_currency')]['decimal_place'];

			if(isset($this->request->post['fixed_shipping_method'])) {
				foreach ($this->request->post['fixed_shipping_method'] as $product_id => $product_shipping_location_id) {
					$product_shipping_data = MsLoader::getInstance()->MsProduct->getProductShipping((int)$product_id, array(
						'language_id' => $this->config->get('config_language_id'),
						'product_shipping_location_id' => $product_shipping_location_id,
					));

					if(isset($product_shipping_data['locations']) && !empty($product_shipping_data['locations'])) {
						foreach ($products as $key => $product) {
							if ((int)$product['product_id'] == (int)$product_id) {
								$shipping_cost_calculated = round($product_shipping_data['locations'][0]['cost'], (int)$decimal_place);

								if ($product['quantity'] > 1) {
									for ($i = 1; $i < $product['quantity']; $i++) {
										$shipping_cost_calculated += round($product_shipping_data['locations'][0]['additional_cost'], (int)$decimal_place);
									}
								}

								$this->session->data['ms_cart_product_shipping']['fixed'][$product_id] = array(
									'shipping_method_id' => $product_shipping_data['locations'][0]['shipping_method_id'],
									'cost' => $shipping_cost_calculated
								);
							}
						}
					}
				}
			}

			if(isset($this->request->post['combined_shipping_method'])) {
				foreach ($this->request->post['combined_shipping_method'] as $seller_id => $shipping_method) {
					/**
					 * $combined_shipping_settings - selected combined shipping method settings
					 * $combined_shipping_settings = array(
					 * 	'0' => seller_shipping_location_id,
					 * 	'1' => combined_products_ids,
					 * );
					 **/
					$combined_shipping_settings = explode('-', $shipping_method);
					$combined_product_ids = explode(',', $combined_shipping_settings[1]);

					$seller_shipping_data = MsLoader::getInstance()->MsShippingMethod->getSellerShipping($seller_id, array(
						'seller_shipping_location_id' => $combined_shipping_settings[0],
					));

					foreach ($combined_product_ids as $product_id) {
						if(isset($seller_shipping_data['methods']) && !empty($seller_shipping_data['methods'])) {
							foreach ($products as $key => $product) {
								if ((int)$product['product_id'] == (int)$product_id) {
									$product_weight_formatted = $this->weight->convert($product['weight'], $product['weight_class_id'], $seller_shipping_data['methods'][0]['weight_class_id']);
									$shipping_cost_calculated = ($seller_shipping_data['methods'][0]['cost_fixed'] / count($combined_product_ids)) + $product_weight_formatted * $seller_shipping_data['methods'][0]['cost_pwu'];

									$this->session->data['ms_cart_product_shipping']['combined'][$product_id] = array(
										'shipping_method_id' => $seller_shipping_data['methods'][0]['shipping_method_id'],
										'cost' => $shipping_cost_calculated
									);
								}
							}
						}
					}
				}
			}

			if(isset($this->request->post['free_shipping'])) {
				foreach ($this->request->post['free_shipping'] as $product_id => $value) {
					$this->session->data['ms_cart_product_shipping']['free'][$product_id] = $value;
				}
			}

			if(isset($this->request->post['digital'])) {
				foreach ($this->request->post['digital'] as $product_id => $value) {
					$this->session->data['ms_cart_product_shipping']['free'][$product_id] = $value;
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxFormatPrice() {
		$json = array();

		$total_shipping_cost = 0;

		$currency_symbols = array(
			$this->currency->getSymbolLeft($this->session->data['currency']),
			$this->currency->getSymbolRight($this->session->data['currency']),
			$this->language->get('thousand_point')
		);

		if(isset($this->request->post['total_shipping_cost']) && is_array($this->request->post['total_shipping_cost'])) {
			foreach ($this->request->post['total_shipping_cost'] as $cost) {
				$cost = str_replace($currency_symbols, '', $cost);
				$total_shipping_cost += (float)$cost;
			}
		}

		$json['total_shipping_cost'] = $total_shipping_cost;
		$json['total_shipping_cost_formatted'] = $this->currency->format($total_shipping_cost, $this->session->data['currency'], 1.00000);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function _calculateCombinedShipping(&$cart_products) {
		foreach ($cart_products as $seller_id => $shipping_rules) {
			/**
			 * $shipping_rules - products sorted by shipping rule
			 * $shipping_rules = array(
			 * 	'fixed_shipping' => array(),
			 * 	'combined_shipping' => array()
			 * );
			 **/

			/** @var array Seller's combined shipping rules */
			$seller_shipping_data = MsLoader::getInstance()->MsShippingMethod->getSellerShipping($seller_id);

			// Go through all seller's shipping rules and find appropriate
			if(!empty($seller_shipping_data)) {
				$max_weight_formatted = isset($seller_shipping_data['methods'][0]) ? $this->weight->convert($seller_shipping_data['methods'][0]['weight_to'], $seller_shipping_data['methods'][0]['weight_class_id'], $this->config->get('config_weight_class_id')) : 0;

				foreach ($seller_shipping_data['methods'] as $method) {
					/** @var int Total cost of combined shipping method, sum of product shipping costs */
					$total_method_cost = 0;

					/** @var string Product ids in combined shipping, comma-separated */
					$combined_product_ids = '';

					/** @var bool Checks if shipping is available to customer's location */
					$shipping_available = MsLoader::getInstance()->MsShippingMethod->getShippingAvailability(
						array(
							'country_id' => $this->session->data['shipping_address']['country_id'],
							'zone_id' => $this->session->data['shipping_address']['zone_id'],
							'geo_zone_id' => $method['to_geo_zone_id']
						)
					);

					/** @var bool Checks if method allows to ship products wordlwide */
					$allowed_worldwide = (int)$method['to_geo_zone_id'] == 0 ? true : false;

					if ($shipping_available || $allowed_worldwide) {
						/** @var float Total weight of combined products */
						$combined_products_weight = 0;

						if (isset($shipping_rules['combined_shipping'])) {
							// Get total combined products weight
							foreach ($shipping_rules['combined_shipping'] as $product) {
								$combined_products_weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $method['weight_class_id']);
							}

							// If total weight meets method weight rules
							if ($combined_products_weight >= $method['weight_from'] && $combined_products_weight < $method['weight_to']) {
								foreach ($shipping_rules['combined_shipping'] as $key => $product) {
									/** @var float Product weight in method's weight units */
									$product_weight_formatted = $this->weight->convert($product['weight'], $product['weight_class_id'], $method['weight_class_id']);

									/** @var float Total product shipping cost */
									$product_shipping_cost = ($method['cost_fixed'] / count($shipping_rules['combined_shipping'])) + $product_weight_formatted * $method['cost_pwu'];
									$total_method_cost += $product_shipping_cost;
									$combined_product_ids .= ($product['product_id'] . ($product !== end($shipping_rules['combined_shipping']) ? ',' : ''));
								}

								$cart_products[$seller_id]['seller_combined_shipping_methods'][] = array(
									'seller_shipping_location_id' => $method['seller_shipping_location_id'],
									'shipping_method_id' => $method['shipping_method_id'],
									'shipping_method_name' => $method['shipping_method_name'],
									'delivery_time_id' => $method['delivery_time_id'],
									'delivery_time_name' => $method['delivery_time_name'],
									'to_geo_zone_id' => $method['to_geo_zone_id'],
									'total_cost' => $total_method_cost,
									'total_cost_formatted' => $this->currency->format($total_method_cost, $this->session->data["currency"])
								);

								$cart_products[$seller_id]['combined_product_ids'] = $combined_product_ids;
							} else {
								$method_max_weight_formatted = $this->weight->convert($method['weight_to'], $method['weight_class_id'], $this->config->get('config_weight_class_id'));
								if($max_weight_formatted > $method_max_weight_formatted) $max_weight_formatted = $method_max_weight_formatted;

								$cart_products[$seller_id]['combined_products_maxweight_exceeded'] = sprintf($this->language->get('mm_checkout_shipping_error_maxweight_exceeded'),
									($this->MsLoader->MsSeller->getSellerNickname($seller_id) ? $this->MsLoader->MsSeller->getSellerNickname($seller_id) : $this->language->get('ms_store_owner')),
									$this->weight->format($combined_products_weight, $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')),
									$this->weight->format($max_weight_formatted, $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')),
									($this->MsLoader->MsSeller->getSellerNickname($seller_id) ? $this->MsLoader->MsSeller->getSellerNickname($seller_id) : $this->language->get('ms_store_owner')),
									$this->url->link('checkout/cart')
								);
							}
						}
					}
				}
			}
		}
	}

}