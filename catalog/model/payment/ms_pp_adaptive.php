<?php 
class ModelPaymentMSPPAdaptive extends Model {
	public function getMethod($address, $total) {
		if ($this->config->get('msppaconf_ppa_log_filename')){
			$log = new Log($this->config->get('msppaconf_ppa_log_filename'));
		}else{
			$log = new Log("ppa_paypal.log");
		}
		$this->load->language('payment/ms_pp_adaptive');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('msppaconf_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('msppaconf_total') > $total) {
			$status = false;
			if ($this->config->get('msppaconf_debug')) $log->write('PayPal Adaptive disabled: Insufficient order total');
		} elseif (!$this->config->get('msppaconf_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
			if ($this->config->get('msppaconf_debug')) $log->write('PayPal Adaptive disabled: No match for geo zone');
		}

		$store_owner = $this->config->get('msppaconf_receiver');
		if (empty($store_owner) || !filter_var($store_owner, FILTER_VALIDATE_EMAIL)) {
			$status = false;
			if ($this->config->get('msppaconf_debug')) $log->write('PayPal Adaptive disabled: Store owner PayPal address invalid or not specified');
		}

		// check valid paypal addresses
		$receivers = array();
		//$receivers[0]['ms.paypal'] = $this->config->get('msppaconf_receiver');
		foreach ($this->cart->getProducts() as $product) {
			// create unique receiver array element
			$seller_id = $this->MsLoader->MsProduct->getSellerId($product['product_id']);
			if (!isset($receivers[$seller_id])) {
				$seller = $this->MsLoader->MsSeller->getSeller($seller_id);

				// Get paypal address from "Paypal" payment gateway settings
				$paypal_address = $this->MsLoader->MsSetting->getSellerSettings(array(
					'seller_id' => $seller_id,
					'name' => MsPgPayment::SELLER_SETTING_PREFIX . 'paypal_pp_address',
					'single' => 1
				));

				$receivers[$seller_id] = $seller;
				$receivers[$seller_id]['amount'] = 0;
				$receivers[$seller_id]['ms.paypal'] = $paypal_address;
			}
		}

		if(count($receivers) > 5) {
			$status = false;
			if ($this->config->get('msppaconf_debug')) $log->write('PayPal Adaptive disabled: Too many receivers: ' . count($receivers));
		}

		foreach ($receivers as $receiver) {
			if (!isset($receiver['ms.paypal']) || empty($receiver['ms.paypal']) || !filter_var($receiver['ms.paypal'], FILTER_VALIDATE_EMAIL)) {
					$status = false;
					if ($this->config->get('msppaconf_debug')) $log->write('PayPal Adaptive disabled: Receiver PayPal address not specified: ' . (isset($receiver['name']) ? $receiver['name'] : 'No seller name') . '(' . (isset($receiver['ms.nickname']) ? $receiver['ms.nickname'] : 'No nickname') . ')');
					break;
			}
		}

		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY'
		);
		
		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			$status = false;
			if ($this->config->get('msppaconf_debug')) $log->write('PayPal Adaptive disabled: Currency not supported: ' . strtoupper($this->currency->getCode()));
		}
		
		$method_data = array();

		if ($status) {
			$method_data = array( 
				'code'       => 'ms_pp_adaptive',
				'title'      => $this->language->get('ppa_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('msppaconf_sort_order')
			);
		}

		return $method_data;
	}
}
?>