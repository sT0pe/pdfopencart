<?php
class ControllerMultimerchProductShipping extends Controller {

	public function index() {
		$data = $this->load->language('multiseller/multiseller');

		$product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
		$language_id = $this->config->get('config_language_id');

		$product = MsLoader::getInstance()->MsProduct->getProduct($product_id);
		$data['product_is_digital'] = (isset($product['shipping']) && $product['shipping'] == 1) ? 0 : 1;

		$product_shipping_data = MsLoader::getInstance()->MsProduct->getProductShipping($product_id, array('language_id' => $language_id));
		$seller_shipping_data = array();

		if($product_shipping_data) {
			$product_shipping_data['processing_time'] = sprintf($data['mm_product_shipping_processing_days'], $product_shipping_data['processing_time'], ($product_shipping_data['processing_time'] == 1 ? 'day' : 'days'));

			foreach ($product_shipping_data['locations'] as $key => &$location) {
				$location['cost'] = $this->MsLoader->MsHelper->trueCurrencyFormat($location['cost']);
				$location['additional_cost'] = $this->MsLoader->MsHelper->trueCurrencyFormat($location['additional_cost']);

				if((int)$location['delivery_time_id'] !== 0) {
					$delivery_time_name = MsLoader::getInstance()->MsShippingMethod->getShippingDeliveryTimes(
						array(
							'delivery_time_id' => $location['delivery_time_id'],
							'language_id' => $language_id
						)
					);
					$location['delivery_time_name'] = isset($delivery_time_name[$location['delivery_time_id']][$language_id]) ? $delivery_time_name[$location['delivery_time_id']][$language_id] : '-';
				} else {
					$location['delivery_time_name'] = $data['mm_checkout_shipping_ew_location_delivery_time_name'];
				}
			}
		} else {
			$seller_id = MsLoader::getInstance()->MsProduct->getSellerId($product_id);
			if($seller_id) {
				// Find seller shipping methods that are applicable for product's weight
				$seller_shipping_data = MsLoader::getInstance()->MsShippingMethod->getSellerShipping($seller_id);

				if($seller_shipping_data) {
					$seller_shipping_data['processing_time'] = sprintf($this->language->get('mm_product_shipping_processing_days'), $seller_shipping_data['processing_time'], ($seller_shipping_data['processing_time'] == 1 ? 'day' : 'days'));

					foreach ($seller_shipping_data['methods'] as $key => &$method) {
						$method['cost_fixed'] = $this->MsLoader->MsHelper->trueCurrencyFormat($method['cost_fixed']);
						$method['cost_pwu'] = $this->MsLoader->MsHelper->trueCurrencyFormat($method['cost_pwu']);
						$method['weight_from'] = $this->MsLoader->MsHelper->trueCurrencyFormat($method['weight_from']);
						$method['weight_to'] = $this->MsLoader->MsHelper->trueCurrencyFormat($method['weight_to']);
					}
				}
			}
		}

		$data['product_shipping'] = (!empty($product_shipping_data) && ($this->config->get('msconf_vendor_shipping_type') == 2 || ($this->config->get('msconf_vendor_shipping_type') == 3 && (int)$product_shipping_data['override'] == 1))) ? $product_shipping_data : array();
		$data['seller_shipping'] = (!empty($seller_shipping_data) && ($this->config->get('msconf_vendor_shipping_type') == 1 || $this->config->get('msconf_vendor_shipping_type') == 3)) ? $seller_shipping_data : array();

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/product/mm_shipping.tpl')) {
			$this->response->setOutput($this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/product/mm_shipping.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/product/mm_shipping.tpl', $data));
		}
	}
}