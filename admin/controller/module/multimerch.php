<?php

if (!class_exists('ControllerMultimerchBase')) {
    die('
<h3>MultiMerch Installation Error - ControllerMultimerchBase class not found</h3>
<pre>This usually means vQmod is missing or broken. Please make sure that:

1. You have installed MultiMerch Core
2. You have installed the latest version of vQmod available at <a href="http://vqmod.com/">http://vqmod.com/</a>
3. You have run vQmod installation script at <a target="_blank" href="'.HTTP_CATALOG.'vqmod/install/">'.HTTP_CATALOG.'vqmod/install/</a> successfully (see <a target="_blank" href="https://github.com/vqmod/vqmod/wiki/Installing-vQmod-on-OpenCart">Installing vQmod on OpenCart</a> for more information)
4. Your vqmod/ and vqmod/vqcache/ folders are server-writable. Contact your hosting provider for more information
5. You have copied all MultiMerch files and folders from the upload/ folder to your OpenCart root
</pre>
    ');
}

class ControllerModuleMultimerch extends ControllerMultimerchBase {
	private $_controllers = array(
		"multimerch/attribute",
		"multimerch/badge",
		"multimerch/base",
		"multimerch/category",
		"multimerch/conversation",
		"multimerch/dashboard",
		"multimerch/debug",
		"multimerch/option",
		"multimerch/payment",
		"multimerch/payment-gateway",
		"multimerch/payment-request",
		"multimerch/product",
		"multimerch/seller",
		"multimerch/seller-group",
		"multimerch/settings",
		"multimerch/shipping-method",
		"multimerch/social_link",
		"multimerch/transaction",

		"total/mm_shipping_total"
	);
	
	private $settings = array(
		"mxtconf_installed" => 1,

		/*questions */
		"msconf_allow_questions" => 1,
		
		/* badges */
		"msconf_badge_enabled" => 0,
		"msconf_badge_width" => 50,
		"msconf_badge_height" => 50,

		/* social links */
		"msconf_sl_status" => 0,
		"msconf_sl_icon_width" => 30,
		"msconf_sl_icon_height" => 30,

		/* messaging */
		"mmess_conf_enable" => 1,

		/* disqus comments */
		'mxtconf_disqus_enable' => 0,
		'mxtconf_disqus_shortname' => '',

		/* google analytics */
		'mxtconf_ga_seller_enable' => 0,

		/* core settings */
		"msconf_seller_validation" => MsSeller::MS_SELLER_VALIDATION_NONE,
		"msconf_product_validation" => MsProduct::MS_PRODUCT_VALIDATION_NONE,

		"msconf_nickname_rules" => 0, // 0 - alnum, 1 - latin extended, 2 - utf

		"msconf_change_group" => 0,

		"msconf_credit_order_statuses" => array(5),
		"msconf_debit_order_statuses" => array(8),
		"msconf_paypal_sandbox" => 1,
		"msconf_paypal_address" => "",

		"msconf_allow_withdrawal_requests" => 1,
		"msconf_allowed_image_types" => 'png,jpg,jpeg',
		"msconf_allowed_download_types" => 'zip,rar,pdf',
		"msconf_minimum_product_price" => 0,
		"msconf_maximum_product_price" => 0,

		"msconf_allow_free_products" => 1,
		"msconf_allow_digital_products" => 0,

		"msconf_allow_seller_categories" => 0,
		"msconf_product_category_type" => 1,
		"msconf_allow_multiple_categories" => 0,
		"msconf_restrict_categories" => array(),

		"msconf_allow_seller_attributes" => 0,
		"msconf_allow_seller_options" => 0,

		"msconf_product_included_fields" => array('price', 'images'),

		"msconf_images_limits" => array(0,0),
		"msconf_downloads_limits" => array(0,0),

		"msconf_enable_shipping" => 0, // 0 - no, 1 - yes, 2 - seller select

		"msconf_allow_relisting" => 0,

		"msconf_enable_non_alphanumeric_seo" => 0,
		"msconf_product_image_path" => 'sellers/',
		"msconf_temp_image_path" => 'tmp/',
		"msconf_temp_download_path" => 'tmp/',
		"msconf_seller_terms_page" => "",
		"msconf_default_seller_group_id" => 1,

		"msconf_rte_whitelist" => "",

		"msconf_reviews_enable" => 0,

		/* Shipping */
		"msconf_shipping_type" => 0, // 0 - disable, 1 - default store shipping, 2 - MM Vendor shipping
		"msconf_vendor_shipping_type" => 3, // 1 - Combined, 2 - Per-Product, 3 - Both
		"mm_shipping_total_status" => 1,
		"mm_shipping_total_sort_order" => 1,

		/* Fee settings */
		"msconf_fee_priority" => 2,

		"msconf_seller_avatar_seller_profile_image_width" => 100,
		"msconf_seller_avatar_seller_profile_image_height" => 100,
		"msconf_seller_avatar_seller_list_image_width" => 228,
		"msconf_seller_avatar_seller_list_image_height" => 228,
		"msconf_seller_avatar_product_page_image_width" => 100,
		"msconf_seller_avatar_product_page_image_height" => 100,
		"msconf_seller_avatar_dashboard_image_width" => 100,
		"msconf_seller_avatar_dashboard_image_height" => 100,
		"msconf_preview_seller_avatar_image_width" => 100,
		"msconf_preview_seller_avatar_image_height" => 100,
		"msconf_preview_product_image_width" => 100,
		"msconf_preview_product_image_height" => 100,
		"msconf_product_seller_profile_image_width" => 100,
		"msconf_product_seller_profile_image_height" => 100,
		"msconf_product_seller_products_image_width" => 100,
		"msconf_product_seller_products_image_height" => 100,
		"msconf_product_seller_product_list_seller_area_image_width" => 40,
		"msconf_product_seller_product_list_seller_area_image_height" => 40,
		"msconf_product_seller_banner_width" => 750,
		"msconf_product_seller_banner_height" => 100,

		"msconf_min_uploaded_image_width" => 0,
		"msconf_min_uploaded_image_height" => 0,
		"msconf_max_uploaded_image_width" => 0,
		"msconf_max_uploaded_image_height" => 0,

		"msconf_change_seller_nickname" => 1,

		// hidden
		"msconf_sellers_slug" => "sellers",
		"msconf_enable_quantities" => 1, // 0 - no, 1 - yes, 2 - shipping dependent
		"msconf_enable_rte" => 1,
		"msconf_minimum_withdrawal_amount" => "50",
		"msconf_withdrawal_waiting_period" => 0,

		// deprecated
		"msconf_notification_email" => "",
		"msconf_allow_inactive_seller_products" => 0,
		"msconf_disable_product_after_quantity_depleted" => 0,
		"msconf_graphical_sellermenu" => 1,
		"msconf_enable_seller_banner" => 1,

		"msconf_allow_specials" => 1,
		"msconf_allow_discounts" => 1,

		"msconf_attribute_display" => 1, // 0 - MM, 1 - OC, 2 - both

		"msconf_additional_category_restrictions" => 0, // 0 - none, 1 - topmost, 2 - all parents
		"msconf_provide_buyerinfo" => 0, // 0 - no, 1 - yes, 2 - shipping dependent

		"msconf_allow_partial_withdrawal" => 1,

		"msconf_enable_seo_urls_seller" => 0,
		"msconf_enable_seo_urls_product" => 0,
		"msconf_enable_update_seo_urls" => 0,
		"msconf_hide_customer_email" => 1,

		/* Messaging */
		"msconf_enable_private_messaging" => 2, // 0 - no, 2 - yes (email only)
		"msconf_msg_allowed_file_types" => 'png,jpg,jpeg,zip,rar,pdf'
	);

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
		$this->data = array_merge(
			$this->data,
			$this->load->language('multiseller/multiseller'),
			$this->load->language('multimerch/multimerch')
		);
		$this->load->model("multimerch/install");
		$this->load->model("multimerch/upgrade");
		$this->load->model('setting/setting');
		$this->load->model('extension/extension');
		$this->data['token'] = $this->session->data['token'];
	}

	private function _editSettings($prefix = '') {
		$set = $this->model_setting_setting->getSetting($prefix);
		$installed_extensions = $this->model_extension_extension->getInstalled('module');

		$extensions_to_be_installed = array();
		foreach ($this->settings as $name=>$value) {
			if (!array_key_exists($name,$set))
				$set[$name] = $value;

			if ((strpos($name,'_module') !== FALSE) && (!in_array(str_replace('_module','',$name),$installed_extensions))) {
				$extensions_to_be_installed[] = str_replace('_module','',$name);
			}
		}

		foreach($set as $s=>$v) {
			if ((strpos($s,'_module') !== FALSE)) {
				if (!isset($this->request->post[$s])) {
					$set[$s] = '';
				} else {
					unset($this->request->post[$s][0]);
					$set[$s] = $this->request->post[$s];
				}
				continue;
			}

			if (isset($this->request->post[$s])) {
				$set[$s] = $this->request->post[$s];
				$this->data[$s] = $this->request->post[$s];
			} elseif ($this->config->get($s)) {
				$this->data[$s] = $this->config->get($s);
			} else {
				if (isset($this->settings[$s]))
					$this->data[$s] = $this->settings[$s];
			}
		}

		$this->model_setting_setting->editSetting($prefix, $set);

		foreach ($extensions_to_be_installed as $ext) {
			$this->model_extension_extension->install('module',$ext);
		}
	}

	public function install() {
		$this->validate(__FUNCTION__);

		/** @see \ModelMultimerchInstall::createSchema */
		$this->model_multimerch_install->createSchema();

		/** @see \ModelMultimerchInstall::createData */
		$this->model_multimerch_install->createData();
		$this->model_setting_setting->editSetting('mxtconf', $this->settings);
		$this->model_setting_setting->editSetting('msconf', $this->settings);
		$this->model_setting_setting->editSetting('mmess_conf', $this->settings);
		$this->model_setting_setting->editSetting('mm_shipping_total', $this->settings);

		$this->load->model('user/user_group');

		foreach ($this->_controllers as $c) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $c);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $c);
		}

		$dirs = array(
			DIR_IMAGE . $this->settings['msconf_product_image_path'],
			DIR_IMAGE . $this->settings['msconf_temp_image_path'],
			DIR_DOWNLOAD . $this->settings['msconf_temp_download_path']
		);

		$this->session->data['success'] = $this->language->get('ms_success_installed');
		$this->session->data['error'] = "";

		foreach ($dirs as $dir) {
			if (!file_exists($dir)) {
				if (!mkdir($dir, 0755)) {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory'), $dir);
				}
			} else {
				if (!is_writable($dir)) {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory_notwritable'), $dir);
				} else {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory_exists'), $dir);
				}
			}
		}
	}

	public function uninstall() {
		/** @see \ModelMultimerchInstall::deleteSchema */
		$this->model_multimerch_install->deleteSchema();

		/** @see \ModelMultimerchInstall::deleteData */
		$this->model_multimerch_install->deleteData();

		$this->load->model('user/user_group');

		foreach ($this->_controllers as $c) {
			$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', $c);
			$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', $c);
		}

		$this->model_setting_setting->editSetting("mxtconf", array("mxtconf_installed" => 0));
	}	

	public function saveSettings() {
		// @todo setting validation

		if (!isset($this->request->post['msconf_credit_order_statuses']))
			$this->request->post['msconf_credit_order_statuses'] = array(-1);

		if (!isset($this->request->post['msconf_debit_order_statuses']))
			$this->request->post['msconf_debit_order_statuses'] = array(-1);

		if (!isset($this->request->post['msconf_product_options']))
			$this->request->post['msconf_product_options'] = array();

		if (!isset($this->request->post['msconf_restrict_categories']))
			$this->request->post['msconf_restrict_categories'] = array();

        if (!isset($this->request->post['msconf_product_included_fields']))
			$this->request->post['msconf_product_included_fields'] = array();

		foreach (array('mxtconf', 'mmess_conf', 'msconf') as $prefix) {
			$this->_editSettings($prefix);
		}

		// Install MM Shipping Total module if Vendor shipping type is selected
		if(isset($this->request->post['msconf_shipping_type'])) {
			if ((int)$this->request->post['msconf_shipping_type'] == 2) {
				$this->model_setting_setting->editSetting('mm_shipping_total', array(
					'mm_shipping_total_status' => 1
				));
			} else {
				$this->model_setting_setting->editSetting('mm_shipping_total', array(
					'mm_shipping_total_status' => 0
				));
			}
		}

		$this->response->setOutput(json_encode(array()));
	}

	public function index() {
		$this->validate(__FUNCTION__);

		foreach($this->settings as $s=>$v) {
			$this->data[$s] = $this->config->get($s);
		}

		$this->document->addScript('view/javascript/multimerch/settings.js');

		$this->load->model("localisation/order_status");
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->load->model("catalog/option");
		$this->data['options'] = $this->model_catalog_option->getOptions();
		$this->load->model("localisation/language");
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		$this->data['currency_code'] = $this->config->get('config_currency');

		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}

		$this->load->model('catalog/information');
		$this->data['informations'] = $this->model_catalog_information->getInformations();
		$this->data['categories'] = $this->MsLoader->MsProduct->getOcCategories();
		$this->data['product_included_fieds'] = array(
			'price' => $this->language->get('ms_catalog_products_field_price'),
			'quantity' => $this->language->get('ms_catalog_products_field_quantity'),
			'category' => $this->language->get('ms_catalog_products_field_category'),
			'tags' => $this->language->get('ms_catalog_products_field_tags'),
			'attributes' => $this->language->get('ms_catalog_products_field_attributes'),
			'options' => $this->language->get('ms_catalog_products_field_options'),
			'special_prices' => $this->language->get('ms_catalog_products_field_special_prices'),
			'quantity_discounts' => $this->language->get('ms_catalog_products_field_quantity_discounts'),
			'images' => $this->language->get('ms_catalog_products_field_images'),
			'files' => $this->language->get('ms_catalog_products_field_files'),
			'model' => $this->language->get('ms_catalog_products_field_model'),
			'sku' => $this->language->get('ms_catalog_products_field_sku'),
			'upc' => $this->language->get('ms_catalog_products_field_upc'),
			'ean' => $this->language->get('ms_catalog_products_field_ean'),
			'jan' => $this->language->get('ms_catalog_products_field_jan'),
			'isbn' => $this->language->get('ms_catalog_products_field_isbn'),
			'mpn' => $this->language->get('ms_catalog_products_field_mpn'),
			'manufacturer' => $this->language->get('ms_catalog_products_field_manufacturer'),
			'dateAvailable' => $this->language->get('ms_catalog_products_field_date_available'),
			'taxClass' => $this->language->get('ms_catalog_products_field_tax_class'),
			'subtract' => $this->language->get('ms_catalog_products_field_subtract'),
			'stockStatus' => $this->language->get('ms_catalog_products_field_stock_status'),
			'metaDescription' => $this->language->get('ms_catalog_products_field_meta_description'),
			'metaKeywords' => $this->language->get('ms_catalog_products_field_meta_keyword'),
			'metaTitle' => $this->language->get('ms_catalog_products_field_meta_title'),
			'seoURL' => $this->language->get('ms_catalog_products_field_seo_url'),
			'filters' => $this->language->get('ms_catalog_products_filters'),
            'minOrderQty' => $this->language->get('ms_catalog_products_min_order_qty'),
            'relatedProducts' => $this->language->get('ms_catalog_products_related_products'),
			'dimensions' => $this->language->get('ms_catalog_products_dimensions'),
			'weight' => $this->language->get('ms_catalog_products_weight')
		);
		ksort($this->data['product_included_fieds']);

		$this->data['shipping_delivery_times'] = $this->MsLoader->MsShippingMethod->getShippingDeliveryTimes();

		$this->document->setTitle($this->language->get('ms_settings_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('module/multimerch', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_settings_breadcrumbs'),
				'href' => $this->url->link('module/multimerch', '', 'SSL'),
			)
		));

		if(isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		if(isset($this->session->data['error_warning'])) {
			$this->data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		}

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multimerch/settings.tpl', $this->data));
	}

	public function upgradeDb() {
		if ($this->MsLoader->MsHelper->isInstalled() && !$this->model_multimerch_upgrade->isDbLatest()) {
			$this->model_multimerch_upgrade->upgradeDb();
			$this->session->data['ms_db_latest'] = $this->language->get('ms_db_success');
		} else {
			$this->session->data['ms_db_latest'] = $this->language->get('ms_db_latest');
		}

		$this->response->redirect($this->url->link('module/multimerch', 'token=' . $this->session->data['token'], 'SSL'));
	}
}
?>