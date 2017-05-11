<?php

class ControllerSellerAccountProfile extends ControllerSellerAccount {
	public function jxUploadSellerAvatar() {
		$json = array();
		$file = array();

		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}
		
		foreach ($_FILES as $name=>$file) {
			$errors = $this->MsLoader->MsFile->checkImage($file);
			if ($errors) {
				$json['errors'] = array_merge($json['errors'], $errors);
			} else {
				if($name === 'ms-avatar') {
					$fileName = $this->MsLoader->MsFile->uploadImage($file);
					$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
					$json['files'][] = array(
						'name' => $fileName,
						'thumb' => $thumbUrl
					);	
				} else if($name === 'ms-banner') {
					$fileName = $this->MsLoader->MsFile->uploadImage($file);
					$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_product_seller_banner_width')/2, $this->config->get('msconf_product_seller_banner_height')/2);
					$json['files'][] = array(
						'name' => $fileName,
						'thumb' => $thumbUrl
					);
				}
			}
		}

		return $this->response->setOutput(json_encode($json));
	}

	public function jxSaveSellerInfo() {
		/** @var \MultiMerch\Module\MultiMerch $MultiMerchModule */
		$MultiMerchModule = $this->MsLoader->load('\MultiMerch\Module\MultiMerch');
		$serviceLocator = $MultiMerchModule->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$data = $this->request->post;

		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());

		if(!isset($data["seller"]['seller_group']) OR (isset($seller['ms.seller_status']) AND $seller['ms.seller_status'] != MsSeller::STATUS_INCOMPLETE) OR !$this->config->get('msconf_change_group')){
			$data["seller"]['seller_group'] = $seller['ms.seller_group'];
		}

		// social links
		$this->MsLoader->MsHelper->addStyle('multimerch_social_links');

		if ($this->config->get('msconf_sl_status')) {
			$this->data['social_channels'] = $this->MsLoader->MsSocialLink->getChannels();
			foreach ($this->data['social_channels'] as &$c) {
				$c['image'] = $this->MsLoader->MsFile->resizeImage($c['image'], 34, 34);
			}

			if ($seller) {
				$seller['social_links'] = $this->MsLoader->MsSocialLink->getSellerChannels($this->customer->getId());
			}
		}

		$json = array();
		$json['redirect'] = $this->url->link('seller/account-dashboard');

		if (!empty($seller) && (in_array($seller['ms.seller_status'], array(MsSeller::STATUS_DISABLED, MsSeller::STATUS_DELETED)))) {
			return $this->response->setOutput(json_encode($json));
		}

		if ($this->config->get('msconf_change_seller_nickname') || empty($seller)) {
			// seller doesn't exist yet
			if (empty($data['seller']['nickname'])) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_empty');
			} else if (utf8_strlen($data['seller']['nickname']) < 4 || utf8_strlen($data['seller']['nickname']) > 128 ) {
				$json['errors']['seller[nickname]'] = $this->language->get('ms_error_sellerinfo_nickname_length');
			} else if ( ($data['seller']['nickname'] != $seller['ms.nickname']) && ($this->MsLoader->MsSeller->nicknameTaken($data['seller']['nickname'])) ) {
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
		} else {
			$data['seller']['nickname'] = $seller['ms.nickname'];
		}

		if (empty($seller)) {
			if ($this->config->get('msconf_seller_terms_page')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

				if ($information_info && !isset($data['seller']['terms'])) {
	 				$json['errors']['seller[terms]'] = htmlspecialchars_decode(sprintf($this->language->get('ms_error_sellerinfo_terms'), $information_info['title']));
				}
			}
		}

		$validator = $this->MsLoader->MsValidator;
		$default = 1;
		$languages = $this->model_localisation_language->getLanguages();
		foreach ($languages as $language){
			$language_id = $language['language_id'];
			$primary = true;
			if($language_id != $default){
				$primary = false;
			}

			// seller description
			if (!$validator->validate(array(
				'name' => $this->language->get('ms_account_sellerinfo_description'),
				// count seller description length without tags (as video script tag)
				'value' => strip_tags(htmlspecialchars_decode($data['seller']['description'][$language_id]['description']))
			),
				array(
					array('rule' => 'max_len,5000')
				)
			)) $json['errors']["seller_description[$language_id]"] = $validator->get_errors($language_id);

			//copy fields data from main language
			if(!$primary) {
				if (empty($data['seller']['description'][$language_id]['description'])){
					$data['seller']['description'][$language_id]['description'] = $data['seller']['description'][$default]['description'];
				}
			}
			// @todo copy to secondary languages if not empty

		}

		if (isset($data['seller']['avatar_name']) && !empty($data['seller']['avatar_name'])) {
			if (!$this->MsLoader->MsFile->checkFileAgainstSession($data['seller']['avatar_name'])) {
				$json['errors']['seller[avatar]'] = $this->language->get('ms_error_file_upload_error');
			}
		}

		if ($this->config->get('msconf_enable_seller_banner')) {
			if (isset($data['seller']['banner_name']) && !empty($data['seller']['banner_name'])) {
				if ($this->config->get('msconf_banners_for_sellers') == 0 && !$this->MsLoader->MsFile->checkFileAgainstSession($data['seller']['banner_name'])) {
					$json['errors']['seller[banner]'] = $this->language->get('ms_error_file_upload_error');
				}
			}
		}

		// strip disallowed tags in description
		if ($this->config->get('msconf_enable_rte')) {
			if ($this->config->get('msconf_rte_whitelist') != '') {
				$allowed_tags = explode(",", $this->config->get('msconf_rte_whitelist'));
				$allowed_tags_ready = "";
				foreach($allowed_tags as $tag) {
					$allowed_tags_ready .= "<".trim($tag).">";
				}
				foreach ($languages as $language){
					$data['seller']['description'][$language['language_id']]['description'] = htmlspecialchars(strip_tags(htmlspecialchars_decode($data['seller']['description'][$language['language_id']]['description'], ENT_COMPAT), $allowed_tags_ready), ENT_COMPAT, 'UTF-8');
				}
			}
		} else {
			foreach ($languages as $language){
				$data['seller']['description'][$language['language_id']]['description'] = htmlspecialchars(nl2br($data['seller']['description']), ENT_COMPAT, 'UTF-8');
			}
		}

		// uncomment to enable RTE for message field
		/*
		if(isset($data['reviewer_message'])) {
			$data['seller']['reviewer_message'] = strip_tags(html_entity_decode($data['seller']['reviewer_message']), $allowed_tags_ready);
		}
		*/

		if ($this->config->get('msconf_sl_status')) {
			foreach($data['seller']['social_links'] as &$link) {
				if(!$this->MsLoader->MsHelper->isValidUrl($link)) {
					$link = '';
				}
			}
		}

		if (empty($json['errors'])) {
			unset($data['seller']['commission']);

			if ($this->config->get('msconf_change_seller_nickname') || empty($seller)) {
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
			}

			if (empty($seller) || (!empty($seller) && $seller['ms.seller_status'] == MsSeller::STATUS_INCOMPLETE)) {
				$data['seller']['approved'] = 0;
				// create new seller
				switch ($this->config->get('msconf_seller_validation')) {
					/*
					case MsSeller::MS_SELLER_VALIDATION_ACTIVATION:
						$data['seller_status'] = MsSeller::STATUS_TOBEACTIVATED;
						break;
					*/

					case MsSeller::MS_SELLER_VALIDATION_APPROVAL:
						$MailSellerAwaitingModeration = $serviceLocator->get('MailSellerAwaitingModeration', false)
							->setTo($this->registry->get('customer')->getEmail())
							->setData(array('addressee' => $this->registry->get('customer')->getFirstname()));
						$mails->add($MailSellerAwaitingModeration);

						$MailAdminSellerAwaitingModeration = $serviceLocator->get('MailAdminSellerAwaitingModeration', false)
							->setTo($MultiMerchModule->getNotificationEmail())
							->setData(array(
								//'addressee' => $this->registry->get('customer')->getFirstname(),
								'seller_name' => $data['seller']['nickname'],
								'customer_name' => $this->customer->getFirstname() . ' ' . $this->customer->getLastname(),
								'customer_email' => $this->MsLoader->MsSeller->getSellerEmail($this->customer->getId()),
							));
						$mails->add($MailAdminSellerAwaitingModeration);

						$data['seller']['status'] = MsSeller::STATUS_INACTIVE;
						if ($this->config->get('msconf_allow_inactive_seller_products')) {
							$json['redirect'] = $this->url->link('account/account');
						} else {
							$json['redirect'] = $this->url->link('seller/account-profile');
						}
						break;

					case MsSeller::MS_SELLER_VALIDATION_NONE:
					default:
						$MailSellerAccountCreated = $serviceLocator->get('MailSellerAccountCreated', false)
							->setTo($this->registry->get('customer')->getEmail())
							->setData(array('addressee' => $this->registry->get('customer')->getFirstname()));
						$mails->add($MailSellerAccountCreated);

						$MailAdminSellerAccountCreated = $serviceLocator->get('MailAdminSellerAccountCreated', false)
							->setTo($MultiMerchModule->getNotificationEmail())
							->setData(array(
								//'addressee' => $this->registry->get('customer')->getFirstname(),
								'seller_name' => $data['seller']['nickname'],
								'customer_name' => $this->customer->getFirstname() . ' ' . $this->customer->getLastname(),
								'customer_email' => $this->MsLoader->MsSeller->getSellerEmail($this->customer->getId()),
							));
						$mails->add($MailAdminSellerAccountCreated);

						$data['seller']['status'] = MsSeller::STATUS_ACTIVE;
						$data['seller']['approved'] = 1;
						break;
				}

				$data['seller']['seller_id'] = $this->customer->getId();
				$data['seller']['product_validation'] = $this->config->get('msconf_product_validation');

				if (!empty($seller) && $seller['ms.seller_status'] == MsSeller::STATUS_INCOMPLETE) {
					$this->MsLoader->MsSeller->editSeller($data['seller']);
					if ($this->config->get('msconf_sl_status')) $this->MsLoader->MsSocialLink->editSellerChannels($this->customer->getId(), $data['seller']);
				} else {
					$this->MsLoader->MsSeller->createSeller($data['seller']);
					 if ($this->config->get('msconf_sl_status')) $this->MsLoader->MsSocialLink->editSellerChannels($this->customer->getId(), $data['seller']);
				}

				$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $data['seller']['seller_group']));
				$fee = (float)$commissions[MsCommission::RATE_SIGNUP]['flat'];

				if ($fee > 0) {
					switch($commissions[MsCommission::RATE_SIGNUP]['payment_method']) {
						case MsPgPayment::METHOD_PG:
							// set seller status to unpaid
							$this->MsLoader->MsSeller->changeStatus($this->customer->getId(), MsSeller::STATUS_UNPAID);

							// unset seller profile creation emails
							if (isset($MailSellerAccountCreated)) {
								$mails->remove($MailSellerAccountCreated);
							}

							$request_id = $this->MsLoader->MsPgRequest->createRequest(
								array(
									'seller_id' => $this->customer->getId(),
									'request_type' => MsPgRequest::TYPE_SIGNUP,
									'request_status' => MsPgRequest::STATUS_UNPAID,
									'description' => sprintf($this->language->get('ms_pg_request_description_signup'), $this->config->get('config_name')),
									'amount' => $fee,
									'currency_id' => $this->currency->getId($this->config->get('config_currency')),
									'currency_code' => $this->currency->getCode($this->config->get('config_currency'))
								)
							);

							// assign payment variables
							$json['data']['amount'] = $this->currency->format($fee, $this->config->get('config_currency'), '', FALSE);
							$json['data']['custom'] = $request_id;

							$mailTransport->sendMails($mails);
							return $this->response->setOutput(json_encode($json));
							break;

						case MsPgPayment::METHOD_BALANCE:
						default:
							// deduct from balance
							$this->MsLoader->MsBalance->addBalanceEntry($this->customer->getId(),
								array(
									'balance_type' => MsBalance::MS_BALANCE_TYPE_SIGNUP,
									'amount' => -$fee,
									'description' => sprintf($this->language->get('ms_pg_request_description_signup'), $this->config->get('config_name'))
								)
							);

							$mailTransport->sendMails($mails);
							break;
					}
				} else {
					$mailTransport->sendMails($mails);
				}

				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			} else {
				// edit seller
				$data['seller']['seller_id'] = $seller['seller_id'];
				$this->MsLoader->MsSeller->editSeller($data['seller']);
				if ($this->config->get('msconf_sl_status')) $this->MsLoader->MsSocialLink->editSellerChannels($this->customer->getId(), $data['seller']);

				if ($seller['ms.seller_status'] == MsSeller::STATUS_UNPAID) {
					$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $this->config->get('msconf_default_seller_group_id')));
					$fee = (float)$commissions[MsCommission::RATE_SIGNUP]['flat'];

					if ($fee > 0) {
						switch($commissions[MsCommission::RATE_SIGNUP]['payment_method']) {
							case MsPgPayment::METHOD_PG:
								// set seller status to unpaid
								$this->MsLoader->MsSeller->changeStatus($this->customer->getId(), MsSeller::STATUS_UNPAID);

								$request_exists = $this->MsLoader->MsPgRequest->getRequests(
									array(
										'seller_id' => $this->customer->getId(),
										'request_type' => array(MsPgRequest::TYPE_SIGNUP),
										'request_status' => array(MsPgRequest::STATUS_UNPAID),
										'single' => 1
									)
								);

								if(empty($request_exists)) {
									$request_id = $this->MsLoader->MsPgRequest->createRequest(
										array(
											'seller_id' => $this->customer->getId(),
											'request_type' => MsPgRequest::TYPE_SIGNUP,
											'request_status' => MsPgRequest::STATUS_UNPAID,
											'description' => sprintf($this->language->get('ms_pg_request_description_signup'), $this->config->get('config_name')),
											'amount' => $fee,
											'currency_id' => $this->currency->getId($this->config->get('config_currency')),
											'currency_code' => $this->currency->getCode($this->config->get('config_currency'))
										)
									);
								} else {
									$request_id = $request_exists['request_id'];

									// edit payment
									$this->MsLoader->MsPgRequest->updateRequest($request_id, array(
										'description' => sprintf($this->language->get('ms_pg_request_description_signup'), $this->config->get('config_name')),
										'amount' => $fee,
										'date_created' => 1
									));
								}

								break;

							case MsPgPayment::METHOD_BALANCE:
							default:
								// deduct from balance
								$this->MsLoader->MsBalance->addBalanceEntry($this->customer->getId(),
									array(
										'balance_type' => MsBalance::MS_BALANCE_TYPE_SIGNUP,
										'amount' => -$fee,
										'description' => sprintf($this->language->get('ms_pg_request_description_signup'), $this->config->get('config_name'))
									)
								);

								break;
						}
					}
				}

				$this->session->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
			}

            /*------------------------------Remove seller cache-----------------------------------------*/
            $this->cache->delete("seller" . $data['seller']['seller_id']);
            $this->cache->delete("catalog_seller");
            $this->cache->delete("catalog_seller_total");
            $this->cache->delete("ms_carousel");
            $this->cache->delete("ms_newsellers");
            $this->cache->delete("ms_topsellers");
            /*----------------------------------------------------------------------------------------------------*/
		}

		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->document->addScript('catalog/view/javascript/plupload/plupload.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.html5.js');
		$this->document->addScript('catalog/view/javascript/account-seller-profile.js');

		// rte
		if($this->config->get('msconf_enable_rte')) {
			$this->document->addScript('catalog/view/javascript/multimerch/ckeditor/ckeditor.js');
		}

		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if ($this->config->get('msconf_sl_status')) {
			$this->data['social_channels'] = $this->MsLoader->MsSocialLink->getChannels();
			foreach ($this->data['social_channels'] as &$c) {
				$c['image'] = $this->MsLoader->MsFile->resizeImage($c['image'], 34, 34);
			}

			if ($seller) {
				$seller['social_links'] = $this->MsLoader->MsSocialLink->getSellerChannels($this->customer->getId());
			}
		}

		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($this->customer->getId());
		$this->data['statusclass'] = 'warning';

		if ($seller) {
			switch ($seller['ms.seller_status']) {
				case MsSeller::STATUS_UNPAID:
				case MsSeller::STATUS_INCOMPLETE:
					$this->data['statusclass'] = 'warning';
					break;
				case MsSeller::STATUS_ACTIVE:
					$this->data['statusclass'] = 'success';
					break;
				case MsSeller::STATUS_DISABLED:
				case MsSeller::STATUS_DELETED:
					$this->data['statusclass'] = 'danger';
					break;
			}

			$this->data['seller'] = $seller; unset($this->data['seller']['banner']);

			if (!empty($seller['ms.avatar'])) {
				$this->data['seller']['avatar']['name'] = $seller['ms.avatar'];
				$this->data['seller']['avatar']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_preview_seller_avatar_image_width'), $this->config->get('msconf_preview_seller_avatar_image_height'));
				$this->session->data['multiseller']['files'][] = $seller['ms.avatar'];
			} else {
				$this->data['seller']['avatar']['name'] = $this->data['seller']['avatar']['thumb'] = '';
			}

			if ($this->config->get('msconf_enable_seller_banner')) {
				if (!empty($seller['banner'])) {
					$this->data['seller']['banner']['name'] = $seller['banner'];
					$this->data['seller']['banner']['thumb'] = $this->MsLoader->MsFile->resizeImage($seller['banner'], $this->config->get('msconf_product_seller_banner_width')/2, $this->config->get('msconf_product_seller_banner_height')/2);
					$this->session->data['multiseller']['files'][] = $seller['banner'];
				} else {
					$this->data['seller']['banner']['name'] = $this->data['seller']['banner']['thumb'] = '';
				}
			}


			$this->data['statustext'] = '';

			if ($seller['ms.seller_status'] != MsSeller::STATUS_INCOMPLETE) {
				$this->data['statustext'] = $this->language->get('ms_account_status') . $this->language->get('ms_seller_status_' . $seller['ms.seller_status']);
			}

			if ($seller['ms.seller_status'] == MsSeller::STATUS_INACTIVE && !$seller['ms.seller_approved']) {
				$this->data['statustext'] .= $this->language->get('ms_account_status_tobeapproved');
			}

			if ($seller['ms.seller_status'] == MsSeller::STATUS_INCOMPLETE) {
				$this->data['statustext'] .= $this->language->get('ms_account_status_please_fill_in');
			}

			foreach ($this->data['languages'] as $language){
				if(!isset($this->data['seller']['descriptions'][$language['language_id']]['description'])){
					$this->data['seller']['descriptions'][$language['language_id']]['description'] = '';
				}
			}

			$this->data['ms_account_sellerinfo_terms_note'] = '';
		} else {
			$this->data['seller'] = FALSE;


			$this->data['statustext'] = $this->language->get('ms_account_status_please_fill_in');

			if ($this->config->get('msconf_seller_terms_page')) {
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('msconf_seller_terms_page'));

				if ($information_info) {
					$this->data['ms_account_sellerinfo_terms_note'] = sprintf($this->language->get('ms_account_sellerinfo_terms_note'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('msconf_seller_terms_page'), 'SSL'), $information_info['title'], $information_info['title']);
				} else {
					$this->data['ms_account_sellerinfo_terms_note'] = '';
				}
			} else {
				$this->data['ms_account_sellerinfo_terms_note'] = '';
			}
		}

		if (!$seller || $seller['ms.seller_status'] == MsSeller::STATUS_UNPAID || $seller['ms.seller_status'] == MsSeller::STATUS_INCOMPLETE) {
			$this->data['group_commissions'] = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $this->config->get('msconf_default_seller_group_id')));

			switch($this->data['group_commissions'][MsCommission::RATE_SIGNUP]['payment_method']) {
				case MsPgPayment::METHOD_PG:
					$this->data['ms_commission_payment_type'] = $this->language->get('ms_account_sellerinfo_fee_pg');
					break;

				case MsPgPayment::METHOD_BALANCE:
				default:
					$this->data['ms_commission_payment_type'] = $this->language->get('ms_account_sellerinfo_fee_balance');
					break;
			}
		}

		$this->data['seller_groups'] = $this->MsLoader->MsSellerGroup->getSellerGroups();

		if (isset($seller['ms.seller_group'])) {
			$this->data['seller_group_id'] = $seller['ms.seller_group'];
		}else{
			$this->data['seller_group_id'] = $this->config->get('config_customer_group_id');
		}

		foreach ($this->data['seller_groups'] as $key => $group){
			$this->data['seller_groups'][$key]['commissions'] = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $group['seller_group_id']));
		}

		$this->data['seller_validation'] = $this->config->get('msconf_seller_validation');
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_sellerinfo_heading'));

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
				'text' => $this->language->get('ms_account_sellerinfo_breadcrumbs'),
				'href' => $this->url->link('seller/account-profile', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-profile');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
}
?>
