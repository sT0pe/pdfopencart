<?php 
class ControllerAccountRegisterSeller extends Controller {
	public  $data = array();

	public function __construct($registry) {
		parent::__construct($registry);

		$this->language->load('account/register');
		$this->load->model('account/customer');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->document->addScript('catalog/view/javascript/multimerch/account-register-seller.js');
	}

	public function index() {
		if ($this->customer->isLogged()) {
	  		$this->response->redirect($this->url->link('seller/account-profile', '', 'SSL'));
    	}

		if ($this->config->get('msconf_seller_terms_page')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

			if ($information_info) {
				$this->data['seller_terms'] = sprintf($this->language->get('ms_account_sellerinfo_terms_note'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('msconf_seller_terms_page'), 'SSL'), $information_info['title'], $information_info['title']);
			}
		}

		$this->document->setTitle($this->language->get('ms_account_register_seller'));
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
			),
			array(
				'text' => $this->language->get('ms_account_register_seller'),
				'href' => $this->url->link('account/register-seller', '', 'SSL')
			)
		));
		$this->data['text_account_already'] = sprintf($this->language->get('text_account_already'),  $this->url->link('account/login', '', 'SSL'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account/register-seller');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxsavesellerinfo() {
		$data = $this->request->post;
		$json = array();

		if ((utf8_strlen($data['seller']['firstname']) < 1) || (utf8_strlen($data['seller']['firstname']) > 32)) {
      		$json['errors']['seller[firstname]'] = $this->language->get('error_firstname');
    	}

    	if ((utf8_strlen($data['seller']['lastname']) < 1) || (utf8_strlen($data['seller']['lastname']) > 32)) {
      		$json['errors']['seller[lastname]'] = $this->language->get('error_lastname');
    	}

    	if ((utf8_strlen($data['seller']['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['seller']['email'])) {
      		$json['errors']['seller[email]'] = $this->language->get('error_email');
    	} else {
			if ($this->model_account_customer->getTotalCustomersByEmail($data['seller']['email'])) {
				$json['errors']['seller[email]'] = $this->language->get('error_exists');
			}
		}

		if ((utf8_strlen($data['seller']['password']) < 4) || (utf8_strlen($data['seller']['password']) > 20)) {
			$json['errors']['seller[password]'] = $this->language->get('error_password');
		}

		if ($data['seller']['password_confirm'] != $data['seller']['password']) {
			$json['errors']['seller[password_confirm]'] = $this->language->get('error_confirm');
		}

		if (empty($data['seller']['nickname'])) {
			$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_empty');
		} else if (utf8_strlen($data['seller']['nickname']) < 4 || utf8_strlen($data['seller']['nickname']) > 128 ) {
			$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_length');
		} else if (($this->MsLoader->MsSeller->nicknameTaken($data['seller']['nickname'])) ) {
			$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_taken');
		} else {
			switch($this->config->get('msconf_nickname_rules')) {
				case 1:
					// extended latin
					if(!preg_match("/^[a-zA-Z0-9_\-\s\x{00C0}-\x{017F}]+$/u", $data['seller']['nickname'])) {
						$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_latin');
					}
					break;

				case 2:
					// utf8
					if(!preg_match("/((?:[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})./x", $data['seller']['nickname'])) {
						$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_utf8');
					}
					break;

				case 0:
				default:
					// alnum
					if(!preg_match("/^[a-zA-Z0-9_\-\s]+$/", $data['seller']['nickname'])) {
						$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_alphanumeric');
					}
					break;
			}
		}

		if ($this->config->get('msconf_seller_terms_page')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

			if ($information_info && !isset($data['seller']['terms'])) {
				$json['errors']['seller[terms]'] = htmlspecialchars_decode(sprintf($this->language->get('ms_error_sellerinfo_terms'), $information_info['title']));
			}
		}

		if (empty($json['errors'])) {
			// Create buyer account
			$data['seller']['telephone'] = '';
			$data['seller']['fax'] = '';
			$data['seller']['company'] = '';
			$data['seller']['address_1'] = '';
			$data['seller']['address_2'] = '';
			$data['seller']['city'] = '';
			$data['seller']['postcode'] = '';
			$data['seller']['country_id'] = 0;
			$data['seller']['zone_id'] = 0;
			$data['seller']['customer_group_id'] = $this->config->get('config_customer_group_id');
			$this->model_account_customer->addCustomer($data['seller']);

			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($data['seller']['email']);
			$this->customer->login($data['seller']['email'], $data['seller']['password']);
			unset($this->session->data['guest']);

			// Add to activity log
			$this->load->model('account/activity');
			$activity_data = array(
				'customer_id' => $this->customer->getId(),
				'name'        => $data['seller']['firstname'] . ' ' . $data['seller']['lastname']
			);
			$this->model_account_activity->addActivity('register', $activity_data);

			// Register seller
			$data['seller']['status'] = MsSeller::STATUS_INCOMPLETE;
			$data['seller']['approved'] = 0;

			// SEO urls generation for sellers
			if ($this->config->get('msconf_enable_seo_urls_seller')) {
				$latin_check = '/[^\x{0030}-\x{007f}]/u';
				$non_latin_chars = preg_match($latin_check, $data['seller']['nickname']);
				if ($this->config->get('msconf_enable_non_alphanumeric_seo') && $non_latin_chars) {
					$data['seller']['keyword'] = implode("-", str_replace("-", "", explode(" ", strtolower($data['seller']['nickname']))));
				}
				else {
					$data['seller']['keyword'] = trim(implode("-", str_replace("-", "", explode(" ", preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($data['seller']['nickname']))))), "-");
				}
			}

			$data['seller']['seller_id'] = $this->customer->getId();
			$this->MsLoader->MsSeller->createSeller($data['seller']);
			$json['redirect'] = $this->url->link('seller/account-profile');
		}

		$this->response->setOutput(json_encode($json));
	}
}
?>