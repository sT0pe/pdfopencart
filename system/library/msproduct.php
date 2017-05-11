<?php
class MsProduct extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_DELETED = 4;
	const STATUS_UNPAID = 5;
	
	const MS_PRODUCT_VALIDATION_NONE = 1;
	const MS_PRODUCT_VALIDATION_APPROVAL = 2;
	
	private $errors;
	
	
	private function _getDepth($a, $eid) {
		foreach ($a as $key => $val) {
			if ($val['category_id'] == $eid) {
				if ($val['parent_id'] == 0) {
					return 0;
				} else {
					return 1 + $this->_getDepth($a, $val['parent_id']);
				}
			}
		}
	}

	private function _getPath($category_id) {
		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $query->row['name'];
		} else {
			return $query->row['name'];
		}
	}

	public function getDefaultStockStatus() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX ."stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY stock_status_id ASC");
		return $query->row['stock_status_id'];

	}
	
	public function getOcCategories($parent_id = 0) {
		//$category_data = $this->cache->get('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id);
		$category_data = FALSE;
		
		if (!$category_data) {
			$category_data = array();
		
			$sql = "SELECT
					c.category_id,
					c.parent_id,
					cd.name,
					(SELECT COUNT(*) FROM `" . DB_PREFIX . "category` cc WHERE cc.parent_id = c.category_id) as children
			FROM `" . DB_PREFIX . "category` c
			LEFT JOIN `" . DB_PREFIX . "category_description` cd
				ON (c.category_id = cd.category_id)
			WHERE c.parent_id = " . (int)$parent_id . "
			AND c.status = 1
			AND cd.language_id = " . (int)$this->config->get('config_language_id') . "
			ORDER BY c.sort_order, cd.name ASC";
			
			$query = $this->db->query($sql);
		
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'category_id' => $result['category_id'],
					'parent_id' => $result['parent_id'],
					'name'        => $result['name'],
					'children'        => $result['children'],
					'disabled' => ((in_array($result['category_id'], is_array($this->config->get('msconf_restrict_categories')) ? $this->config->get('msconf_restrict_categories') : (array)$this->config->get('msconf_restrict_categories')) || ($this->config->get('msconf_additional_category_restrictions') == 1 && $result['parent_id'] == 0) || ($this->config->get('msconf_additional_category_restrictions') == 2 && $result['children'] > 0)) ? TRUE : FALSE)
				);
			
				//Recursive call of the function and merge of all the categories together
				$category_data = array_merge($category_data, $this->getOcCategories($result['category_id']));
			}
	
			//$this->cache->set('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $category_data);
		}
		
		// The first calls of the function (for the root categories), where indentation takes place
		if ($parent_id == 0) {
			$category_data_indented = array();
			foreach ($category_data as $category) {
				$category_data_indented[] = array(
					'category_id' => $category['category_id'],
					'name'        => str_repeat('&nbsp;&nbsp;&nbsp;',$this->_getDepth($category_data, $category['category_id'])) . $category['name'],
					'parent_id' => $category['parent_id'],
					'children' => $category['children'],
					'disabled' => $category['disabled'],
				);
			}
			return $category_data_indented;
		}
		
		return $category_data;
	}
	
	public function getSellerId($product_id) {
		$sql = "SELECT seller_id FROM " . DB_PREFIX . "ms_product
				WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);
		
		if (isset($res->row['seller_id']))
			return $res->row['seller_id'];
		else
			return 0;
	}
	
	public function isEnabled($product_id) {
		$sql = "SELECT	p.status as enabled,
				FROM `" . DB_PREFIX . "product` p
				WHERE p.product_id = " . (int)$product_id;

		$res = $this->db->query($sql);
		
		if (!$res->row['enabled'])
			return false;
		else
			return true;
	}

	public function isShippableByOC($product_id) {
		$sql = "SELECT	p.shipping as shippable
				FROM `" . DB_PREFIX . "product` p
				WHERE p.product_id = " . (int)$product_id;

		$res = $this->db->query($sql);

		if (!$res->row['shippable'])
			return false;
		else
			return true;
	}
	
	public function getProductImages($product_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC";
		$res = $this->db->query($sql);
		
		$images = array();
		foreach ($res->rows as $row) {
			$images[$row['product_image_id']] = $row;
		}
		
		return $images;
	}

	public function getProductOcCategories($product_id) {
		$sql = "SELECT group_concat(ptc.category_id separator ',') as category_id FROM `" . DB_PREFIX . "product_to_category` ptc WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		return $res->row['category_id'];
	}

	public function getProductMsCategories($product_id) {
		$sql = "SELECT group_concat(msp2c.ms_category_id separator ',') as ms_category_id FROM `" . DB_PREFIX . "ms_product_to_category` msp2c WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		return $res->row['ms_category_id'];
	}

	public function getProductDownloads($product_id) {
		$sql = "SELECT 	*
				FROM `" . DB_PREFIX . "download` d
				LEFT JOIN `" . DB_PREFIX . "product_to_download` pd
					USING(download_id)
				WHERE pd.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		
		$downloads = array();
		foreach ($res->rows as $row) {
			$downloads[$row['download_id']] = $row;
		}
		
		return $downloads;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
		
		return $query->rows;
	}
	
	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
		
		return $query->rows;
	}

	public function getProductThumbnail($product_id) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->row;
	}

	public function getProductShipping($product_id, $data = array()) {
		$sql = "SELECT
					mps.from_country as `from_country_id`,
					c.name as `from_country_name`,
					mps.free_shipping,
					mps.processing_time,
					mps.override
				FROM " . DB_PREFIX . "ms_product_shipping mps
				LEFT JOIN " . DB_PREFIX . "country c
					ON (c.country_id = mps.from_country)
				WHERE mps.product_id = " . (int)$product_id;

		$res = $this->db->query($sql);

		if (!$res->num_rows) return FALSE;

		// Get shipping locations
		$sql = "";
		$sql .= "SELECT
					mpsl.product_shipping_location_id as `mspl.location_id`,
					mpsl.to_geo_zone_id as `to_geo_zone_id`,
					mpsl.shipping_method_id as `shipping_method_id`,
					msmd.name as `shipping_method_name`,
					delivery_time_id,
					cost,
					additional_cost
				FROM " . DB_PREFIX . "ms_product_shipping_location mpsl
				JOIN " . DB_PREFIX . "ms_shipping_method_description msmd
					ON (msmd.shipping_method_id = mpsl.shipping_method_id)
				WHERE 1 = 1
					AND product_id = " . (int)$product_id . "
					AND msmd.language_id = " . (int)$data['language_id']
				. (isset($data['product_shipping_location_id']) ? " AND mpsl.product_shipping_location_id = " . (int)$data['product_shipping_location_id'] : "")
				. (isset($data['to_geo_zone_id']) ? " AND mpsl.to_geo_zone_id = " . (int)$data['to_geo_zone_id'] : "")
				. (isset($data['shipping_method_id']) ? " AND mpsl.shipping_method_id = " . (int)$data['shipping_method_id'] : "")
				. (isset($data['delivery_time_id']) ? " AND delivery_time_id = " . (int)$data['delivery_time_id'] : "");

		$locations = $this->db->query($sql);
		$shipping_locations_data = array();
		foreach ($locations->rows as $key => $location) {
			$shipping_locations_data[$key] = $location;

			$res_geo_zone_name = $this->db->query("SELECT name FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = " . (int)$location['to_geo_zone_id']);
			$shipping_locations_data[$key]['to_geo_zone_name'] = ($res_geo_zone_name->num_rows && $res_geo_zone_name->row['name']) ? $res_geo_zone_name->row['name'] : $this->language->get('ms_account_product_shipping_elsewhere');
		}

		$res->row['locations'] = $shipping_locations_data;

		return $res->row;
	}

	public function getProductCommissionId($product_id) {
		$sql = "SELECT commission_id
				FROM `" . DB_PREFIX . "ms_product`
				WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);

		return $res->num_rows && isset($res->row['commission_id']) ? $res->row['commission_id'] : FALSE;
	}


	private function _saveImages($product_id, $images = array(), $data = array()) {
		foreach ($images as $key => $img) {
			$newImagePath = $this->MsLoader->MsFile->moveImage($img);
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
		}
	}

	private function _saveDownloads($product_id, $downloads = array(), $data = array()) {
		foreach ($downloads as $key => $dl) {
			$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
			$fileMask = substr($newFile,0,strrpos($newFile,'.'));

			$this->db->query("INSERT INTO " . DB_PREFIX . "download SET filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
			$download_id = $this->db->getLastId();
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			if(isset($data['languages'])) {
				foreach ($data['languages'] as $language_id => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
				}	
			}
		}
	}

	private function _saveKeyword($product_id, $keyword) {
		//$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($keyword) . "%' AND query LIKE 'product_id=%'");
		$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($keyword) . "%'");
		$number = $similarity_query->num_rows;

		if ($number > 0) {
			$keyword = $keyword . "-" . $number;
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
	}

	private function _saveDescriptions($product_id, $data = array()) {
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $l) {
			$language_id = $l['language_id'];
			$description = array(
				'product_name' => $data['product_name'][$language_id],
				'product_description' => $data['product_description'][$language_id],
				'product_meta_description' => isset($data['product_meta_description'][$language_id]) ? htmlspecialchars(nl2br($data['product_meta_description'][$language_id]), ENT_COMPAT) : '',
				'product_meta_keyword' => isset($data['product_meta_keywords'][$language_id]) ? htmlspecialchars(nl2br($data['product_meta_keywords'][$language_id]), ENT_COMPAT) : '',
				'product_meta_title' => isset($data['product_meta_title'][$language_id]) ? htmlspecialchars(nl2br($data['product_meta_title'][$language_id]), ENT_COMPAT) : '',
				'product_tags' => isset($data['product_tags'][$language_id]) ? $data['product_tags'][$language_id] : ''
			);

			$sql = "INSERT INTO " . DB_PREFIX . "product_description
					SET product_id = " . (int)$product_id . ",
						name = '" . $this->db->escape($description['product_name']) . "',
						description = '" . $this->db->escape($description['product_description']) . "',
						meta_description = '" . $this->db->escape($description['product_meta_description']) . "',
						meta_keyword = '" . $this->db->escape($description['product_meta_keyword']) . "',
						meta_title = '" . $this->db->escape($description['product_meta_title']) . "',
						tag = '" . $this->db->escape($description['product_tags']) . "',
						language_id = " . (int)$language_id;
			$this->db->query($sql);
		}
	}

	private function _updateDescriptions($product_id, $data = array()) {
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $l) {
			$language_id = $l['language_id'];
			$description = array(
				'product_name' => $data['product_name'][$language_id],
				'product_description' => $data['product_description'][$language_id],
				'product_meta_description' => isset($data['product_meta_description'][$language_id]) ? htmlspecialchars(nl2br($data['product_meta_description'][$language_id]), ENT_COMPAT) : '',
				'product_meta_keyword' => isset($data['product_meta_keywords'][$language_id]) ? htmlspecialchars(nl2br($data['product_meta_keywords'][$language_id]), ENT_COMPAT) : '',
				'product_meta_title' => isset($data['product_meta_title'][$language_id]) ? htmlspecialchars(nl2br($data['product_meta_title'][$language_id]), ENT_COMPAT) : '',
				'product_tags' => isset($data['product_tags'][$language_id]) ? $data['product_tags'][$language_id] : ''
			);

			$sql = "UPDATE " . DB_PREFIX . "product_description
					SET	name = '" . $this->db->escape($description['product_name']) . "',
						description = '" . $this->db->escape($description['product_description']) . "',
						meta_description = '" . $this->db->escape($description['product_meta_description']) . "',
						meta_keyword = '" . $this->db->escape($description['product_meta_keyword']) . "',
						meta_title = '" . $this->db->escape($description['product_meta_title']) . "',
						tag = '" . $this->db->escape($description['product_tags']) . "'
					WHERE product_id = " . (int)$product_id . "
					AND language_id = " . (int)$language_id;

			$this->db->query($sql);
		}


	}

	private function _saveOcCategories($product_id, $categories) {
		foreach ($categories as $id => $category_id) {
			$sql = "INSERT IGNORE INTO " . DB_PREFIX . "product_to_category
				SET product_id = " . (int)$product_id . ",
					category_id = " . (int)$category_id;
			$this->db->query($sql);
		}
	}

	private function _saveMsCategories($product_id, $ms_categories) {
		foreach ($ms_categories as $id => $category_id) {
			$sql = "INSERT IGNORE INTO " . DB_PREFIX . "ms_product_to_category
				SET product_id = " . (int)$product_id . ",
					ms_category_id = " . (int)$category_id;
			$this->db->query($sql);
		}
	}

	private function _saveStores($product_id, $stores) {
		foreach ($stores as $store_id) {
			$sql = "INSERT INTO " . DB_PREFIX . "product_to_store
					SET product_id = " . (int)$product_id . ",
						store_id = " . (int)$store_id;
			$this->db->query($sql);
		}
	}

	private function _saveOptions($product_id, $options) {
		foreach ($options as $product_option) {
			// unset sample
			if (isset($product_option['product_option_value'][0])) unset($product_option['product_option_value'][0]);

			// get type
			$o = $this->MsLoader->MsOption->getOptions(array('option_id' => $product_option['option_id'], 'single' => 1));
			if (!$o) continue; else { $product_option['type'] = $o['type']; }

			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (isset($product_option['required']) ? (int)$product_option['required'] : 0) . "'");

				$product_option_id = $this->db->getLastId();

				if (isset($product_option['product_option_value']) && count($product_option['product_option_value']) > 0 ) {
					foreach ($product_option['product_option_value'] as $product_option_value) {
						$product_option_value['price_prefix'] = ($product_option_value['price_prefix'] == '-' ? '-' : '+');
						//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (isset($product_option_value['subtract']) ? (int)$product_option_value['subtract'] : 0) . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_option_value['price']) . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "'");
					}
				}else{
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_option_id = '".$product_option_id."'");
				}
			} else {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value = '" . $this->db->escape($product_option['option_value']) . "', required = '" . (int)$product_option['required'] . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '', required = '" . (isset($product_option['required']) ? (int)$product_option['required'] : 0) . "'");
			}
		}
	}

	private function _saveSpecials($product_id, $specials = array()) {
		$customer_group_id = (int)$this->config->get('config_customer_group_id');

		foreach ($specials as $product_special) {
			if (isset($product_special['customer_group_id'])) $customer_group_id = (int)$product_special['customer_group_id'];
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . $customer_group_id . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_special['price']) . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
		}
	}

	private function _saveRelated($product_id, $related = array()) {
		foreach ($related as $related_id) {
			$this->db->query(
				"INSERT INTO " . DB_PREFIX . "product_related"
				. " SET product_id = '" . (int)$product_id . "', "
				. " related_id = '" . (int)$related_id . "'"
			);
		}
	}

	private function _saveDiscounts($product_id, $discounts = array()) {
		$customer_group_id = (int)$this->config->get('config_customer_group_id');
		foreach ($discounts as $product_discount) {
			if (isset($product_discount['customer_group_id'])) $customer_group_id = (int)$product_discount['customer_group_id'];
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . $customer_group_id . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($product_discount['price']) . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
		}
	}

	private function _saveFilters($product_id, $filters = array()) {
		foreach ($filters as $filter_id) {
			$this->db->query(
				"INSERT INTO " . DB_PREFIX . "product_filter"
				. " SET product_id = '" . (int) $product_id . "',"
				. " filter_id = '" . (int) $filter_id . "'"
			);
		}
	}

	private function _saveAttributes($product_id, $attributes = array()) {
		// unset sample
		if (isset($attributes[0])) unset($attributes[0]);

		foreach ($attributes as $product_attribute) {
			if ($product_attribute['attribute_id']) {
				foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($product_attribute_description['text']) . "'");
				}
			}
		}
	}

	private function _saveShipping($product_id, $shipping = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_product_shipping
				SET product_id = " . (int)$product_id . ",
					from_country = " . (int)$shipping['from_country']['id'] . ",
					free_shipping = " . (int)$shipping['free_shipping'] . ",
					processing_time = " . (int)$shipping['processing_time'] . ",
					override = " . (isset($shipping['override']) ? (int)$shipping['override'] : 0);

		$this->db->query($sql);

		foreach ($shipping['locations'] as $shipping_location) {
			// Check if selected countries and methods exist
			if((int)$shipping_location['method']['id'] > 0 && ($shipping_location['cost'] || (float)$shipping_location['cost'] == 0) && ($shipping_location['additional_cost'] || (float)$shipping_location['additional_cost'] == 0)) {
				$sql = "";
				$sql .= "INSERT INTO " . DB_PREFIX . "ms_product_shipping_location
						SET "
						. (isset($shipping_location['product_shipping_location_id']) && $shipping_location['product_shipping_location_id'] ? " product_shipping_location_id = " . (int)$shipping_location['product_shipping_location_id'] . "," : "")
						. " product_id = " . (int)$product_id . ",
							to_geo_zone_id = " . (isset($shipping_location['to_geo_zone']['id']) ? (int)$shipping_location['to_geo_zone']['id'] : 0) . ",
							shipping_method_id = " . (isset($shipping_location['method']['id']) ? (int)$shipping_location['method']['id'] : 0) . ",
							delivery_time_id = " . (isset($shipping_location['delivery_time']) ? (int)$shipping_location['delivery_time'] : 0) . ",
							cost = " . (float)$shipping_location['cost'] . ",
							additional_cost = " . (float)$shipping_location['additional_cost'] . "
						ON DUPLICATE KEY UPDATE
							product_id = " . (int)$product_id . ",
							to_geo_zone_id = " . (isset($shipping_location['to_geo_zone']['id']) ? (int)$shipping_location['to_geo_zone']['id'] : 0) . ",
							shipping_method_id = " . (isset($shipping_location['method']['id']) ? (int)$shipping_location['method']['id'] : 0) . ",
							delivery_time_id = " . (isset($shipping_location['delivery_time']) ? (int)$shipping_location['delivery_time'] : 0) . ",
							cost = " . (float)$shipping_location['cost'] . ",
							additional_cost = " . (float)$shipping_location['additional_cost'];

				$this->db->query($sql);
			}
		}
	}

	private function _createProduct($data = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "product
				SET model = '" . $this->db->escape(isset($data['product_model']) ? $data['product_model'] : reset($data['product_name'])) . "',
				    minimum = '" . (isset($data['minimum']) ? (int)$data['minimum'] : 1) . "',
				    sku = '" . $this->db->escape(isset($data['product_sku']) ? $data['product_sku'] : '') . "',
				    upc = '" . $this->db->escape(isset($data['product_upc']) ? $data['product_upc'] : '') . "',
				    ean = '" . $this->db->escape(isset($data['product_ean']) ? $data['product_ean'] : '') . "',
				    jan = '" . $this->db->escape(isset($data['product_jan']) ? $data['product_jan'] : '') . "',
				    isbn = '" . $this->db->escape(isset($data['product_isbn']) ? $data['product_isbn'] : '') . "',
				    mpn = '" . $this->db->escape(isset($data['product_mpn']) ? $data['product_mpn'] : '') . "',
				    manufacturer_id = '" . (isset($data['product_manufacturer_id']) ? (int)$data['product_manufacturer_id'] : 0 ) . "',
				    price = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($data['product_price']) . ",
				    weight = '" . (isset($data['weight']) ? (float)$data['weight'] : 0) . "',
				    weight_class_id = '" . (isset($data['weight_class_id']) ? (int)$data['weight_class_id'] : 0) . "',
				    length = '" . (isset($data['length']) ? (float)$data['length'] : 0) . "',
				    width = '" . (isset($data['width']) ? (float)$data['width'] : 0) . "',
				    height = '" . (isset($data['height']) ? (float)$data['height'] : 0) . "',
				    length_class_id = '" . (isset($data['length_class_id']) ? (int)$data['length_class_id'] : 0) . "',
					image = '" .  $this->db->escape(isset($data['product_thumbnail'])? $this->MsLoader->MsFile->moveImage($data['product_thumbnail']) : '')  . "',
					subtract = " . (isset($data['product_subtract']) ? (int)$data['product_subtract'] : 1) . ",
					tax_class_id = '" . (isset($data['product_tax_class_id']) ? (int)$data['product_tax_class_id'] : 0) . "',
					stock_status_id = '" . (isset($data['product_stock_status_id']) ? (int)$data['product_stock_status_id'] : (int)$this->getDefaultStockStatus()) . "',
					date_available = '" . $this->db->escape(isset($data['product_date_available']) ? $data['product_date_available'] : date('Y-m-d', time() - 86400)) . "',
					quantity = " . (int)$data['product_quantity'] . ",
					shipping = " . (int)$data['product_enable_shipping'] . ",
					status = " . (int)$data['enabled'] . ",
					date_added = NOW(),
					date_modified = NOW()";

		$this->db->query($sql);
		return $this->db->getLastId();
	}

	private function _updateProduct($product_id, $data = array()) {
        $included_field_sql = '';
        isset($data['product_model']) ? $included_field_sql .= " model = '" . $this->db->escape($data['product_model']) . "',"  : '';
        isset($data['minimum']) ? $included_field_sql .= " minimum = '" . (int)$data['minimum'] . "'," : '';
        isset($data['product_sku']) ? $included_field_sql .= " sku = '" . $this->db->escape($data['product_sku']) . "',"  : '';
        isset($data['product_upc']) ? $included_field_sql .= " upc = '" . $this->db->escape($data['product_upc']) . "',"  : '';
        isset($data['product_ean']) ? $included_field_sql .= " ean = '" . $this->db->escape($data['product_ean']) . "',"  : '';
        isset($data['product_jan']) ? $included_field_sql .= " jan = '" . $this->db->escape($data['product_jan']) . "',"  : '';
        isset($data['product_isbn']) ? $included_field_sql .= " isbn = '" . $this->db->escape($data['product_isbn']) . "',"  : '';
        isset($data['product_mpn']) ? $included_field_sql .= " mpn = '" . $this->db->escape($data['product_mpn']) . "',"  : '';
        isset($data['product_manufacturer_id']) ? $included_field_sql .= " manufacturer_id = '" . (int)$data['product_manufacturer_id'] . "',"  : '';
        isset($data['product_tax_class_id']) ? $included_field_sql .= " tax_class_id = '" . $this->db->escape($data['product_tax_class_id']) . "',"  : '';
        isset($data['product_stock_status_id']) ? $included_field_sql .= " stock_status_id = '" . (int)$data['product_stock_status_id'] . "',"  : '';
        isset($data['product_date_available']) ? $included_field_sql .= " date_available = '" . $this->db->escape($data['product_date_available']) . "',"  : '';

		isset($data['weight']) ? $included_field_sql .= " weight = '" . $this->db->escape($data['weight']) . "',"  : '';
		isset($data['weight_class_id']) ? $included_field_sql .= " weight_class_id = '" . $this->db->escape($data['weight_class_id']) . "',"  : '';
		isset($data['length']) ? $included_field_sql .= " length = '" . $this->db->escape($data['length']) . "',"  : '';
		isset($data['width']) ? $included_field_sql .= " width = '" . $this->db->escape($data['width']) . "',"  : '';
		isset($data['height']) ? $included_field_sql .= " height = '" . $this->db->escape($data['height']) . "',"  : '';
		isset($data['length_class_id']) ? $included_field_sql .= " length_class_id = '" . $this->db->escape($data['length_class_id']) . "',"  : '';

		$sql = "UPDATE " . DB_PREFIX . "product
				SET" . $included_field_sql . " price = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($data['product_price']) . ",
					status = " . (int)$data['enabled'] . ",
					subtract = " . (isset($data['product_subtract']) ? (int)$data['product_subtract'] : 1) . ",
					quantity = " . (int)$data['product_quantity'] . ",
					shipping = " . (int)$data['product_enable_shipping'] . ",
					date_modified = NOW()
				WHERE product_id = " . (int)$product_id;

		$this->db->query($sql);
	}


	private function _updateImages($product_id, $data) {
		$old_thumbnail = $this->getProductThumbnail($product_id);
		$old_images = $this->getProductImages($product_id);

		// thumbnail
		if (isset($data['product_thumbnail'])) {
			$keep_thumbnail = false;
			foreach ($old_images as $old_image) {
				if ($old_image['image'] == $data['product_thumbnail']) {
					$keep_thumbnail = true;
					$thumbnail = $old_image['image'];
					break;
				}
			}

			if (!$keep_thumbnail) {
				if ($old_thumbnail['image'] == $data['product_thumbnail']) {
					$thumbnail = $old_thumbnail['image'];
				} else {
					$this->MsLoader->MsFile->deleteImage($old_thumbnail['image']);
					$thumbnail = $this->MsLoader->MsFile->moveImage($data['product_thumbnail']);
				}
			}
		} else {
			$this->MsLoader->MsFile->deleteImage($old_thumbnail['image']);
			$thumbnail = '';
		}

		$sql = "UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($thumbnail) . "' WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);

		// images
		if (isset($data['product_images'])) {

			$new_images = $data['product_images'];

			foreach($old_images as $k => $old_image) {
				$key = array_search($old_image['image'], $data['product_images']);
				if ($key !== FALSE) {
					unset($old_images[$k]);
					unset($data['product_images'][$key]);
				}
			}

			foreach ($data['product_images'] as $key => $product_image) {
				$newImagePath = $this->MsLoader->MsFile->moveImage($product_image);
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($newImagePath, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)array_search($product_image, $new_images) . "'");
			}

			$i = 0;
			foreach ($new_images as $key => $image) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_image SET sort_order = " . $i++ . " WHERE product_id = '" . (int)$product_id . "' AND image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "'");
			}
		}

		// delete old images
		foreach($old_images as $old_image) {
			if ($old_image['image'] != $thumbnail) {
				$this->MsLoader->MsFile->deleteImage($old_image['image']);
			}
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' AND product_image_id = '" . (int)$old_image['product_image_id'] . "'");
		}
	}

	private function _updateDownloads($product_id, $data){
		$old_downloads = $this->getProductDownloads($product_id);
		$languages = $this->model_localisation_language->getLanguages();

		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				if (!empty($dl['download_id'])) {
					if (!empty($dl['filename'])) {
						// update download #download_id:
						$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
						$fileMask = substr($newFile,0,strrpos($newFile,'.'));

						$this->db->query("UPDATE " . DB_PREFIX . "download SET filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "'");


						foreach ($languages as $l) {
							$language_id = $l['language_id'];
							$this->db->query("UPDATE " . DB_PREFIX . "download_description SET name = '" . $this->db->escape($fileMask) . "' WHERE download_id = '" . (int)$dl['download_id'] . "' AND language_id = '" . (int)$language_id . "'");
						}

						$this->MsLoader->MsFile->deleteDownload($old_downloads[$dl['download_id']]['filename']);
					} else {
						// do nothing
					}

					// don't remove the download
					unset($old_downloads[$dl['download_id']]);
				} else if (!empty($dl['filename'])) {
					// add new download
					$newFile = $this->MsLoader->MsFile->moveDownload($dl['filename']);
					$fileMask = substr($newFile,0,strrpos($newFile,'.'));

					$this->db->query("INSERT INTO " . DB_PREFIX . "download SET filename = '" . $this->db->escape($newFile) . "', mask = '" . $this->db->escape($fileMask) . "'");
					$download_id = $this->db->getLastId();
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");

					foreach ($languages as $l) {
							$language_id = $l['language_id'];
						$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($fileMask) . "', language_id = '" . (int)$language_id . "'");
					}
				}
			}
		}

		if (!empty($old_downloads)) {
			foreach($old_downloads as $old_download) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE download_id ='" . (int)$old_download['download_id'] . "'");
				$this->MsLoader->MsFile->deleteDownload($old_download['filename']);
			}
		}
	}

	/*
	 *
	 *
	 *
	 */

	public function saveProduct($data) {
		// create main record
		$product_id = $this->_createProduct($data);

		// keyword
		if(isset($data['keyword']) && !empty($data['keyword'])) $this->_saveKeyword($product_id, $data['keyword']);

		// categories
		if (isset($data['product_category'])) $this->_saveOcCategories($product_id, $data['product_category']);

		// seller categories
		if (isset($data['product_ms_category'])) $this->_saveMsCategories($product_id, $data['product_ms_category']);

		// stores (default by default)
		$data['product_store'] = array($this->config->get('config_store_id'));
		if (isset($data['product_store'])) $this->_saveStores($product_id, $data['product_store']);

		// images
		if (isset($data['product_images'])) $this->_saveImages($product_id, $data['product_images'], $data);

		// downloads
		if (isset($data['product_downloads'])) $this->_saveDownloads($product_id, $data['product_downloads'], $data);

		// options
		if (isset($data['product_option'])) $this->_saveOptions($product_id, $data['product_option']);

		// specials
		if (isset($data['product_specials'])) $this->_saveSpecials($product_id, $data['product_specials']);

		// discounts
		if (isset($data['product_discounts'])) $this->_saveDiscounts($product_id, $data['product_discounts']);

		// filters
		if (isset($data['product_filter'])) $this->_saveFilters($product_id, $data['product_filter']);

		// related products
		if (isset($data['product_related'])) $this->_saveRelated($product_id, $data['product_related']);

		// attributes
		if (isset($data['product_attribute'])) $this->_saveAttributes($product_id, $data['product_attribute']);

		// shipping
		if (isset($data['product_shipping']) && ($data['product_shipping']['free_shipping'] || !empty($data['product_shipping']['locations']))) {
			$this->_saveShipping($product_id, $data['product_shipping']);
		}

		// digital product
		if(isset($data['product_is_digital'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET shipping = '" . (($data['product_is_digital'] && $this->config->get('msconf_allow_digital_products')) ? 0 : 1) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		// descriptions, metas
		$this->_saveDescriptions($product_id, $data);

		$sql = "INSERT INTO " . DB_PREFIX . "ms_product
				SET product_id = " . (int)$product_id . ",
					seller_id = " . (int)$this->registry->get('customer')->getId() . ",
					product_status = " . (int)$data['product_status'] . ",
					product_approved = " . (int)$data['product_approved']
					. ( (isset($data['list_until']) && $data['list_until'] != NULL ) ? ", list_until = '" . $this->db->escape($data['list_until']) . "'" : "");

		$this->db->query($sql);

		// delete OC product cache
		$this->registry->get('cache')->delete('product');
		
		return $product_id;
	}	

	public function editProduct($data) {
		$product_id = $data['product_id'];

		// delete existing records
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'product_id=" . (int)$product_id . "'");

		if(isset($data['product_shipping'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_shipping WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_shipping_location WHERE product_id = '" . (int)$product_id . "'");
		}

		if (isset($data['product_ms_category'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_to_category WHERE product_id = '" . (int)$product_id . "'");
		}

		// update main record
		$this->_updateProduct($product_id, $data);

		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET product_status = " . (int)$data['product_status'] . ",
					product_approved = " . (int)$data['product_approved'] .
				" WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);

		// keyword
		if(isset($data['keyword']) && !empty($data['keyword'])) $this->_saveKeyword($product_id, $data['keyword']);

		// categories
		if (isset($data['product_category'])) $this->_saveOcCategories($product_id, $data['product_category']);

		// seller categories
		if (isset($data['product_ms_category'])) $this->_saveMsCategories($product_id, $data['product_ms_category']);

		// options
		if (isset($data['product_attribute'])) $this->_saveAttributes($product_id, $data['product_attribute']);

		// options
		if (isset($data['product_option'])) $this->_saveOptions($product_id, $data['product_option']);

		// specials
		if (isset($data['product_specials'])) $this->_saveSpecials($product_id, $data['product_specials']);

		// discounts
		if (isset($data['product_discounts'])) $this->_saveDiscounts($product_id, $data['product_discounts']);

		// filters
		if (isset($data['product_filter'])) $this->_saveFilters($product_id, $data['product_filter']);

		// related products
		if (isset($data['product_related'])) $this->_saveRelated($product_id, $data['product_related']);

		// shipping
		if (isset($data['product_shipping']) && ($data['product_shipping']['free_shipping'] || !empty($data['product_shipping']['locations']))) {
			$this->_saveShipping($product_id, $data['product_shipping']);
		}

		// digital product
		if(isset($data['product_is_digital'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET shipping = '" . (($data['product_is_digital'] && $this->config->get('msconf_allow_digital_products')) ? 0 : 1) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		// descriptions, metas
		$this->_updateDescriptions($product_id, $data);

		// images & thumbnail
		$this->_updateImages($product_id, $data);

		// downloads
		$this->_updateDownloads($product_id, $data);

		// delete OC product cache
		$this->registry->get('cache')->delete('product');
		
		return $product_id;
	}
	
	public function hasDownload($product_id, $download_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "product_to_download`
				WHERE product_id = " . (int)$product_id . " 
				AND download_id = " . (int)$download_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];
	}
	
	public function getDownload($download_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");
		
		return $query->row;
	}
	
	public function productOwnedBySeller($product_id, $seller_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_product`
				WHERE seller_id = " . (int)$seller_id . " 
				AND product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];			
	}
	
	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product WHERE product_id = '" . (int)$product_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");

		// delete unpaid listing fee requests for this product
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_pg_request WHERE product_id = '" . (int)$product_id . "' AND request_type = '" . (int)MsPgRequest::TYPE_LISTING . "' AND request_status = '" . (int)MsPgRequest::STATUS_UNPAID . "'");

		$this->registry->get('cache')->delete('product');		
	}
	
	/*****************************************/

	public function getTotalProducts($data) {

		// fix for counting different language translations
		// as separate products
		$sql = "
			SELECT COUNT(DISTINCT product_id) as total
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "ms_product mp
				USING (product_id)
			LEFT JOIN " . DB_PREFIX . "ms_seller ms
				USING (seller_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd
				USING(product_id)
			WHERE 1 = 1 "
			. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['search']) ? " AND pd.name LIKE '%" .  $this->db->escape($data['search']) . "%'" : '')
			. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
			. (isset($data['enabled']) ? " AND status =  " .  (int)$data['enabled'] : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	public function getTotalProductViews($data) {
		$sql = "
			SELECT SUM(p.viewed) as total
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "ms_product mp
				USING (product_id)
			LEFT JOIN " . DB_PREFIX . "ms_seller ms
				USING (seller_id)
			WHERE 1 = 1 "
			. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	//todo
	public function getProduct($product_id) {
		$sql = "SELECT 	p.price,
		                p.model, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn, p.minimum,
		                p.manufacturer_id, p.tax_class_id, p.subtract, p.stock_status_id, p.date_available,
		                p.weight, p.weight_class_id,
						p.product_id as 'product_id',
						mp.product_status as 'mp.product_status',
						p.status as enabled,
						p.image as thumbnail,
						p.shipping as shipping,
						p.quantity as quantity,
						(SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "' LIMIT 1) AS keyword,
						mp.product_status,
						mp.product_approved,
						mp.commission_id,
						mp.seller_id
				FROM `" . DB_PREFIX . "product` p
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON p.product_id = mp.product_id
				WHERE p.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);

		if (!$res->num_rows) return FALSE;

		$sql = "SELECT pd.*,
					   pd.description as 'pd.description'
				FROM " . DB_PREFIX . "product_description pd
				WHERE pd.product_id = " . (int)$product_id . "
				GROUP BY language_id";

		$descriptions = $this->db->query($sql);
		$product_description_data = array();
		foreach ($descriptions->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'tags'      => $result['tag'],
				'meta_keyword'     => $result['meta_keyword'],
				'meta_title'     => $result['meta_title'],
				'meta_description' => $result['meta_description']
			);
		}

		$res->row['languages'] = $product_description_data;
		return $res->row;
	}
	
	public function getProducts($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}
		// todo validate order parameters
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS "
					// additional columns
					. (isset($cols['product_earnings']) ? "
						(SELECT SUM(seller_net_amt) AS seller_total
						FROM " . DB_PREFIX . "order_product op
						INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
							ON (op.product_id = mopd.product_id)
						WHERE op.product_id = p.product_id) as product_earnings,
					" : "")

					. (isset($cols['product_sales']) ? "
						(SELECT count(*) as total FROM " . DB_PREFIX . "order_product op
						LEFT JOIN ". DB_PREFIX . "ms_product msp
							ON (msp.product_id = op.product_id)
						LEFT JOIN `" . DB_PREFIX . "order` o
							ON (o.order_id = op.order_id)
						WHERE op.product_id = p.product_id
							AND msp.seller_id = ms.seller_id
							AND o.order_status_id > '0') as number_sold,
					" : "")
					
					."p.product_id as 'product_id',
					p.image as 'p.image',
					p.price as 'p.price',
					p.quantity as 'p.quantity',
					p.model as 'p.model',
					pd.name as 'pd.name',
					ms.seller_id as 'seller_id',
					ms.nickname as 'ms.nickname',
					mp.product_status as 'mp.product_status',
					mp.product_approved as 'mp.product_approved',
					mp.list_until as 'mp.list_until',
					p.date_added as 'p.date_added',
					p.date_modified  as 'p.date_modified',
					pd.description as 'pd.description'
				FROM " . DB_PREFIX . "product p
				INNER JOIN " . DB_PREFIX . "product_description pd
					USING(product_id)
				LEFT JOIN " . DB_PREFIX . "ms_product mp
					USING(product_id)
				LEFT JOIN " . DB_PREFIX . "ms_seller ms
					USING (seller_id)
				LEFT JOIN " . DB_PREFIX . "product_to_category p2c
					ON(p2c.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "ms_product_to_category msp2c
					ON(msp2c.product_id = p.product_id)
				WHERE 1 = 1"

				. (isset($data['seller_id']) ? " AND ms.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['category_id']) ? " AND p2c.category_id =  " .  (int)$data['category_id'] : '')
				. (isset($data['ms_category_id']) ? " AND msp2c.ms_category_id =  " .  (int)$data['ms_category_id'] : '')
				. (isset($data['language_id']) ? " AND pd.language_id =  " .  (int)$data['language_id'] : '')
				. (isset($data['product_status']) ? " AND product_status IN  (" .  $this->db->escape(implode(',', $data['product_status'])) . ")" : '')
				
				. $wFilters
				
				. " GROUP BY p.product_id HAVING 1 = 1 "
				
				. $hFilters
				
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');
		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return $res->rows;
	}
	
	public function getStatus($product_id) {
		$sql = "SELECT mp.product_status AS status
				FROM `" . DB_PREFIX . "ms_product` mp
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['status'];
	}

	public function changeStatus($product_id, $product_status) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	product_status =  " .  (int)$product_status . "
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);

		if ($product_status == MsProduct::STATUS_ACTIVE)
			$enabled = 1;
		else
			$enabled = 0;
		
		$sql = "UPDATE " . DB_PREFIX . "product
				SET status = " . (int)$enabled . " WHERE product_id = " . (int)$product_id;

		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');
	}
	
	public function approve($product_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	product_approved =  1
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');
	}
	
	public function disapprove($product_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	product_approved =  0
				WHERE product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		$this->registry->get('cache')->delete('product');
	}
	
	public function createRecord($product_id, $data = array()) {
		$sql = "INSERT IGNORE INTO " . DB_PREFIX . "ms_product
				SET	product_id =  " . (int)$product_id . ",
					product_status = " . (int)MsProduct::STATUS_INACTIVE
				. (isset($data['seller_id']) ? ", seller_id =  " .  (int)$data['seller_id'] : '');
		
		$res = $this->db->query($sql);
	}
	
	public function getSaleData($product_id) {
		// note: change getProducts() if changing this
		$sql = "SELECT SUM(seller_net_amt) AS seller_total
				FROM " . DB_PREFIX . "order_product op
				INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
					ON (op.product_id = mopd.product_id)
				WHERE op.product_id = " . (int)$product_id . "
				GROUP BY op.order_product_id";

		$res = $this->db->query($sql);
		
		return $res->num_rows ? $res->row : FALSE;
	}
	
	public function changeSeller($product_id, $seller_id) {
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET	seller_id =  " . (int)$seller_id . "
				WHERE product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
		$this->registry->get('cache')->delete('product');
	}

    public function getManufacturers($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
}
?>
