<?php
class ModelTotalMmShippingTotal extends Model {
	public function getTotal($total) {
		$this->load->language('multiseller/multiseller');

		$shipping_total = (float)0.0;

		if ($this->cart->hasShipping() && isset($this->session->data['ms_cart_product_shipping'])) {
			foreach ($this->cart->getProducts() as $product) {
				if (isset($this->session->data['ms_cart_product_shipping']['fixed'][$product['product_id']]['cost'])) {
					$product_shipping_cost = $this->session->data['ms_cart_product_shipping']['fixed'][$product['product_id']]['cost'];
				} else if (isset($this->session->data['ms_cart_product_shipping']['combined'][$product['product_id']]['cost'])) {
					$product_shipping_cost = $this->session->data['ms_cart_product_shipping']['combined'][$product['product_id']]['cost'];
				} else {
					$product_shipping_cost = 0;
				}

				$shipping_total += $product_shipping_cost;
			}

			$total['totals'][] = array(
				'code'       => 'mm_shipping_total',
				'title'      => $this->language->get('mm_account_order_shipping_total'),
				'value'      => $shipping_total,
				'sort_order' => $this->config->get('mm_shipping_total_sort_order')
			);

			$total['total'] += $shipping_total;
		}
	}
}