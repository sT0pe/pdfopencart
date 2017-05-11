<?php
class MsShippingMethod extends Model {
	// Shipping methods status
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;


	/**
	 *  Admin-side related methods
	 */
	public function getShippingMethods($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*
					FROM `" . DB_PREFIX . "ms_shipping_method` sm
					JOIN `" . DB_PREFIX . "ms_shipping_method_description` smd
						USING(shipping_method_id)

					WHERE 1 = 1"

			. (isset($data['shipping_method_id']) ? " AND sm.shipping_method_id =  " .  (int)$data['shipping_method_id'] : '')
			. (isset($data['language_id']) ? " AND smd.language_id =  " .  (int)$data['language_id'] : '')

			. $wFilters

			. " GROUP BY sm.shipping_method_id HAVING 1 = 1 "

			. $hFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		if(isset($data['shipping_method_id'])) {
			$sql = "SELECT
					*
					FROM " . DB_PREFIX . "ms_shipping_method_description
					WHERE shipping_method_id = " . (int)$data['shipping_method_id'];

			$descriptions = $this->db->query($sql);
			$shipping_method_description_data = array();
			foreach ($descriptions->rows as $result) {
				$shipping_method_description_data[$result['language_id']] = array(
					'name'             => $result['name'],
					'description'      => $result['description'],
				);
			}
			$res->row['languages'] = $shipping_method_description_data;
		}

		return (isset($data['shipping_method_id'])) ? $res->row : $res->rows;
	}

	public function createShippingMethod($data = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_shipping_method
				SET logo = '" . $this->db->escape($data['logo']) . "',
					status = " . (isset($data['status']) ? (int)$data['status'] : 2);

		$this->db->query($sql);
		$shipping_method_id = $this->db->getLastId();

		if(isset($data['description'])) {
			foreach ($data['description'] as $language_id => $row) {
				$sql = "INSERT INTO " . DB_PREFIX . "ms_shipping_method_description
						SET shipping_method_id = " . (int)$shipping_method_id . ",
							name = '" . $this->db->escape($row['name']) . "',
							description = '" . $this->db->escape($row['description']) . "',
							language_id = " . (int)$language_id;

				$this->db->query($sql);
			}
		}
	}

	public function editShippingMethod($shipping_method_id, $data = array()) {
		$sql = "UPDATE " . DB_PREFIX . "ms_shipping_method
				SET logo = '" . $this->db->escape($data['logo']) . "',
					status = " . (isset($data['status']) ? (int)$data['status'] : 2) . "
				WHERE
					shipping_method_id = " . (int)$shipping_method_id;

		$this->db->query($sql);

		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_shipping_method_description WHERE shipping_method_id = " . (int)$shipping_method_id);
		if(isset($data['description'])) {
			foreach ($data['description'] as $language_id => $row) {
				$sql = "INSERT INTO " . DB_PREFIX . "ms_shipping_method_description
						SET shipping_method_id = " . (int)$shipping_method_id . ",
							name = '" . $this->db->escape($row['name']) . "',
							description = '" . $this->db->escape($row['description']) . "',
							language_id = " . (int)$language_id;

				$this->db->query($sql);
			}
		}

	}

