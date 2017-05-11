<?php
final class MsSetting extends Model {
	private $_slr_settings = array(
		'slr_full_name' => '',
		'slr_address_line1' => '',
		'slr_address_line2' => '',
		'slr_city' => '',
		'slr_state' => '',
		'slr_zip' => '',
		'slr_country' => 0,
		'slr_company' => '',
		'slr_website' => '',
		'slr_phone' => '',
		'slr_logo' => '',
		'slr_ga_tracking_id' => ''
	);

	private $_slr_gr_settings = array(
		'slr_gr_product_number_limit' => ''
	);


	// Seller related methods

	/**
	 * Returns default set of settings for seller
	 *
	 * @return array
	 */
	public function getSellerDefaults() {
		return $this->_slr_settings;
	}

	/**
	 * Get seller settings
	 *
	 * @param array $data
	 * @return array|mixed
	 */
	public function getSellerSettings($data = array()) {
		$sql = "SELECT
					name,
					value,
					is_encoded
				FROM `" . DB_PREFIX . "ms_seller_setting` mss
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['name']) ? " AND name = '" . $this->db->escape($data['name']) . "'" : '')
				. (isset($data['code']) ? " AND name LIKE '" . $this->db->escape($data['code']) . "%'" : '');

		$res = $this->db->query($sql);

		$settings = array();

		foreach ($res->rows as $result) {
			if (!$result['is_encoded']) {
				$settings[$result['name']] = $result['value'];
			} else {
				$settings[$result['name']] = json_decode($result['value'], true);
			}
			if(isset($data['single']) && $data['single']) $settings = $settings[$result['name']];
		}

		return $settings;
	}

	/**
	 * Creates or updates seller setting
	 *
	 * @param array $data
	 */
	public function createSellerSetting($data = array()) {
		foreach ($data['settings'] as $name => $value) {
			$value = is_array($value) ? json_encode($value) : $this->db->escape($value);
			$sql = "INSERT INTO " . DB_PREFIX . "ms_seller_setting
			 SET seller_id = " . (isset($data['seller_id']) ? (int)$data['seller_id'] : 'NULL') . ",
				name = '" . $this->db->escape($name) . "',
				value = '" . $value . "'
				ON DUPLICATE KEY UPDATE
				value = '" . $value . "'";
			$this->db->query($sql);
		}
	}

	/**
	 * Deletes seller setting
	 *
	 * @param array $data
	 */
	public function deleteSellerSetting($data = array()) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "ms_seller_setting
			WHERE name LIKE '" . $this->db->escape($data['code']) . "%'"
			. (isset($data['name']) ? " AND name = '" . $this->db->escape($data['name']) . "'" : '')
		);
	}


	/************************************************************/


	// Seller group related methods

	/**
	 * Returns default set of settings for seller group
	 *
	 * @return array
	 */
	public function getSellerGroupDefaults() {
		return $this->_slr_gr_settings;
	}

	/**
	 * Get seller group settings
	 *
	 * @param array $data
	 * @return array|mixed
	 */
	public function getSellerGroupSettings($data = array()) {
		$sql = "SELECT
					name,
					value,
					is_encoded
				FROM `" . DB_PREFIX . "ms_seller_group_setting` msgs
				WHERE 1 = 1 "
			. (isset($data['seller_group_id']) ? " AND seller_group_id =  " .  (int)$data['seller_group_id'] : '')
			. (isset($data['name']) ? " AND name = '" . $this->db->escape($data['name']) . "'" : '')
			. (isset($data['code']) ? " AND name LIKE '" . $this->db->escape($data['code']) . "%'" : '');

		$res = $this->db->query($sql);

		$settings = array();

		foreach ($res->rows as $result) {
			if (!$result['is_encoded']) {
				$settings[$result['name']] = $result['value'];
			} else {
				$settings[$result['name']] = json_decode($result['value'], true);
			}
			if(isset($data['single']) && $data['single']) $settings = $settings[$result['name']];
		}

		return $settings;
	}

	/**
	 * Creates or updates seller group setting
	 *
	 * @param array $data
	 */
	public function createSellerGroupSetting($data = array()) {
		foreach ($data['settings'] as $name => $value) {
			$value = is_array($value) ? json_encode($value) : $this->db->escape($value);
			$sql = "INSERT INTO " . DB_PREFIX . "ms_seller_group_setting
				SET seller_group_id = " . (isset($data['seller_group_id']) ? (int)$data['seller_group_id'] : 'NULL') . ",
					name = '" . $this->db->escape($name) . "',
					value = '" . $value . "'
					ON DUPLICATE KEY UPDATE
					value = '" . $value . "'";
			$this->db->query($sql);
		}
	}

	/**
	 * Deletes seller group setting
	 *
	 * @param array $data
	 */
	public function deleteSellerGroupSetting($data = array()) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "ms_seller_group_setting
			WHERE name LIKE '" . $this->db->escape($data['code']) . "%'"
			. (isset($data['name']) ? " AND name = '" . $this->db->escape($data['name']) . "'" : '')
		);
	}
}

?>
