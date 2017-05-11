<?php

class ControllerSellerAccountProduct extends ControllerSellerAccount {
	public function getTableData() {
		$colMap = array(
			'product_name' => 'pd.name',
			'product_status' => '`mp.product_status`',
			'date_added' => '`p.date_added`',
			'list_until' => 'mp.list_until',
			'number_sold' => 'number_sold',
			'product_price' => 'p.price',
		);
		
		$sorts = array('product_name', 'product_price', 'date_added', 'list_until', 'product_status', 'product_earnings', 'number_sold');
		$filters = array_diff($sorts, array('product_status'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$seller_id = $this->customer->getId();
		$products = $this->MsLoader->MsProduct->getProducts(
			array(
				'seller_id' => $seller_id,
				'language_id' => $this->config->get('config_language_id'),
				'product_status' => array(MsProduct::STATUS_ACTIVE, MsProduct::STATUS_INACTIVE, MsProduct::STATUS_DISABLED, MsProduct::STATUS_UNPAID)
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			),
			array(
				'product_earnings' => 1,
				'product_sales' => 1
			)
		);
		
		$total = isset($products[0]) ? $products[0]['total_rows'] : 0;

		$columns = array();
		foreach ($products as $product) {
			$sale_data = $this->MsLoader->MsProduct->getSaleData($product['product_id']);

			// special price
			$specials = $this->MsLoader->MsProduct->getProductSpecials($product['product_id']);
			$special = false;
			foreach ($specials as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));
					break;
				}
			}

			// price
			$product['p.price'] = $this->currency->format($product['p.price'], $this->config->get('config_currency'));
			if ($special) {
				$price = "<span style='text-decoration: line-through;'>{$product['p.price']}></span><br/>";
				$price .= "<span class='special-price' style='color: #b00;'>$special</span>";
			} else {
				$price = $product['p.price'];
			}

			// Product Image
			if ($product['p.image'] && file_exists(DIR_IMAGE . $product['p.image'])) {
				$image = $this->MsLoader->MsFile->resizeImage($product['p.image'], $this->config->get('msconf_product_seller_product_list_seller_area_image_width'), $this->config->get('msconf_product_seller_product_list_seller_area_image_height'));
			} else {
				$image = $this->MsLoader->MsFile->resizeImage('no_image.png', $this->config->get('msconf_product_seller_product_list_seller_area_image_width'), $this->config->get('msconf_product_seller_product_list_seller_area_image_height'));
			}
			
			// actions
			$actions = "";
			if ($product['mp.product_status'] != MsProduct::STATUS_DISABLED) {
				if ($product['mp.product_status'] == MsProduct::STATUS_ACTIVE)
					$actions .= "<a class='icon-view' href='" . $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL') ."' title='" . $this->language->get('ms_viewinstore') . "'><i class='fa fa-search'></i></a>";
	
				if ($product['mp.product_approved']) {
					if ($product['mp.product_status'] == MsProduct::STATUS_INACTIVE)
						$actions .= "<a class='icon-publish' href='" . $this->url->link('seller/account-product/publish', 'product_id=' . $product['product_id'], 'SSL') ."' title='" . $this->language->get('ms_publish') . "'><i class='fa fa-plus'></i></a>";
		
					if ($product['mp.product_status'] == MsProduct::STATUS_ACTIVE)
						$actions .= "<a class='icon-unpublish' href='" . $this->url->link('seller/account-product/unpublish', 'product_id=' . $product['product_id'], 'SSL') ."' title='" . $this->language->get('ms_unpublish') . "'><i class='fa fa-minus'></i></a>";
				}
				
				$actions .= "<a class='icon-edit' href='" . $this->url->link('seller/account-product/update', 'product_id=' . $product['product_id'], 'SSL') ."' title='" . $this->language->get('ms_edit') . "'><i class='fa fa-pencil'></i></a>";
				$actions .= "<a class='icon-remove' href='" . $this->url->link('seller/account-product/delete', 'product_id=' . $product['product_id'], 'SSL') ."' title='" . $this->language->get('ms_delete') . "'><i class='fa fa-times'></i></a>";
			} else {
				if ($this->config->get('msconf_allow_relisting')) {
					$actions .= "<a href='" . $this->url->link('seller/account-product/update', 'product_id=' . $product['product_id'] . "&relist=1", 'SSL') ."' class='ms-button ms-button-relist' title='" . $this->language->get('ms_relist') . "'></a>";
				}
			}
			
			// product status
			$status = "";
			if ($product['mp.product_status'] == MsProduct::STATUS_ACTIVE) {
				$status = "<span class='active' style='color: #080;'>" . $this->language->get('ms_product_status_' . $product['mp.product_status']) . "</td></span>";
			} else {
				$status = "<span class='inactive' style='color: #b00;'>" . $this->language->get('ms_product_status_' . $product['mp.product_status']) . "</td></span>";
			}
			
			// List until
			if (isset($product['mp.list_until']) && $product['mp.list_until'] != NULL) {
				$list_until = date($this->language->get('date_format_short'), strtotime($product['mp.list_until']));
			} else {
				$list_until = $this->language->get('ms_not_defined');
			}
			$columns[] = array_merge(
				$product,
				array(
					'image' => "<img src='$image' />",
					'product_name' => $product['pd.name'],
					'product_price' => $price,
					'number_sold' => $product['number_sold'],
					'product_earnings' => $this->currency->format($sale_data['seller_total'], $this->config->get('config_currency')),
					'product_status' => $status,
					'date_added' => date($this->language->get('date_format_short'), strtotime($product['p.date_added'])),
					'list_until' => $list_until,
					'actions' => $actions,
					'quantity' => $product['p.quantity']
				)
			);
		}
		
		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function jxAutocompleteOptions(){
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('tool/image');

			$data = array(
				'seller_ids' => '0,' . $this->customer->getId(),
				'option_status' => MsOption::STATUS_ACTIVE
			);

			$filter_data = array(
				'filters' => array(
					'od.name' => $this->request->get['filter_name']
				)
			);

			$options = $this->MsLoader->MsOption->getOptions($data, $filter_data);

			foreach ($options as $option) {
				$option_value_data = array();

				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_values = $this->MsLoader->MsOption->getOptionValues($option['option_id']);

					foreach ($option_values as $option_value) {
						if (is_file(DIR_IMAGE . $option_value['image'])) {
							$image = $this->model_tool_image->resize($option_value['image'], 50, 50);
						} else {
							$image = $this->model_tool_image->resize('no_image.png', 50, 50);
						}

						$option_value_data[] = array(
							'option_value_id' => $option_value['option_value_id'],
							'name'            => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
							'image'           => $image
						);
					}

					$sort_order = array();

					foreach ($option_value_data as $key => $value) {
						$sort_order[$key] = $value['name'];
					}

					array_multisort($sort_order, SORT_ASC, $option_value_data);
				}

				$json[] = array(
					'option_id'    => $option['option_id'],
					'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
					'type'         => $option['type'],
					'option_value' => $option_value_data
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

	public function jxAutocompleteShippingCountry() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('tool/image');

			if(isset($this->request->get['referrer']) && $this->request->get['referrer'] == 'to_geo_zone') {
				$geo_zones = $this->MsLoader->MsShippingMethod->getShippingGeoZones(
					array(
						'name' => $this->request->get['filter_name']
					)
				);

				foreach ($geo_zones as $geo_zone) {
					$json[] = array(
						'country_id'	=> $geo_zone['geo_zone_id'],
						'name'			=> strip_tags(html_entity_decode($geo_zone['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
			} else if($this->request->get['referrer'] == 'country_from') {
				$countries = $this->MsLoader->MsShippingMethod->getShippingCountries(
					array(
						'name' => $this->request->get['filter_name']
					)
				);

				foreach ($countries as $country) {
					$json[] = array(
						'country_id'	=> $country['country_id'],
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
				'country_id'	=> 0,
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

	public function jxAutocompleteAttributes(){
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$data = array(
				'seller_ids' => '0,' . $this->customer->getId(),
				'attribute_status' => MsAttribute::STATUS_ACTIVE
			);

			$filter_data = array(
				'filters' => array(
					'ad.name' => $this->request->get['filter_name']
				)
			);

			$results = $this->MsLoader->MsAttribute->getAttributes($data, $filter_data);

			foreach ($results as $result) {
				$attribute_group_data = $this->MsLoader->MsAttribute->getAttributeGroups(array('attribute_group_id' => $result['attribute_group_id'], 'single' => 1));

				$json[] = array(
					'attribute_id'    => $result['attribute_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $attribute_group_data['name']
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

	public function jxUpdateFile() {
		$json = array();
		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}
		
		if (isset($this->request->post['file_id']) && isset($this->request->post['product_id'])) {
			$download_id = (int)substr($this->request->post['file_id'], strrpos($this->request->post['file_id'], '-')+1);
			$product_id = (int)$this->request->post['product_id'];
			$seller_id = $this->customer->getId();
			if  ($this->MsLoader->MsProduct->productOwnedBySeller($product_id,$seller_id) && $this->MsLoader->MsProduct->hasDownload($product_id,$download_id)) {
				$file = array_shift($_FILES);
				$errors = $this->MsLoader->MsFile->checkDownload($file);
				
				if ($errors) {
					$json['errors'] = array_merge($json['errors'], $errors);
				} else {
					$fileData = $this->MsLoader->MsFile->uploadDownload($file);
					$json['fileName'] = $fileData['fileName'];
					$json['fileMask'] = $fileData['fileMask'];
				}
			}
		}
			
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxUploadSellerAvatar() {
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
				$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'));
				$json['files'][] = array(
					'name' => $fileName,
					'thumb' => $thumbUrl
				);
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxUploadImages() {
		$json = array();
		$file = array();
		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}

		// allow a maximum of N images
		$msconf_images_limits = $this->config->get('msconf_images_limits');
		foreach ($_FILES as $file) {
			if ($msconf_images_limits[1] > 0 && $this->request->post['imageCount'] > $msconf_images_limits[1]) {
				$json['errors'][] = sprintf($this->language->get('ms_error_product_image_maximum'),$msconf_images_limits[1]);
				$json['cancel'] = 1;
				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$errors = $this->MsLoader->MsFile->checkImage($file);
				
				if ($errors) {
					$json['errors'] = array_merge($json['errors'], $errors);
				} else {
					$fileName = $this->MsLoader->MsFile->uploadImage($file);

					$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'));
					$json['files'][] = array(
						'name' => $fileName,
						'thumb' => $thumbUrl
					);
				}
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxUploadDownloads() {
		$json = array();
		$file = array();
		
		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}

		// allow a maximum of N files
		$msconf_downloads_limits = $this->config->get('msconf_downloads_limits');
		foreach ($_FILES as $file) {
			if ($msconf_downloads_limits[1] > 0 && $this->request->post['fileCount'] > $msconf_downloads_limits[1]) {
				$json['errors'][] = sprintf($this->language->get('ms_error_product_download_maximum'),$msconf_downloads_limits[1]);
				$json['cancel'] = 1;
				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$errors = $this->MsLoader->MsFile->checkDownload($file);
				
				if ($errors) {
					$json['errors'] = array_merge($json['errors'], $errors);
				} else {
					$fileData = $this->MsLoader->MsFile->uploadDownload($file);

					$json['files'][] = array (
						'fileName' => $fileData['fileName'],
						'fileMask' => $fileData['fileMask'],
						'filePages' => isset($pages) ? $pages : ''
					);
				}
			}
		}
		
		return $this->response->setOutput(json_encode($json));
	}
	
	public function jxGetFee() {
		$data = $this->request->get;

		if (!isset($data['price']) && !is_numeric($data['price'])) 
			$data['price'] = 0;

		$rates = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->customer->getId()));
		echo $this->currency->format((float)$rates[MsCommission::RATE_LISTING]['flat'] + ((float)$rates[MsCommission::RATE_LISTING]['percent'] * $data['price'] / 100), $this->config->get('config_currency'));
	}

	public function jxGetCategoryFee() {
		$json = array();
		$data = $this->request->post;

		if (!isset($data['price']) && !is_numeric($data['price']))
			$data['price'] = 0;

		if(isset($data['category_id'])) {
			$commission_rates = $this->MsLoader->MsCategory->getOcCategoryCommission($data['category_id'], MsCommission::RATE_LISTING, array('price' => $data['price']));

			if($commission_rates && isset($commission_rates[MsCommission::RATE_LISTING])) {
				$calculated_fee = $this->currency->format($commission_rates[MsCommission::RATE_LISTING]['calculated_fee'], $this->config->get('config_currency'));

				switch ($commission_rates[MsCommission::RATE_LISTING]['payment_method']) {
					case MsPgPayment::METHOD_PG:
						$ms_commission_payment_type = $this->language->get('ms_account_product_listing_pg');
						break;

					case MsPgPayment::METHOD_BALANCE:
					default:
						$ms_commission_payment_type = $this->language->get('ms_account_product_listing_balance');
						break;
				}
			} else {
				$calculated_fee = $this->currency->format(0, $this->config->get('config_currency'));
				$ms_commission_payment_type = '';
			}

			$json = array(
				'rate' => sprintf($data['msg'], $calculated_fee),
				'type' => $ms_commission_payment_type
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function jxSubmitProduct() {
		/** @var \MultiMerch\Module\MultiMerch $MultiMerchModule */
		$MultiMerchModule = $this->MsLoader->load('\MultiMerch\Module\MultiMerch');
		$serviceLocator = $MultiMerchModule->getServiceLocator();
		/** @var \MultiMerch\Mail\Transport\MultiMerchMail $mailTransport */
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$this->load->model('localisation/language');
		//ob_start();
		$data = $this->request->post;
		$seller = $this->MsLoader->MsSeller->getSeller($this->customer->getId());

		if (isset($data['product_id']) && !empty($data['product_id'])) {
			if ($this->MsLoader->MsProduct->productOwnedBySeller($data['product_id'], $this->customer->getId())) {
				$product = $this->MsLoader->MsProduct->getProduct($data['product_id']);
				$data['images'] = $this->MsLoader->MsProduct->getProductImages($data['product_id']);
			} else {
				return;
			}
		}

		$json = array();

		$validator = $this->MsLoader->MsValidator;

		// Only check default language for errors
		$i = 0;
		$defaultLanguageId = $this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language'));

		$languages = $this->model_localisation_language->getLanguages();
		// validate primary language
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$primary = true;
			if($language_id != $defaultLanguageId)
				$primary = false;
			// product name
			if (!$validator->validate(array(
					'name' => $this->language->get('ms_account_product_name'),
					'value' => html_entity_decode($data['product_name'][$language_id])
				),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'max_len,100')
				)
			)) $json['errors']["product_name[$language_id]"] = $validator->get_errors();

			// @todo $description_length = $this->config->get('msconf_enable_rte') ? utf8_strlen(strip_tags(htmlspecialchars_decode($language['product_description'], ENT_COMPAT))) : utf8_strlen(htmlspecialchars_decode($language['product_description'], ENT_COMPAT));*
			// product description
			
			if (!$validator->validate(array(
					'name' => $this->language->get('ms_account_product_description'),
					// count product description length without tags (as video script tag)
					'value' => strip_tags(htmlspecialchars_decode($data['product_description'][$language_id]))
				),
			
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'max_len,10000')
				)
			)) $json['errors']["product_description[$language_id]"] = $validator->get_errors($language_id);
			if(!$validator->validate(array(
					'name' => $this->language->get('ms_account_product_attribute'),
					'value' => isset($data['product_attribute'][++$i]) ? $data['product_attribute'][$i]['product_attribute_description'][$language_id]['text'] : ''
				),
				array(
					array('rule' => 'max_len, 1000')
				)
			)) $json['errors']["product_attribute[$language_id]"] = $validator->get_errors();
			// strip disallowed tags in description
			if ($this->config->get('msconf_enable_rte')) {
				if ($this->config->get('msconf_rte_whitelist') != '') {
					$allowed_tags = explode(",", $this->config->get('msconf_rte_whitelist'));
					$allowed_tags_ready = "";
					foreach ($allowed_tags as $tag) {
						$allowed_tags_ready .= "<" . trim($tag) . ">";
					}
					$data['product_description'][$language_id] = htmlspecialchars(strip_tags(htmlspecialchars_decode($data['description'][$language_id], ENT_COMPAT), $allowed_tags_ready), ENT_COMPAT, 'UTF-8');
				}
			} else {
				$data['description'][$language_id] = htmlspecialchars(nl2br($data['description'][$language_id]), ENT_COMPAT, 'UTF-8');
			}

			// price
			if(in_array('price', $this->config->get('msconf_product_included_fields'))) {
				if(!$validator->validate(array(
					'name' => $this->language->get('ms_account_product_price'),
					'value' => $data['product_price']
				),
					array(
						array('rule' => 'required'),
						array('rule' => 'numeric')
					)
				)) $json['errors']["product_price"] = $validator->get_errors();
			}

			// tags
			if(isset($data['product_tags'])) {
				if (!$validator->validate(array(
					'name' => $this->language->get('ms_account_product_tags'),
					'value' => $data['product_tags'][$language_id]
				),
					array(
						array('rule' => 'max_len,100')
					)
				)) $json['errors']["product_tags[$language_id]"] = $validator->get_errors();
			}

			//copy fields data from main language
			if(!$primary) {
				if (empty($data['product_name'][$language_id])) $data['product_name'][$language_id] = $data['product_name'][$defaultLanguageId];
				if (empty($data['product_description'][$language_id])) $data['product_description'][$language_id] = $data['product_description'][$defaultLanguageId];
				if (isset($data['product_tags']) && empty($data['product_tags'][$language_id])) $data['product_tags'][$language_id] = $data['product_tags'][$defaultLanguageId];
				if(isset($data['product_attribute'])) {
					foreach($data['product_attribute'] as $key => $attribute) {
						if($attribute != reset($data['product_attribute'])){
							if(empty($attribute['product_attribute_description'][$language_id]['text']))
								$data['product_attribute'][$key]['product_attribute_description'][$language_id] = $attribute['product_attribute_description'][$defaultLanguageId];
						}
					}
				}
			}
		}
		// @todo copy to secondary languages if not empty

		if ((float)$data['product_price'] == 0) {
			if (!is_numeric($data['product_price'])) {
				$json['errors']['product_price'] = $this->language->get('ms_error_product_price_invalid');
			} else if ($this->config->get('msconf_allow_free_products') == 0) {
				$json['errors']['product_price'] = $this->language->get('ms_error_product_price_empty');
			}
		} else if ((float)$data['product_price'] < (float)$this->config->get('msconf_minimum_product_price')) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_low');
		} else if (($this->config->get('msconf_maximum_product_price') != 0) && ((float)$data['product_price'] > (float)$this->config->get('msconf_maximum_product_price'))) {
			$json['errors']['product_price'] = $this->language->get('ms_error_product_price_high');
		}

		// downloads
		$msconf_downloads_limits = $this->config->get('msconf_downloads_limits');
		if (!isset($data['product_downloads'])) {
			if ($msconf_downloads_limits[0] > 0) {
				$json['errors']['product_download'] = sprintf($this->language->get('ms_error_product_download_count'), $msconf_downloads_limits[0]);
			}
		} else {
			if ($msconf_downloads_limits[1] > 0 && count($data['product_downloads']) > $msconf_downloads_limits[1]) {
				$json['errors']['product_download'] = sprintf($this->language->get('ms_error_product_download_maximum'), $msconf_downloads_limits[1]);
			} else if ($msconf_downloads_limits[0] > 0 && count($data['product_downloads']) < $msconf_downloads_limits[0]) {
				$json['errors']['product_download'] = sprintf($this->language->get('ms_error_product_download_count'), $msconf_downloads_limits[0]);
			} else {
				foreach ($data['product_downloads'] as $key => $download) {
					if (!empty($download['filename'])) {
						if (!$this->MsLoader->MsFile->checkFileAgainstSession($download['filename'])) {
							$json['errors']['product_download'] = $this->language->get('ms_error_file_upload_error');
						}
					} else if (!empty($download['download_id']) && !empty($product['product_id'])) {
						if (!$this->MsLoader->MsProduct->hasDownload($product['product_id'], $download['download_id'])) {
							$json['errors']['product_download'] = $this->language->get('ms_error_file_upload_error');
						}
					} else {
						unset($data['product_downloads'][$key]);
					}
					//str_replace($this->MsLoader->MsSeller->getNickname() . '_', '', $download);
					//$download = substr_replace($download, '.' . $this->MsLoader->MsSeller->getNickname() . '_', strpos($download,'.'), strlen('.'));
				}
			}
		}

		// images
		$msconf_images_limits = $this->config->get('msconf_images_limits');
		if (!isset($data['product_images'])) {
			if ($msconf_images_limits[0] > 0) {
				$json['errors']['product_image'] = sprintf($this->language->get('ms_error_product_image_count'), $msconf_images_limits[0]);
			}
		} else {
			if ($msconf_images_limits[1] > 0 && count($data['product_images']) > $msconf_images_limits[1]) {
				$json['errors']['product_image'] = sprintf($this->language->get('ms_error_product_image_maximum'), $msconf_images_limits[1]);
			} else if ($msconf_images_limits[0] > 0 && count($data['product_images']) < $msconf_images_limits[0]) {
				$json['errors']['product_image'] = sprintf($this->language->get('ms_error_product_image_count'), $msconf_images_limits[0]);
			} else {
				foreach ($data['product_images'] as $image) {
//					if(!empty($image)){
						if (!$this->MsLoader->MsFile->checkFileAgainstSession($image)) {
							$json['errors']['product_image'] = $this->language->get('ms_error_file_upload_error');
						}
//					}
				}
				$data['product_thumbnail'] = array_shift($data['product_images']);
			}
		}

		// specials
		unset($data['product_specials'][0]); // Remove sample row
		if (isset($data['product_specials']) && is_array($data['product_specials'])) {
			foreach ($data['product_specials'] as $product_special) {
				if (!isset($product_special['priority']) || $product_special['priority'] == null || $product_special['priority'] == "") {
					$json['errors']['specials'] = $this->language->get('ms_error_invalid_special_price_priority');
				}
				if ((!$this->MsLoader->MsHelper->isUnsignedFloat($product_special['price'])) || ((float)$product_special['price'] < (float)0)) {
					$json['errors']['specials'] = $this->language->get('ms_error_invalid_special_price_price');
				}
				if (!isset($product_special['date_start']) || ($product_special['date_start'] == NULL) || (!isset($product_special['date_end']) || $product_special['date_end'] == NULL)) {
					$json['errors']['specials'] = $this->language->get('ms_error_invalid_special_price_dates');
				}
			}
		}

		// bulk discounts
		unset($data['product_discounts'][0]); // Remove sample row
		if (isset($data['product_discounts']) && is_array($data['product_discounts'])) {
			foreach ($data['product_discounts'] as $product_discount) {
				if (!isset($product_discount['priority']) || $product_discount['priority'] == null || $product_discount['priority'] == "") {
					$json['errors']['quantity_discounts'] = $this->language->get('ms_error_invalid_quantity_discount_priority');
				}
				if ((int)$product_discount['quantity'] < (int)2) {
					$json['errors']['quantity_discounts'] = $this->language->get('ms_error_invalid_quantity_discount_quantity');
				}
				if ((!$this->MsLoader->MsHelper->isUnsignedFloat($product_discount['price'])) || ((float)$product_discount['price'] < (float)0)) {
					$json['errors']['quantity_discounts'] = $this->language->get('ms_error_invalid_quantity_discount_price');
				}
				if (!isset($product_discount['date_start']) || ($product_discount['date_start'] == NULL) || (!isset($product_discount['date_end']) || $product_discount['date_end'] == NULL)) {
					$json['errors']['quantity_discounts'] = $this->language->get('ms_error_invalid_quantity_discount_dates');
				}
			}
		}

		// Store categories
		$product_oc_categories = array();
		foreach (array_keys($data) as $k) {
			if (preg_match('/^product_category/', $k, $matches)) {
				$product_oc_categories[] = $data[$k];
				unset($data[$k]);
			}
		}

		if (!empty($product_oc_categories)) {
			$data['product_category'] = array();
			$disabled = array();
			$categories = $this->MsLoader->MsProduct->getOcCategories();
			foreach ($categories as $k => $c) {
				if ($c['disabled']) $disabled[] = $c['category_id'];
			}

			foreach ($product_oc_categories as $category_row) {
				$diffed_category_row = array_diff($category_row, $disabled);
				array_push($data['product_category'], trim(end($diffed_category_row)));
			}

			if (!$this->config->get('msconf_allow_multiple_categories') && count($data['product_category']) > 1) {
				$data['product_category'] = array($data['product_category'][0]);
			}
		}

		// Seller categories
		$product_ms_categories = array();
		foreach (array_keys($data) as $k) {
			if (preg_match('/^product_ms_category/', $k, $matches)) {
				$product_ms_categories[] = $data[$k];
				unset($data[$k]);
			}
		}

		if (!empty($product_ms_categories)) {
			$data['product_ms_category'] = array();
			foreach ($product_ms_categories as $category_row) {
				array_push($data['product_ms_category'], trim(end($category_row)));
			}

			if (!$this->config->get('msconf_allow_multiple_categories') && count($data['product_ms_category']) > 1) {
				$data['product_ms_category'] = array($data['product_ms_category'][0]);
			}
		}

		// model
		if (in_array('model', $this->config->get('msconf_product_included_fields'))) {
			if (empty($data['product_model'])) {
				$json['errors']['product_model'] = $this->language->get('ms_error_product_model_empty');
			} else if (utf8_strlen($data['product_model']) < 4 || utf8_strlen($data['product_model']) > 64) {
				$json['errors']['product_model'] = sprintf($this->language->get('ms_error_product_model_length'), 4, 64);
			}
		}

		// options
		//unset($data['product_option'][0]); // Remove sample row		

		// shipping
		if ($this->config->get('msconf_shipping_type') == 1 || $this->config->get('msconf_shipping_type') == 2) {
			// Store Shipping / Vendor Shipping
			$data['product_enable_shipping'] = 1;
		} else if ($this->config->get('msconf_shipping_type') == 0) {
			// Shipping disabled
			$data['product_enable_shipping'] = 0;
		}

		// quantities
		$data['product_quantity'] = isset($data['product_quantity']) ? (int)$data['product_quantity'] : 0;

		// minimum order quantity
		$data['minimum'] = isset($data['minimum']) ? (int)$data['minimum'] : 1;

		if ($this->config->get('msconf_enable_seo_urls_product')) {
			$product_name = $data['product_name'][$this->config->get('config_language_id')];
			$src_string = (is_array($this->config->get('msconf_product_included_fields')) && in_array('seoURL', $this->config->get('msconf_product_included_fields')) && (isset($data['keyword']) && $data['keyword'])) ? $data['keyword'] : $product_name;

			//test utf8 string: öäüßйȝîûηыეமிᚉ⠛
			$data['keyword'] = $this->MsLoader->MsHelper->slugify($src_string);
		}
		
		// return if errors
		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		// post-validation

		// set product status
		switch ($seller['ms.product_validation']) {
			case MsProduct::MS_PRODUCT_VALIDATION_APPROVAL:
				$data['enabled'] = 0;
				$data['product_status'] = MsProduct::STATUS_INACTIVE;
				$data['product_approved'] = 0;

				if (!isset($data['product_id']) || empty($data['product_id'])) {
					$MailProductAwaitingModeration = $serviceLocator->get('MailProductAwaitingModeration', false)
						->setTo($this->registry->get('customer')->getEmail())
						->setData(array(
							'addressee' => $this->registry->get('customer')->getFirstname(),
							'product_name' => $data['product_name'][$defaultLanguageId],
						));
					$mails->add($MailProductAwaitingModeration);

                    $MailAdminNewProductAwaitingModeration = $serviceLocator->get('MailAdminNewProductAwaitingModeration', false)
                        ->setTo($MultiMerchModule->getNotificationEmail())
                        ->setData(array(
                            //'addressee' => $this->registry->get('customer')->getFirstname(),
                            'product_name' => $data['product_name'][$defaultLanguageId],
                            'message' => isset($data['product_message']) ? $data['product_message'] : '',
                        ));
                    $mails->add($MailAdminNewProductAwaitingModeration);
				} else {
					$MailProductAwaitingModeration = $serviceLocator->get('MailProductAwaitingModeration', false)
						->setTo($this->registry->get('customer')->getEmail())
						->setData(array(
							'addressee' => $this->registry->get('customer')->getFirstname(),
							'product_name' => $data['product_name'][$defaultLanguageId],
						));
					$mails->add($MailProductAwaitingModeration);

                    $MailAdminEditProductAwaitingModeration = $serviceLocator->get('MailAdminEditProductAwaitingModeration', false)
                        ->setTo($MultiMerchModule->getNotificationEmail())
                        ->setData(array(
                            //'addressee' => $this->registry->get('customer')->getFirstname(),
                            'product_name' => $data['product_name'][$defaultLanguageId],
                            'message' => isset($data['product_message']) ? $data['product_message'] : '',
                        ));
                    $mails->add($MailAdminEditProductAwaitingModeration);
				}
				break;

			case MsProduct::MS_PRODUCT_VALIDATION_NONE:
			default:
				$data['enabled'] = 1;
				$data['product_status'] = MsProduct::STATUS_ACTIVE;
				$data['product_approved'] = 1;

				if (!isset($data['product_id']) || empty($data['product_id'])) {
					$MailAdminProductCreated = $serviceLocator->get('MailAdminProductCreated', false)
						->setTo($MultiMerchModule->getNotificationEmail())
						->setData(array(
							//'addressee' => $this->registry->get('customer')->getFirstname(),
							'product_name' => $data['product_name'][$defaultLanguageId],
						));
					$mails->add($MailAdminProductCreated);
				} else {
					// product edited mail if needed
				}
				break;
		}

		if ($mails->count()) {
			$mailTransport->sendMails($mails);
		}

		// Get commissions
		if ($this->config->get('msconf_fee_priority') == 1 && isset($data['product_category'])) {
			// If category commission is in prior
			$product_category_ids = implode(',', $data['product_category']);
			$commissions = $this->MsLoader->MsCategory->getOcCategoryCommission($product_category_ids, MsCommission::RATE_LISTING, array('price' => $data['product_price']));
			$fee = isset($commissions[MsCommission::RATE_LISTING]['calculated_fee']) ? (float)$commissions[MsCommission::RATE_LISTING]['calculated_fee'] : 0;
		} else {
			// If vendor commission is in prior or no product categories are set
			$commissions = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->customer->getId()));
			$fee = (float)$commissions[MsCommission::RATE_LISTING]['flat'] + $commissions[MsCommission::RATE_LISTING]['percent'] * $data['product_price'] / 100;
		}

		// If product is digital delete all MM shipping information about it
		if((isset($data['product_is_digital']) && $data['product_is_digital'] == 1) && isset($data['product_shipping'])) {
			unset($data['product_shipping']);
		}

		// unset sample shipping location
		if (isset($data['product_shipping']['locations'][0])) unset($data['product_shipping']['locations'][0]);

		// finish
		if (isset($data['product_id']) && !empty($data['product_id'])) {
			$product_id = $this->MsLoader->MsProduct->editProduct($data);

			// Create fee requests
			if ($product['product_status'] == MsProduct::STATUS_UNPAID) {
				if ($fee > 0) {
					switch($commissions[MsCommission::RATE_LISTING]['payment_method']) {
						case MsPgPayment::METHOD_PG:
							// change status to unpaid
							$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_UNPAID);

							// check if request exists
							$request_exists = $this->MsLoader->MsPgRequest->getRequests(
								array(
									'seller_id' => $this->customer->getId(),
									'product_id' => $product_id,
									'request_type' => array(MsPgRequest::TYPE_LISTING),
									'request_status' => array(MsPgRequest::STATUS_UNPAID),
									'single' => 1
								)
							);

							if (empty($request_exists)) {
								$request_id = $this->MsLoader->MsPgRequest->createRequest(
									array(
										'seller_id' => $this->customer->getId(),
										'product_id' => $product_id,
										'request_type' => MsPgRequest::TYPE_LISTING,
										'request_status' => MsPgRequest::STATUS_UNPAID,
										'description' => sprintf($this->language->get('ms_pg_request_description_listing'), $this->url->link('seller/account-product/update', 'product_id=' . $product_id, 'SSL'), $data['product_name'][$defaultLanguageId]),
										'amount' => $fee,
										'currency_id' => $this->currency->getId($this->config->get('config_currency')),
										'currency_code' => $this->currency->getCode($this->config->get('config_currency')),
									)
								);
							} else {
								$request_id = $request_exists['request_id'];

								// edit payment
								$this->MsLoader->MsPgRequest->updateRequest(
									$request_id,
									array(
										'description' => sprintf($this->language->get('ms_pg_request_description_listing'), $this->url->link('seller/account-product/update', 'product_id=' . $product_id, 'SSL'), $data['product_name'][$defaultLanguageId]),
										'amount' => $fee,
										'date_created' => 1
									)
								);
							}

							break;

						case MsPgPayment::METHOD_BALANCE:
						default:
							// deduct from balance
							$this->MsLoader->MsBalance->addBalanceEntry($this->customer->getId(),
								array(
									'product_id' => $product_id,
									'balance_type' => MsBalance::MS_BALANCE_TYPE_LISTING,
									'amount' => -$fee,
									'description' => sprintf($this->language->get('ms_pg_request_description_listing'), $this->url->link('seller/account-product/update', 'product_id=' . $product_id, 'SSL'), $data['product_name'][$defaultLanguageId])
								)
							);

							break;
					}
				}
			}

			$this->session->data['success'] = $this->language->get('ms_success_product_updated');
		} else {
			$product_id = $this->MsLoader->MsProduct->saveProduct($data);

			// Create fee requests
			if ($fee > 0) {
				switch($commissions[MsCommission::RATE_LISTING]['payment_method']) {
					case MsPgPayment::METHOD_PG:
						// set product status to unpaid
						$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_UNPAID);

						$request_id = $this->MsLoader->MsPgRequest->createRequest(
							array(
								'seller_id' => $this->customer->getId(),
								'product_id' => $product_id,
								'request_type' => MsPgRequest::TYPE_LISTING,
								'request_status' => MsPgRequest::STATUS_UNPAID,
								'description' => sprintf($this->language->get('ms_pg_request_description_listing'), $this->url->link('seller/account-product/update', 'product_id=' . $product_id, 'SSL'), $data['product_name'][$defaultLanguageId]),
								'amount' => $fee,
								'currency_id' => $this->currency->getId($this->config->get('config_currency')),
								'currency_code' => $this->currency->getCode($this->config->get('config_currency'))
							)
						);

						break;

					case MsPgPayment::METHOD_BALANCE:
					default:
						// deduct from balance
						$this->MsLoader->MsBalance->addBalanceEntry($this->customer->getId(),
							array(
								'product_id' => $product_id,
								'balance_type' => MsBalance::MS_BALANCE_TYPE_LISTING,
								'amount' => -$fee,
								'description' => sprintf($this->language->get('ms_pg_request_description_listing'), $this->url->link('seller/account-product/update', 'product_id=' . $product_id, 'SSL'), $data['product_name'][$defaultLanguageId])
							)
						);

						break;
				}
			}

			$this->session->data['success'] = $this->language->get('ms_success_product_created');
		}

		$json['redirect'] = $this->url->link('seller/account-product', '', 'SSL');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxRenderOptionValues() {
		$this->data['option'] = $this->MsLoader->MsOption->getOptions(
			array(
				'option_id' => 	$this->request->get['option_id'],
				'single' => 1
			)
		);
		
		$this->data['values'] = $this->MsLoader->MsOption->getOptionValues($this->request->get['option_id']);
		$this->data['option_index'] = 0;
		
		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multiseller/account-product-form-options-values.tpl')) {
			$this->template = MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multiseller/account-product-form-options-values.tpl';
		} else {
			$this->template = 'default/template/multiseller/account-product-form-options-values.tpl';
		}
	
		$this->response->setOutput($this->load->view($this->template, $this->data));
	}
	
	public function jxRenderProductOptions() {
		$this->load->model('catalog/product');
		$options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
		
		$output = '';
		if ($options) {
			$this->data['option_index'] = 0;
			foreach ($options as $o) {
				$this->data['option'] = $o;
				$this->data['product_option_values'] = $o['product_option_value'];
				$this->data['values'] = $this->MsLoader->MsOption->getOptionValues($o['option_id']);
				
				if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multiseller/account-product-form-options-values.tpl')) {
					$this->template = MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multiseller/account-product-form-options-values.tpl';
				} else {
					$this->template = 'default/template/multiseller/account-product-form-options-values.tpl';
				}
				
				$output .= $this->load->view($this->template, $this->data);
				$this->data['option_index']++;
			}
		}
	
		$this->response->setOutput($output);
	}
	
	public function jxAutocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/manufacturer');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start' => 0,
				'limit' => 5
			);

			$results = $this->MsLoader->MsProduct->getManufacturers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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

    public function index() {
		// paypal listing payment confirmation
		if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
			$this->data['success'] = $this->language->get('ms_success_product_published');
		}

		$seller_id = $this->customer->getId();
		$seller_group = $this->MsLoader->MsSellerGroup->getSellerGroupBySellerId($seller_id);
		$seller_group_id = isset($seller_group['seller_group']) ? $seller_group['seller_group'] : NULL;

		$total_products = $this->MsLoader->MsProduct->getTotalProducts(array(
			'seller_id' => $seller_id,
			'product_status' => array(MsProduct::STATUS_ACTIVE, MsProduct::STATUS_INACTIVE, MsProduct::STATUS_DISABLED, MsProduct::STATUS_UNPAID)
		));

		$slr_gr_settings = $this->MsLoader->MsSetting->getSellerGroupSettings(array('seller_group_id' => $seller_group_id));
		if(isset($slr_gr_settings['slr_gr_product_number_limit']) && $slr_gr_settings['slr_gr_product_number_limit'] !== '' && $total_products + 1 > (int)$slr_gr_settings['slr_gr_product_number_limit']) {
			$this->data['product_number_limit_exceeded'] = $this->language->get('ms_error_slr_gr_product_number_limit_exceeded');
		}
		
		// Links
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		$this->data['link_create_product'] = $this->url->link('seller/account-product/create', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_products_heading'));		
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
				'text' => $this->language->get('ms_account_products_breadcrumbs'),
				'href' => $this->url->link('seller/account-product', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-product');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
	
	private function _initForm() {
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('localisation/currency');
		$this->load->model('localisation/language');
		$this->load->model('account/customer_group');

		$this->document->addScript('catalog/view/javascript/multimerch/Sortable.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.html5.js');
		$this->document->addScript('catalog/view/javascript/account-product-form.js');
		$this->document->addScript('catalog/view/javascript/multimerch/account-product-form-options.js');

		$this->MsLoader->MsHelper->addStyle('multimerch/flags');

		// rte
		if ($this->config->get('msconf_enable_rte')) {
			$this->document->addScript('catalog/view/javascript/multimerch/ckeditor/ckeditor.js');
		}

		$this->data['seller'] = $this->MsLoader->MsSeller->getSeller($this->customer->getId());
		$this->data['seller_group'] = $this->MsLoader->MsSellerGroup->getSellerGroup($this->data['seller']['ms.seller_group']);

		$this->data['customer_groups'] = $this->model_account_customer_group->getCustomerGroups();
		$this->data['hide_customer_groups'] = (count($this->data['customer_groups']) < 2) ? true : false;

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		if ($product_id) $product_status = $this->MsLoader->MsProduct->getStatus($product_id);

		if (!$product_id || $product_status == MsProduct::STATUS_UNPAID) {
			$this->data['seller']['commissions'] = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $this->customer->getId()));

			$this->data['enable_category_fee'] = 1;

			switch ($this->data['seller']['commissions'][MsCommission::RATE_LISTING]['payment_method']) {
				case MsPgPayment::METHOD_PG:
					$this->data['ms_commission_payment_type'] = $this->language->get('ms_account_product_listing_pg');
					break;

				case MsPgPayment::METHOD_BALANCE:
				default:
					$this->data['ms_commission_payment_type'] = $this->language->get('ms_account_product_listing_balance');
					break;
			}
		}
		$this->data['salt'] = $this->MsLoader->MsSeller->getSalt($this->customer->getId());
		$this->data['categories'] = $this->MsLoader->MsProduct->getOcCategories();
		$this->data['ms_categories'] = $this->MsLoader->MsCategory->getCategories(array('seller_ids' => $this->customer->getId(), 'category_status' => MsCategory::STATUS_ACTIVE));
		$this->data['date_available'] = date('Y-m-d', time());
		$this->data['tax_classes'] = $this->MsLoader->MsHelper->getTaxClasses();
		$this->data['stock_statuses'] = $this->MsLoader->MsHelper->getStockStatuses();

		// fix price delimiters
		$this->data['ms_account_product_price_note'] = sprintf($this->data['ms_account_product_price_note'], $this->currency->getSymbolLeft($this->config->get('config_currency')), $this->language->get('thousand_point'), $this->language->get('decimal_point'), $this->currency->getSymbolRight($this->config->get('config_currency')));

		// product_info
		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		// filters
		$filters = $this->MsLoader->MsFilter->getProductFilters($product_id);

		$this->data['product_filters'] = array();
		foreach ($filters as $filter_id) {
			$filter_info = $this->MsLoader->MsFilter->getFilter($filter_id);
			if ($filter_info) {
				$this->data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name' => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}
		// related products
		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {
			$products = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$this->data['product_relateds'] = array();

		foreach ($products as $product) {

			if (!empty($product_info)) {
				$this->data['product_relateds'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}

		// Dimensions
		if (isset($this->request->post['weight'])) {
			$this->data['weight'] = $this->request->post['weight'];
		} elseif (!empty($product_info)) {
			$this->data['weight'] = $product_info['weight'];
		} else {
			$this->data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
			$this->data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($product_info)) {
			$this->data['weight_class_id'] = $product_info['weight_class_id'];
		} else {
			$this->data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['length'])) {
			$this->data['length'] = $this->request->post['length'];
		} elseif (!empty($product_info)) {
			$this->data['length'] = $product_info['length'];
		} else {
			$this->data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$this->data['width'] = $this->request->post['width'];
		} elseif (!empty($product_info)) {
			$this->data['width'] = $product_info['width'];
		} else {
			$this->data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$this->data['height'] = $this->request->post['height'];
		} elseif (!empty($product_info)) {
			$this->data['height'] = $product_info['height'];
		} else {
			$this->data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
			$this->data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($product_info)) {
			$this->data['length_class_id'] = $product_info['length_class_id'];
		} else {
			$this->data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		// Attributes
		$product_attributes = $this->MsLoader->MsAttribute->ocGetProductAttributes($product_id);

		$this->data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->MsLoader->MsAttribute->ocGetAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$this->data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}

		$this->data['product_options'] = array();

		if ($product_id) {
			$options = $this->model_catalog_product->getProductOptions($product_id);

			if ($options) {
				$d = $this->data;
				$d['option_index'] = 0;
				foreach ($options as &$o) {
					$d['option'] = $o;
					$d['product_option_values'] = $o['product_option_value'];
					$d['values'] = $this->MsLoader->MsOption->getOptionValues($o['option_id']);

					if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multiseller/account-product-form-options-values.tpl')) {
						$this->template = MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multiseller/account-product-form-options-values.tpl';
					} else {
						$this->template = 'default/template/multiseller/account-product-form-options-values.tpl';
					}

					$o['val'] = $this->load->view($this->template, $d);
					$d['option_index']++;
				}
			}

			$this->data['product_options'] = $options;
		}

		$this->data['enable_quantities'] = 0;
		if ($this->config->get('msconf_enable_quantities') == 1) {
			$this->data['enable_quantities'] = 1;
		} else if ($this->config->get('msconf_enable_quantities') == 2) {
			// shipping-dependent
			if ($this->config->get('msconf_enable_shipping') == 1) {
				$this->data['enable_quantities'] = 1;
			}
		}

		// Shipping
		$this->data['shipping_delivery_times'] = $this->MsLoader->MsShippingMethod->getShippingDeliveryTimes(array('language_id' => $this->config->get('config_language_id')));

		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['msconf_allow_multiple_categories'] = $this->config->get('msconf_allow_multiple_categories');
		$this->data['msconf_images_limits'] = $this->config->get('msconf_images_limits');
		$this->data['msconf_downloads_limits'] = $this->config->get('msconf_downloads_limits');
		$this->data['msconf_enable_quantities'] = $this->config->get('msconf_enable_quantities');
        $this->data['ms_account_product_download_note'] = sprintf($this->language->get('ms_allowed_extensions'), $this->config->get('msconf_allowed_download_types'));
		$this->data['ms_account_product_image_note'] = sprintf($this->language->get('ms_allowed_extensions'), $this->config->get('msconf_allowed_image_types'));
		$this->data['back'] = $this->url->link('seller/account-product', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_products_heading'));
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
				'text' => $this->language->get('ms_account_products_breadcrumbs'),
				'href' => $this->url->link('seller/account-product', '', 'SSL'),
			)
		));
	}
	
	public function create() {
		$this->_initForm();
		$this->data['product_attributes'] = FALSE;
		$this->data['product'] = FALSE;
		$this->data['heading'] = $this->language->get('ms_account_newproduct_heading');
		$this->document->setTitle($this->language->get('ms_account_newproduct_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-product-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
	
	public function update() {
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$seller_id = $this->customer->getId();
		
		if ($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id)) {
    		$product = $this->MsLoader->MsProduct->getProduct($product_id);
		} else {
			return $this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
		$this->_initForm();

		// name, description, metas
		foreach ($product['languages'] as $id => $l) {
			$product['name'][$id] = $l['name'];
			$product['description'][$id] = ($this->config->get('msconf_enable_rte') ? htmlspecialchars_decode($l['description']) : strip_tags(htmlspecialchars_decode($l['description'])));

			$product['tags'][$id] = $l['tags'];
			$product['product_meta_keywords'][$id] = $l['meta_keyword'];
			$product['product_meta_description'][$id] = $l['meta_description'];
			$product['product_meta_title'][$id] = $l['meta_title'];
		}

		// Store categories
		$p_oc_cat_ids = explode(',', $this->MsLoader->MsProduct->getProductOcCategories($product_id));
		foreach ($p_oc_cat_ids as $cat_id) {
			$oc_cat_path = $this->MsLoader->MsCategory->getOcCategoryPath($cat_id);
			$this->categoryPathToArray($oc_cat_path, $product['oc_categories']);
		}

		// Seller categories
		$p_ms_cat_ids = explode(',', $this->MsLoader->MsProduct->getProductMsCategories($product_id));
		foreach ($p_ms_cat_ids as $cat_id) {
			$ms_cat_path = $this->MsLoader->MsCategory->getMsCategoryPath($cat_id);
			$this->categoryPathToArray($ms_cat_path, $product['ms_categories']);
		}

		// price @todo formatting
		$currencies = $this->model_localisation_currency->getCurrencies();
  		$decimal_place = $currencies[$this->config->get('config_currency')]['decimal_place'];
		$product['price'] = $this->MsLoader->MsHelper->trueCurrencyFormat($product['price']);

		// specials
		$product['specials'] = $this->MsLoader->MsProduct->getProductSpecials($product_id);
		foreach ($product['specials'] as &$special) {
			$special['price'] = $this->MsLoader->MsHelper->trueCurrencyFormat($special['price']);
		}

		// bulk discounts
		$product['discounts'] = $this->MsLoader->MsProduct->getProductDiscounts($product_id);
		foreach ($product['discounts'] as &$discount) {
			$discount['price'] = $this->MsLoader->MsHelper->trueCurrencyFormat($discount['price']);
		}

		// tax class
		$product['tax_class_id'] = isset($product['tax_class_id']) ? $product['tax_class_id'] : 0;

		// stock status
		$product['stock_status_id'] = isset($product['stock_status_id']) ? $product['stock_status_id'] : (int)$this->MsLoader->MsProduct->getDefaultStockStatus();

		// date available
		$product['date_available'] = isset($product['date_available']) ? date('Y-m-d', strtotime($product['date_available'])) : '';

		// thumbnail
		if (!empty($product['thumbnail'])) {
			$product['images'][] = array(
				'name' => $product['thumbnail'],
				'thumb' => $this->MsLoader->MsFile->resizeImage($product['thumbnail'], $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'))
			);
			
			if (!in_array($product['thumbnail'], $this->session->data['multiseller']['files'])) 	$this->session->data['multiseller']['files'][] = $product['thumbnail'];
		}

		// images
		$images = $this->MsLoader->MsProduct->getProductImages($product_id);
		foreach ($images as $image) {
			$product['images'][] = array(
				'name' => $image['image'],
				'thumb' => $this->MsLoader->MsFile->resizeImage($image['image'], $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'))
			);
			
			if (!in_array($image['image'], $this->session->data['multiseller']['files'])) $this->session->data['multiseller']['files'][] = $image['image'];
		}

		// downloads
		$downloads = $this->MsLoader->MsProduct->getProductDownloads($product_id);
		$product['downloads'] = array();
		foreach ($downloads as $download) {
			$product['downloads'][] = array(
				'name' => $download['mask'],
				'src' => $download['filename'],
				'href' => $this->url->link('seller/account-product/download', 'download_id=' . $download['download_id'] . '&product_id=' . $product_id, 'SSL'),
				'id' => $download['download_id'],
			);
			
			if (!in_array($download['filename'], $this->session->data['multiseller']['files']))
				$this->session->data['multiseller']['files'][] = $download['filename'];
		}

		// manufacturer
		if(isset($product['manufacturer_id'])){
			$product['manufacturer_id'] = (int)$product['manufacturer_id'];
			$this->load->model('catalog/manufacturer');
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product['manufacturer_id']);
			if ($manufacturer_info) {
				$product['manufacturer'] = $manufacturer_info['name'];
			} else {
				$product['manufacturer'] = '';
			}
		} else {
			$product['manufacturer_id'] = 0;
			$product['manufacturer'] = '';
		};

		// shipping
		$product['oc_shipping'] = $product['shipping'];
		if($this->config->get('msconf_shipping_type') == 2) {
			$shipping_data = $this->MsLoader->MsProduct->getProductShipping($product_id, array('language_id' => $this->config->get('config_language_id')));
			if(isset($shipping_data['locations']) && !empty($shipping_data['locations'])) {
				foreach ($shipping_data['locations'] as $key => $location) {
					$shipping_data['locations'][$key]['cost'] = round($location['cost'], (int)$decimal_place);
					$shipping_data['locations'][$key]['additional_cost'] = round($location['additional_cost'], (int)$decimal_place);
				}
			}
			$product['shipping'] = $shipping_data;
		}

		// assign main data
		$this->data['product'] = $product;

		// set page heading
		$this->data['heading'] = $this->language->get('ms_account_editproduct_heading');
		$this->document->setTitle($this->language->get('ms_account_editproduct_heading'));

		// render
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-product-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
	
	public function delete() {
		$product_id = (int)$this->request->get['product_id'];
		$seller_id = (int)$this->customer->getId();
		
		if ($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id)) {
			$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_DELETED);

			// delete unpaid listing fee requests for this product
			$payment_request = $this->MsLoader->MsPgRequest->getRequests(array(
				'seller_id' => $seller_id,
				'product_id' => $product_id,
				'request_type' => array(MsPgRequest::TYPE_LISTING),
				'request_status' => array(MsPgRequest::STATUS_UNPAID),
				'single' => 1
			));

			if(isset($payment_request['request_id'])) {
				$this->MsLoader->MsPgRequest->deleteRequest($payment_request['request_id']);
			}

			$this->session->data['success'] = $this->language->get('ms_success_product_deleted');			
		}
		
		$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
	}
	
	public function publish() {
		$product_id = (int)$this->request->get['product_id'];
		$seller_id = (int)$this->customer->getId();
		
		if ($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id)
			&& $this->MsLoader->MsProduct->getStatus($product_id) == MsProduct::STATUS_INACTIVE) {
			$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
			$this->session->data['success'] = $this->language->get('ms_success_product_published');
		}
		
		$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
	}	
	
	public function unpublish() {
		$product_id = (int)$this->request->get['product_id'];
		$seller_id = (int)$this->customer->getId();
		
		if ($this->MsLoader->MsProduct->productOwnedBySeller($product_id, $seller_id)
			&& $this->MsLoader->MsProduct->getStatus($product_id) == MsProduct::STATUS_ACTIVE) {
			$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_INACTIVE);
			$this->session->data['success'] = $this->language->get('ms_success_product_unpublished');
		}
		
		$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
	}	
	
	public function download() {
		if (!$this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (isset($this->request->get['download_id'])) {
			$download_id = $this->request->get['download_id'];
		} else {
			$download_id = 0;
		}
		
		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		if (!$this->MsLoader->MsProduct->hasDownload($product_id,$download_id) || !$this->MsLoader->MsProduct->productOwnedBySeller($product_id,$this->customer->getId()))
			$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
			
		$download_info = $this->MsLoader->MsProduct->getDownload($download_id);
		
		if ($download_info) {
			$file = DIR_DOWNLOAD . $download_info['filename'];
			$mask = basename($download_info['mask']);

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					
					readfile($file, 'rb');
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
	}	
	
	public function jxGetCategories() {
		$categories = array();
		$category_id = isset($this->request->get['category_id']) ? $this->request->get['category_id'] : 0;

		if(isset($this->request->get['type']) && $this->request->get['type'] == 'oc') {
			$this->load->model('catalog/category');
			$categories = $this->model_catalog_category->getCategories($category_id);
		}

		if(isset($this->request->get['type']) && $this->request->get['type'] == 'ms') {
			$categories = $this->MsLoader->MsCategory->getCategories(array('parent_id' => $category_id, 'category_status' => MsCategory::STATUS_ACTIVE));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($categories));
	}

	protected function categoryPathToArray($category_path, &$categories_array) {
		if($category_path) {
			$cs = explode(',', $category_path);
			$cs_array = array();
			for($i = 0; $i < count($cs); $i++) {
				if($i == 0) $cs_array[$i] = $cs[$i];

				// if there is categories hierarchy
				if(isset($cs[$i+1])) {
					$cs_array[$cs[$i]] = $cs[$i+1];
				}
			}

			$categories_array[] = $cs_array;
		}
	}
}
?>
