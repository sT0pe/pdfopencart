<?php
class MsOption extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_APPROVED = 3;
	const STATUS_DISABLED = 4;


	// Options

	/**
	 * Gets option(s)
	 *
	 * @param array $data
	 * @param array $sort
	 * @return array|mixed
	 */
	public function getOptions($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					o.*,
					od.*,
					mso.seller_id,
					mso.option_status,
					mss.nickname
				FROM `" . DB_PREFIX . "option` o
				LEFT JOIN `" . DB_PREFIX . "option_description` od
					USING (option_id)
				LEFT JOIN `" . DB_PREFIX . "ms_option` mso
					USING (option_id)
				LEFT JOIN `" . DB_PREFIX . "ms_seller` mss
					ON (mss.seller_id = mso.seller_id)
				WHERE 1 = 1"
				. (isset($data['option_id']) ? " AND o.option_id =  " .  (int)$data['option_id'] : '')
				. (isset($data['language_id']) ? " AND od.language_id =  " .  (int)$data['language_id'] : " AND od.language_id =  " .  (int)$this->config->get('config_language_id'))
				. (isset($data['option_status']) ? " AND (mso.option_status IS NULL OR mso.option_status = " . (int)$data['option_status'] . ")" : "")
				. (isset($data['seller_ids']) ? " AND (mso.seller_id IN (" . $data['seller_ids'] . ")" . (in_array(0, explode(',', $data['seller_ids'])) ? " OR mso.seller_id IS NULL" : "") . ")" : "")

				. $filters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		if(isset($data['option_id'])) {
			$sql = "SELECT od.*
					FROM " . DB_PREFIX . "option_description od
					WHERE od.option_id = " . (int)$data['option_id'] . "
					GROUP BY language_id";

			$names = $this->db->query($sql);

			$option_description_data = array();
			foreach ($names->rows as $result) {
				$option_description_data[$result['language_id']] = array(
					'name' => $result['name'],
				);
			}

			$res->row['languages'] = $option_description_data;
		}

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	/**
	 * Creates or updates option data in Multimerch table
	 *
	 * @param $option_id
	 * @param array $data
	 */
	public function createOrUpdateMsOption($option_id, $data = array()) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_option
			SET option_id = '" . (int)$option_id . "',
				option_status = '" . (isset($data['option_status']) && $data['option_status'] ? (int)$data['option_status'] : MsOption::STATUS_DISABLED) . "',
				seller_id = " . (isset($data['seller_id']) ? (int)$data['seller_id'] : "0") . "
			ON DUPLICATE KEY UPDATE
				option_id = option_id"
			. (isset($data['option_status']) && $data['option_status'] ? ", option_status = '" . (int)$data['option_status'] . "'" : "")
			. (isset($data['seller_id']) ? ", seller_id = " . (int)$data['seller_id'] : ""));
	}

	/**
	 * Creates seller's option. This method is a copy of Opencart's addOption() with addition with MsOption creation.
	 *
	 * @param array $data
	 * @return int $option_id
	 */
	public function sellerCreateOption($data = array()) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "option`
			SET type = '" . $this->db->escape($data['type']) . "',
				sort_order = '" . (int)$data['sort_order'] . "'");

		$option_id = $this->db->getLastId();

		foreach ($data['option_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "option_description
				SET option_id = '" . (int)$option_id . "',
					language_id = '" . (int)$language_id . "',
					name = '" . $this->db->escape($value['name']) . "'");
		}

		if (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "option_value
					SET option_id = '" . (int)$option_id . "',
						image = '" . (isset($option_value['image']) ? $this->db->escape(html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8')) : "") . "',
						sort_order = '" . (int)$option_value['sort_order'] . "'");

				$option_value_id = $this->db->getLastId();

				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description
						SET option_value_id = '" . (int)$option_value_id . "',
							language_id = '" . (int)$language_id . "',
							option_id = '" . (int)$option_id . "',
							name = '" . $this->db->escape($option_value_description['name']) . "'");
				}
			}
		}

		$this->createOrUpdateMsOption($option_id, $data);

		return $option_id;
	}

	/**
	 * Creates seller's option. This method is a copy of Opencart's editOption() with addition with MsOption update.
	 *
	 * @param int $option_id
	 * @param array $data
	 */
	public function sellerUpdateOption($option_id, $data = array()) {
		$this->db->query("UPDATE `" . DB_PREFIX . "option`
			SET type = '" . $this->db->escape($data['type']) . "',
				sort_order = '" . (int)$data['sort_order'] . "'
			WHERE option_id = '" . (int)$option_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "option_description
			WHERE option_id = '" . (int)$option_id . "'");

		foreach ($data['option_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "option_description
				SET option_id = '" . (int)$option_id . "',
					language_id = '" . (int)$language_id . "',
					name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int)$option_id . "'");

		if (isset($data['option_value'])) {
			foreach ($data['option_value'] as $option_value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "option_value
					SET option_id = '" . (int)$option_id . "',
						image = '" . (isset($option_value['image']) ? $this->db->escape(html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8')) : "") . "',
						sort_order = '" . (int)$option_value['sort_order'] . "'");

				$option_value_id = $this->db->getLastId();

				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description
						SET option_value_id = '" . (int)$option_value_id . "',
							language_id = '" . (int)$language_id . "',
							option_id = '" . (int)$option_id . "',
							name = '" . $this->db->escape($option_value_description['name']) . "'");
				}
			}
		}

		$this->createOrUpdateMsOption($option_id, $data);
	}

	/**
	 * Deletes option. This method is a copy of Opencart's deleteOption().
	 *
	 * @param int $option_id
	 */
	public function deleteOption($option_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "option` WHERE option_id = '" . (int)$option_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$option_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "option_value WHERE option_id = '" . (int)$option_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int)$option_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_option WHERE option_id = '" . (int)$option_id . "'");
	}

	/**
	 * Checks whether option is created by seller or by admin
	 *
	 * @param int $option_id
	 * @param array $data
	 * @return bool
	 */
	public function isMsOption($option_id, $data = array()) {
		$sql = "SELECT 1 FROM " . DB_PREFIX. "ms_option
				WHERE option_id = " . (int)$option_id
					. (isset($data['seller_id']) ? " AND seller_id = " . (int)$data['seller_id'] : "");

		$res = $this->db->query($sql);

		return $res->num_rows ? true : false;
	}

	public function sellerActivateOption($option_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "ms_option`
			SET option_status = " . (int)MsOption::STATUS_ACTIVE . "
			WHERE option_id = " . (int)$option_id);
	}

	public function sellerDeactivateOption($option_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "ms_option`
			SET option_status = " . (int)MsOption::STATUS_INACTIVE . "
			WHERE option_id = " . (int)$option_id);
	}

	/************************************************************/


	// Option values

	public function getOptionValues($option_id) {
		$option_value_data = array();
		$sql = "SELECT *
			FROM " . DB_PREFIX . "option_value ov
			LEFT JOIN " . DB_PREFIX . "option_value_description ovd
				ON (ov.option_value_id = ovd.option_value_id)
			WHERE ov.option_id = " . (int)$option_id
				. (isset($data['language_id']) ? " AND ovd.language_id =  " .  (int)$data['language_id'] : " AND ovd.language_id =  " .  (int)$this->config->get('config_language_id')) .  "
			ORDER BY ov.sort_order ASC";

		$option_value_query = $this->db->query($sql);
	
		foreach ($option_value_query->rows as $option_value) {
			$option_value_description_data = array();
			$option_value_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int)$option_value['option_value_id'] . "'");
			foreach ($option_value_description_query->rows as $option_value_description) {
				$option_value_description_data[$option_value_description['language_id']] = array('name' => $option_value_description['name']);
			}

			// @todo rebuild array, delete 'name' everywhere
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'option_value_description' => $option_value_description_data,
				'name'            => $option_value['name'],
				'image'           => $option_value['image'],
				'sort_order'      => $option_value['sort_order']
			);
		}
	
		return $option_value_data;
	}

	public function ocGetTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}
}
?>