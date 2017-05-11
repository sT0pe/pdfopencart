<?php

class ControllerSellerAccountSetting extends ControllerSellerAccount {
	public function index() {
		$this->document->addScript('catalog/view/javascript/ms-common.js');
		$this->document->addScript('catalog/view/javascript/account-settings.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.html5.js');

		$this->load->model('localisation/country');
		$this->load->model('localisation/weight_class');

		$this->document->setTitle($this->language->get('ms_account_sellersetting_breadcrumbs'));

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
				'text' => $this->language->get('ms_account_sellersetting_breadcrumbs'),
				'href' => $this->url->link('seller/account-setting', '', 'SSL'),
			)
		));

		$this->data['seller_id'] = $this->customer->getId();
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		$this->data['payment_gateways'] = $this->_getPaymentGateways();

		$this->data['seller_shipping'] = $this->_getSellerShipping($this->data['seller_id']);
		$this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
		$this->data['delivery_times'] = $this->MsLoader->MsShippingMethod->getShippingDeliveryTimes();

		/* Seller shipping methods errors */
		if(empty($this->data['delivery_times'])) {
			$this->data['ssm_errors'][] = $this->language->get('ms_account_setting_ssm_error_no_dt');
		}

		$shipping_methods = $this->MsLoader->MsShippingMethod->getShippingCompanies(array(
			'language_id' => $this->config->get('config_language_id')
		));
		if(empty($shipping_methods)) {
			$this->data['ssm_errors'][] = $this->language->get('ms_account_setting_ssm_error_no_sm');
		}

		$geo_zones = $this->MsLoader->MsShippingMethod->getShippingGeoZones();
		if(empty($geo_zones)) {
			$this->data['ssm_errors'][] = $this->language->get('ms_account_setting_ssm_error_no_gz');
		}
		/* End seller shipping methods errors */

		//get seller settings
		$seller_settings = $this->MsLoader->MsSetting->getSellerSettings(array('seller_id' => $this->customer->getId()));
		$defaults = $this->MsLoader->MsSetting->getSellerDefaults();
		$this->data['settings'] = array_merge($defaults, $seller_settings);

		$this->data['settings']['slr_thumb'] = $this->MsLoader->MsFile->resizeImage($this->data['settings']['slr_logo'], $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/settings/default');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxSaveSellerInfo() {
		$json = array();
		$data = $this->request->post;

		if (isset($data['settings']['slr_logo']) && !empty($data['settings']['slr_logo'])) {
			if (!$this->MsLoader->MsFile->checkFileAgainstSession($data['settings']['slr_logo'])) {
				$json['errors']['settings[slr_logo]'] = $this->language->get('ms_error_file_upload_error');
			} else {
				$data['settings']['slr_logo'] = $this->MsLoader->MsFile->moveImage($data['settings']['slr_logo']);
			}
		} else {

		}

		if (isset($data['settings']['slr_website']) && !empty($data['settings']['slr_website'])) {
			$data['settings']['slr_website'] = $this->MsLoader->MsHelper->addHttp($data['settings']['slr_website']);
		}

		$validator = $this->MsLoader->MsValidator;

		$is_valid = $validator->validate(array(
			'name' => $this->language->get('ms_seller_full_name'),
			'value' => $data['settings']['slr_full_name']
			),
			array(
				array('rule' => 'max_len,100')
			)
		);
		if(!$is_valid) $json['errors']['slr_full_name'] = $validator->get_errors();

		$is_valid = $validator->validate(array(
			'name' => $this->language->get('ms_seller_address1'),
			'value' => $data['settings']['slr_address_line1']
			),
			array(
				array('rule' => 'max_len,100')
			)
		);
		if(!$is_valid) $json['errors']['slr_address_line1'] = $validator->get_errors();

		$is_valid = $validator->validate(array(
			'name' => $this->language->get('ms_seller_address2'),
			'value' => $data['settings']['slr_address_line2']
			),
			array(
				array('rule' => 'max_len,100')
			)
		);
		if(!$is_valid) $json['errors']['slr_address_line2'] = $validator->get_errors();

		$is_valid = $validator->validate(array(
			'name' => $this->language->get('ms_seller_state'),
			'value' => $data['settings']['slr_state']
			),
			array(
				array('rule' => 'max_len,50')
			)
		);
		if(!$is_valid) $json['errors']['slr_state'] = $validator->get_errors();

		$is_valid = $validator->validate(array(
			'name' => $this->language->get('ms_seller_website'),
			'value' => $data['settings']['slr_website']
			),
			array(
				array('rule' => 'valid_url'),
				array('rule' => 'max_len,128')
			)
		);
		if(!$is_valid) $json['errors']['slr_website'] = $validator->get_errors();

		$is_valid = $validator->validate(array(
			'name' => $this->language->get('ms_seller_company'),
			'value' => $data['settings']['slr_company']
			),
			array(
				array('rule' => 'max_len,50')
			)
		);
		if(!$is_valid) $json['errors']['slr_company'] = $validator->get_errors();

		$is_valid = $validator->validate(array(
			'name' => $this->language->get('ms_seller_phone'),
			'value' => $data['settings']['slr_phone']
			),
			array(
				array('rule' => 'max_len,25')
			)
		);
		if(!$is_valid) $json['errors']['slr_phone'] = $validator->get_errors();

		// @todo regex validation \bUA-\d{4,10}(-\d{1,4})?\b
		if ($this->config->get('mxtconf_ga_seller_enable') == 1 && !empty($seller['settings']['slr_ga_tracking_id'])) {
			$is_valid = $validator->validate(array(
				'name' => $this->language->get('mxt_google_analytics_code'),
				'value' => $data['settings']['slr_ga_tracking_id']
			),
				array(
					array('rule' => 'max_len,15')
				)
			);
			if (!$is_valid) $json['errors']['slr_ga_tracking_id'] = $validator->get_errors();
		}

		if (!isset($json['errors'])) {
			$this->MsLoader->MsSetting->createSellerSetting($data);
			$this->session->data['success'] = $this->language->get('ms_success_settings_saved');
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxUploadSellerLogo() {
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

	public function jxSaveSellerShipping() {
		$json = array();
		$data = $this->request->post;

		if(!isset($data['seller_shipping']))
			$json['errors'][] = $this->language->get('ms_account_setting_ssm_error_data');

		// unset sample row
		unset($data['seller_shipping']['methods'][0]);

		if(empty($data['seller_shipping']['methods']))
			$json['errors'][] = $this->language->get('ms_account_setting_ssm_error_methods');

		if(!isset($json['errors'])) {
			foreach ($data['seller_shipping']['methods'] as $key => $ss) {
				if(!isset($ss['to_geo_zone_id']) || $ss['to_geo_zone_id'] == '')
					$json['errors']['seller_shipping[methods][' . $key . '][to_geo_zone_id]'] = $this->language->get('ms_account_setting_ssm_error_location');

				if(!isset($ss['delivery_time_id']) || $ss['delivery_time_id'] == '')
					$json['errors']['seller_shipping[methods][' . $key . '][delivery_time_id]'] = $this->language->get('ms_account_setting_ssm_error_delivery_time');

				if(!isset($ss['shipping_method_id']) || $ss['shipping_method_id'] <= 0 || $ss['shipping_method_name'] == '')
					$json['errors']['seller_shipping[methods][' . $key . '][shipping_method_id]'] = $this->language->get('ms_account_setting_ssm_error_method');

				if(!isset($ss['weight_from']) || $ss['weight_from'] == '')
					$json['errors']['seller_shipping[methods][' . $key . '][weight_from]'] = $this->language->get('ms_account_setting_ssm_error_weight');

				if(!isset($ss['cost_fixed']) || $ss['cost_fixed'] == '')
					$json['errors']['seller_shipping[methods][' . $key . '][cost_fixed]'] = $this->language->get('ms_account_setting_ssm_error_cost');
			}

			if(!isset($json['errors'])) {
				$this->MsLoader->MsShippingMethod->saveSellerShipping($this->customer->getId(), $data['seller_shipping']);
				$json['success'] = $this->language->get('ms_account_setting_ssm_success');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxAutocompleteShippingLocation() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			if(isset($this->request->get['referrer']) && $this->request->get['referrer'] == 'to_geo_zone') {
				$geo_zones = $this->MsLoader->MsShippingMethod->getShippingGeoZones(array(
					'name' => $this->request->get['filter_name']
				));

				foreach ($geo_zones as $geo_zone) {
					$json[] = array(
						'location_id'	=> $geo_zone['geo_zone_id'],
						'name'			=> strip_tags(html_entity_decode($geo_zone['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			} else if($this->request->get['referrer'] == 'country_from') {
				$countries = $this->MsLoader->MsShippingMethod->getShippingCountries(array(
					'name' => $this->request->get['filter_name']
				));

				foreach ($countries as $country) {
					$json[] = array(
						'location_id'	=> $country['country_id'],
						'name'			=> strip_tags(html_entity_decode($country['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		if(isset($this->request->get['referrer']) && $this->request->get['referrer'] == 'to_geo_zone') {
			array_unshift($json, array(
				'location_id'	=> 0,
				'name'			=> strip_tags(html_entity_decode($this->language->get('ms_account_product_shipping_elsewhere'), ENT_QUOTES, 'UTF-8')),
			));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxAutocompleteShippingMethod() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('tool/image');

			$companies = $this->MsLoader->MsShippingMethod->getShippingCompanies(
				array(
					'name' => $this->request->get['filter_name'],
					'language_id' => $this->config->get('config_language_id')
				)
			);

			foreach ($companies as $company) {
				$json[] = array(
					'method_id'    => $company['shipping_method_id'],
					'name'         => strip_tags(html_entity_decode($company['name'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function _getPaymentGateways() {
		$this->load->model('extension/extension');
		$this->load->model('setting/setting');

		$payment_gateways = array();
		$extensions = $this->model_extension_extension->getExtensions('ms_payment');

		foreach ($extensions as $extension) {
			$extension_name = str_replace('ms_pg_', '', $extension['code']);

			$this->load->language('multimerch/payment/' . $extension_name);

			$settings = $this->model_setting_setting->getSetting($extension['code']);

			foreach ($settings as $name => $value) {
				if((strpos($name, 'payout_enabled') && $value) || (strpos($name, 'fee_enabled') && $value)) {
					$payment_gateways[] = array(
						'code' => $extension['code'],
						'text_title' => $this->language->get('text_title'),
						'view' => $this->load->controller('multimerch/payment/' . $extension_name)
					);
					break;
				}
			}
		}

		// Enable `PayPal` settings if `PayPal Adaptive` extension is enabled
		$pg_paypal = false;
		foreach ($payment_gateways as $payment_gateway) {
			if($payment_gateway['code'] == 'ms_pg_paypal') {
				$pg_paypal = true;
			}
		}

		if(!$pg_paypal) {
			$oc_payment_extensions = $this->model_extension_extension->getExtensions('payment');
			foreach ($oc_payment_extensions as $oc_payment_extension) {
				if($oc_payment_extension['code'] == 'ms_pp_adaptive') {
					$this->load->language('multimerch/payment/paypal');

					$payment_gateways[] = array(
						'code' => 'ms_pg_paypal',
						'text_title' => $this->language->get('text_title'),
						'view' => $this->load->controller('multimerch/payment/paypal')
					);
					break;
				}
			}
		}

		return $payment_gateways;
	}

	private function _getSellerShipping($seller_id) {
		$seller_shipping = $this->MsLoader->MsShippingMethod->getSellerShipping($seller_id);

		if(!empty($seller_shipping) && isset($seller_shipping['methods'])) {
			foreach ($seller_shipping['methods'] as &$ssm) {
				$ssm['cost_fixed'] = $this->MsLoader->MsHelper->trueCurrencyFormat($ssm['cost_fixed']);
				$ssm['cost_pwu'] = $this->MsLoader->MsHelper->trueCurrencyFormat($ssm['cost_pwu']);
				$ssm['weight_from'] = $this->MsLoader->MsHelper->trueCurrencyFormat($ssm['weight_from']);
				$ssm['weight_to'] = $this->MsLoader->MsHelper->trueCurrencyFormat($ssm['weight_to']);
			}
		}

		return $seller_shipping;
	}
}

?>