	public function deleteShippingMethod($shipping_method_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_shipping_method WHERE shipping_method_id = " . (int)$shipping_method_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_shipping_method_description WHERE shipping_method_id = " . (int)$shipping_method_id);
	}


	public function shippingMethodExists($shipping_method_id) {
		$exists = $this->db->query("SELECT 1 FROM " . DB_PREFIX . "ms_shipping_method WHERE shipping_method_id = " . (int)$shipping_method_id);
		return $exists->num_rows;
	}


	public function getShippingDeliveryTimes($data = array()) {
		$delivery_times_data = array();
		$sql = "SELECT 
					*
				FROM " . DB_PREFIX . "ms_shipping_delivery_time
				WHERE 1 = 1"

			. (isset($data['delivery_time_id']) ? " AND delivery_time_id = " . (int)$data['delivery_time_id'] : "");

		$delivery_time_ids = $this->db->query($sql);

		foreach ($delivery_time_ids->rows as $row) {
			$sql = "SELECT
						*
					FROM " . DB_PREFIX . "ms_shipping_delivery_time_description
					WHERE delivery_time_id = " . (int)$row['delivery_time_id']

				. (isset($data['language_id']) ? " AND language_id = " . (int)$data['language_id'] : "");

			$res = $this->db->query($sql);

			foreach ($res->rows as $time) {
				$delivery_times_data[$row['delivery_time_id']][$time['language_id']] = $time['name'];
			}
		}

		return $delivery_times_data;
	}

	public function createShippingDeliveryTime($data = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_shipping_delivery_time () VALUES ()";

		$this->db->query($sql);
		$delivery_time_id = $this->db->getLastId();

		foreach ($data['names'] as $language_id => $name) {
			if($name) {
				$sql = "INSERT INTO " . DB_PREFIX . "ms_shipping_delivery_time_description
						SET
							delivery_time_id = " . (int)$delivery_time_id . ",
							name = '" . $this->db->escape($name) . "',
							language_id = " . (int)$language_id;

				$this->db->query($sql);
			}
		}

		return $delivery_time_id;
	}

	public function editShippingDeliveryTime($data = array()) {
		$sql = "UPDATE " . DB_PREFIX . "ms_shipping_delivery_time_description
				SET 
					name = '" . $this->db->escape($data['name']) . "'
				WHERE 1 = 1
					AND delivery_time_id = " . (int)$data['delivery_time_id'] . "
					AND language_id = " . (int)$data['language_id'];

		$this->db->query($sql);
	}

	public function deleteShippingDeliveryTime($delivery_time_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_shipping_delivery_time WHERE delivery_time_id = " . (int)$delivery_time_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_shipping_delivery_time_description WHERE delivery_time_id = " . (int)$delivery_time_id);
	}

	/**
	 * Seller-side related methods
	 */
	public function getShippingCountries($data = array()) {
		$sql = "SELECT 
				country_id,
				name
				FROM " . DB_PREFIX . "country
				WHERE 1 = 1"

			. (isset($data['name']) ? " AND name LIKE '" . $this->db->escape($data['name']) . "%'" : "")
			. (isset($data['country_id']) ? " AND country_id = " . (int)$data['country_id'] : "");

		$res = $this->db->query($sql);

		return $res->rows;
	}

	public function getShippingGeoZones($data = array()) {
		$sql = "SELECT
				gz.geo_zone_id,
				gz.name
				FROM " . DB_PREFIX . "geo_zone gz
				WHERE 1 = 1"

			. (isset($data['name']) ? " AND name LIKE '" . $this->db->escape($data['name']) . "%'" : "")
			. (isset($data['geo_zone_id']) ? " AND geo_zone_id = " . (int)$data['geo_zone_id'] : "");

		$res = $this->db->query($sql);

		return isset($data['single']) ? $res->row : $res->rows;
	}

	public function getShippingAvailability($data = array()) {
		$sql = "SELECT DISTINCT country_id, zone_id
				FROM " . DB_PREFIX . "zone_to_geo_zone
				WHERE 1 = 1"

			. (isset($data['geo_zone_id']) ? " AND geo_zone_id = " . (int)$data['geo_zone_id'] : "");

		$geo_zone_data = $this->db->query($sql);

		$shipping_available = false;
		if(!empty($geo_zone_data->rows)) {
			foreach ($geo_zone_data->rows as $row) {
				if((isset($data['country_id']) && (int)$row['country_id'] == (int)$data['country_id']) && ((isset($data['zone_id']) && (int)$row['zone_id'] == (int)$data['zone_id']) || (int)$row['zone_id'] == 0)) {
					$shipping_available = true;
				}
			}
		}

		return $shipping_available;
	}

	public function getShippingCompanies($data = array()) {
		$sql = "SELECT 
					shipping_method_id,
					name
				FROM " . DB_PREFIX . "ms_shipping_method_description smd
				JOIN " . DB_PREFIX . "ms_shipping_method sm
					USING (shipping_method_id)
				WHERE sm.status = 1"

			. (isset($data['name']) ? " AND smd.name LIKE '" . $this->db->escape($data['name']) . "%'" : "")
			. (isset($data['language_id']) ? " AND smd.language_id = " . (int)$data['language_id'] : "")
			. (isset($data['method_id']) ? " AND smd.shipping_method_id = " . (int)$data['method_id'] : "");

		$res = $this->db->query($sql);

		return isset($data['single']) ? $res->row : $res->rows;
	}


	/**
	 * Weight-based combined shipping related methods
	 */
	public function getSellerShipping($seller_id, $data = array()) {
		$ssm_data = array();

		$sql = "SELECT
					mss.*,
					c.name as from_country_name
				FROM " . DB_PREFIX . "ms_seller_shipping mss
				LEFT JOIN " . DB_PREFIX . "country c
					ON (mss.from_country_id = c.country_id)
				WHERE 1 = 1"

			. (isset($seller_id) ? " AND seller_id = " . (int)$seller_id : "");

		$res = $this->db->query($sql);

		if($res->num_rows && $res->row['seller_shipping_id']) {
			$ssm_data = $res->rows[0];

			$sql2 = "SELECT
						mssl.seller_shipping_location_id,
						mssl.seller_shipping_id,
						mssl.shipping_method_id,
						msmd.name as shipping_method_name,
						mssl.delivery_time_id,
						msdtd.name as delivery_time_name,
						mssl.to_geo_zone_id,
						IF(mssl.to_geo_zone_id <> 0, gz.name, IF(mssl.to_geo_zone_id = 0, '" . $this->db->escape($this->language->get('ms_account_product_shipping_elsewhere')) . "', NULL)) as to_geo_zone_name,
						mssl.weight_from,
						mssl.weight_to,
						mssl.weight_class_id,
						wcd.title as weight_class_name,
						wcd.unit as weight_class_unit,
						mssl.cost_fixed,
						mssl.cost_pwu
					FROM " . DB_PREFIX . "ms_seller_shipping_location mssl
					LEFT JOIN " . DB_PREFIX . "geo_zone gz
						ON (mssl.to_geo_zone_id = gz.geo_zone_id)
					LEFT JOIN " . DB_PREFIX . "ms_shipping_method_description msmd
						ON (mssl.shipping_method_id = msmd.shipping_method_id)
					LEFT JOIN " . DB_PREFIX . "ms_shipping_delivery_time_description msdtd
						ON (mssl.delivery_time_id = msdtd.delivery_time_id)
					LEFT JOIN " . DB_PREFIX . "weight_class_description wcd
						ON (mssl.weight_class_id = wcd.weight_class_id)
					WHERE
						seller_shipping_id = " . (int)$res->row['seller_shipping_id'] . "
						AND msmd.language_id = '" . (int)$this->config->get('config_language_id') . "'
						AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
						AND msdtd.language_id = '" . (int)$this->config->get('config_language_id') . "'"
					. (isset($data['seller_shipping_location_id']) ? " AND mssl.seller_shipping_location_id = " . (int)$data['seller_shipping_location_id'] : "")
					. (isset($data['to_geo_zone_id']) ? " AND mssl.to_geo_zone_id = " . (int)$data['to_geo_zone_id'] : "")
					. (isset($data['shipping_method_id']) ? " AND mssl.shipping_method_id = " . (int)$data['shipping_method_id'] : "")
					. (isset($data['delivery_time_id']) ? " AND mssl.delivery_time_id = " . (int)$data['delivery_time_id'] : "");

			$res2 = $this->db->query($sql2);

			$ssm_data['methods'] = $res->num_rows ? $res2->rows : array();
		}

		return $ssm_data;
	}

	public function saveSellerShipping($seller_id, $data = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_seller_shipping
				SET	"
					. (isset($data['seller_shipping_id']) && $data['seller_shipping_id'] ? " seller_shipping_id = " . (int)$data['seller_shipping_id'] . "," : "")
					. " seller_id = " . (int)$seller_id . ",
					from_country_id = " . (int)$data['from_country_id'] . ",
					processing_time = " . (int)$data['processing_time'] . "
				ON DUPLICATE KEY UPDATE
					from_country_id = " . (int)$data['from_country_id'] . ",
		            processing_time = " . (int)$data['processing_time'];

		$this->db->query($sql);
		$seller_shipping_id = isset($data['seller_shipping_id']) && $data['seller_shipping_id'] ? $data['seller_shipping_id'] : $this->db->getLastId();

		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_shipping_location WHERE seller_shipping_id = '" . (int)$seller_shipping_id . "'");

		foreach ($data['methods'] as $ssm) {
			$weight_to = $ssm['weight_to'] == '-' ? PHP_INT_MAX : $ssm['weight_to'];

			$sql = "INSERT INTO " . DB_PREFIX . "ms_seller_shipping_location
					SET "
						. (isset($ssm['seller_shipping_location_id']) && $ssm['seller_shipping_location_id'] ? " seller_shipping_location_id = " . (int)$ssm['seller_shipping_location_id'] . "," : "")
						. " seller_shipping_id = " . (int)$seller_shipping_id . ",
						shipping_method_id = " . (int)$ssm['shipping_method_id'] . ",
						delivery_time_id = " . (int)$ssm['delivery_time_id'] . ",
						to_geo_zone_id = " . (int)$ssm['to_geo_zone_id'] . ",
						weight_from = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($ssm['weight_from']) . ",
						weight_to = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($weight_to) . ",
						weight_class_id = " . (int)$ssm['weight_class_id'] . ",
						cost_fixed = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($ssm['cost_fixed']) . ",
						cost_pwu = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($ssm['cost_pwu']) . "
					ON DUPLICATE KEY UPDATE
						seller_shipping_id = " . (int)$seller_shipping_id . ",
						shipping_method_id = " . (int)$ssm['shipping_method_id'] . ",
						delivery_time_id = " . (int)$ssm['delivery_time_id'] . ",
						to_geo_zone_id = " . (int)$ssm['to_geo_zone_id'] . ",
						weight_from = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($this->MsLoader->MsHelper->trueCurrencyFormat($ssm['weight_from'])) . ",
						weight_to = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($this->MsLoader->MsHelper->trueCurrencyFormat($weight_to)) . ",
						weight_class_id = " . (int)$ssm['weight_class_id'] . ",
						cost_fixed = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($this->MsLoader->MsHelper->trueCurrencyFormat($ssm['cost_fixed'])) . ",
						cost_pwu = " . (float)$this->MsLoader->MsHelper->uniformDecimalPoint($this->MsLoader->MsHelper->trueCurrencyFormat($ssm['cost_pwu']));

			$this->db->query($sql);
		}
	}
}
?>