<?php
class MsAttribute extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_APPROVED = 3;
	const STATUS_DISABLED = 4;


	// Attribute related methods

	/**
	 * Get attribute(s) created by seller(s)
	 *
	 * @param array $data
	 * @param array $sort
	 * @return array|mixed
	 */
	public function getAttributes($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					a.*,
					agd.name as `ag_name`,
					ad.*,
					msa.seller_id,
					msa.attribute_status,
					mss.nickname
				FROM `" . DB_PREFIX . "attribute` a
				LEFT JOIN `" . DB_PREFIX . "attribute_description` ad
					USING (attribute_id)
				LEFT JOIN `" . DB_PREFIX . "ms_attribute` msa
					USING (attribute_id)
				LEFT JOIN `" . DB_PREFIX . "attribute_group_description` agd
					USING (attribute_group_id)
				LEFT JOIN `" . DB_PREFIX . "ms_seller` mss
					ON (mss.seller_id = msa.seller_id)
				WHERE
					ad.language_id = " . (isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id'))
				. " AND agd.language_id = " . (isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id'))

				. (isset($data['attribute_id']) ? " AND a.attribute_id = " . (int)$data['attribute_id'] : "")
				. (isset($data['attribute_status']) ? " AND (msa.attribute_status IS NULL OR msa.attribute_status = " . (int)$data['attribute_status'] . ")" : "")
				. (isset($data['seller_ids']) ? " AND (msa.seller_id IN (" . $data['seller_ids'] . ")" . (in_array(0, explode(',', $data['seller_ids'])) ? " OR msa.seller_id IS NULL" : "") . ")" : "")

				. $filters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->num_rows) {
			if(isset($data['single'])) {
				$res->row['total_rows'] = $total->row['total'];
			} else {
				$res->rows[0]['total_rows'] = $total->row['total'];
			}
		}

		if(isset($data['attribute_id'])) {
			$sql = "SELECT ad.*
					FROM " . DB_PREFIX . "attribute_description ad
					WHERE ad.attribute_id = " . (int)$data['attribute_id'] . "
					GROUP BY language_id";

			$names = $this->db->query($sql);

			$attribute_description_data = array();
			foreach ($names->rows as $result) {
				$attribute_description_data[$result['language_id']] = array(
					'name' => $result['name'],
				);
			}

			$res->row['languages'] = $attribute_description_data;
		}

		return ($res->num_rows && isset($data['single'])) ? $res->row : $res->rows;
	}

	/**
	 * Creates attribute data in Multimerch table
	 *
	 * @param $attribute_id
	 * @param array $data
	 */
	public function createOrUpdateMsAttribute($attribute_id, $data = array()) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_attribute
			SET attribute_id = '" . (int)$attribute_id . "',
				attribute_status = '" . (isset($data['attribute_status']) && $data['attribute_status'] ? (int)$data['attribute_status'] : MsAttribute::STATUS_DISABLED) . "',
				seller_id = " . (isset($data['seller_id']) ? (int)$data['seller_id'] : "0") . "
			ON DUPLICATE KEY UPDATE
				attribute_id = attribute_id"
				. (isset($data['attribute_status']) && $data['attribute_status'] ? ", attribute_status = '" . (int)$data['attribute_status'] . "'" : "")
				. (isset($data['seller_id']) ? ", seller_id = " . (int)$data['seller_id'] : ""));
	}

	/**
	 * Creates attribute.
	 *
	 * @param array $data
	 * @return int $attribute_id
	 */
	public function sellerCreateAttribute($data = array()) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "attribute
			SET attribute_group_id = '" . (int)$data['attribute_group_id'] . "',
				sort_order = '" . (int)$data['sort_order'] . "'");

		$attribute_id = $this->db->getLastId();

		foreach ($data['attribute_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description
				SET attribute_id = '" . (int)$attribute_id . "',
					language_id = '" . (int)$language_id . "',
					`name` = '" . $this->db->escape($value['name']) . "'");
		}

		$this->createOrUpdateMsAttribute($attribute_id, $data);

		return $attribute_id;
	}

	/**
	 * Updates attribute
	 *
	 * @param int $attribute_id
	 * @param array $data
	 */
	public function sellerUpdateAttribute($attribute_id, $data = array()) {
		$this->db->query("UPDATE " . DB_PREFIX . "attribute
			SET attribute_id = attribute_id"
				. (isset($data['attribute_group_id']) ? ", attribute_group_id = '" . (int)$data['attribute_group_id'] . "'" : "")
				. (isset($data['sort_order']) ? ", sort_order = '" . (int)$data['sort_order'] . "'" : "") . "
			WHERE
				attribute_id = '" . (int)$attribute_id . "'");

		if(isset($data['attribute_description'])) {
			foreach ($data['attribute_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description
					SET attribute_id = '" . (int)$attribute_id . "',
						language_id = '" . (int)$language_id . "',
						`name` = '" . $this->db->escape($value['name']) . "'
					ON DUPLICATE KEY UPDATE
						`name` = '" . $this->db->escape($value['name']) . "'");
			}
		}

		$this->createOrUpdateMsAttribute($attribute_id, $data);
	}

	/**
	 * Deletes seller's attribute
	 *
	 * @param int $attribute_id
	 */
	public function deleteAttribute($attribute_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
	}

	/**
	 * Checks whether attribute is created by seller or by admin
	 *
	 * @param int $attribute_id
	 * @param array $data
	 * @return bool
	 */
	public function isMsAttribute($attribute_id, $data = array()) {
		$sql = "SELECT 1 FROM " . DB_PREFIX. "ms_attribute
				WHERE attribute_id = " . (int)$attribute_id
					. (isset($data['seller_id']) ? " AND seller_id = " . (int)$data['seller_id'] : "");

		$res = $this->db->query($sql);

		return $res->num_rows ? true : false;
	}

	/************************************************************/


	// Attribute group related methods

	/**
	 * Get attribute group(s) created by seller(s)
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function getAttributeGroups($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					ag.*,
					agd.*,
					msag.seller_id,
					msag.attribute_group_status,
					mss.nickname
				FROM `" . DB_PREFIX . "attribute_group` ag
				LEFT JOIN `" . DB_PREFIX . "attribute_group_description` agd
					USING(attribute_group_id)
				LEFT JOIN `" . DB_PREFIX . "ms_attribute_group` msag
					USING(attribute_group_id)
				LEFT JOIN `" . DB_PREFIX . "ms_seller` mss
					ON (mss.seller_id = msag.seller_id)
				WHERE
					agd.language_id = " . (isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id'))

				. (isset($data['attribute_group_id']) ? " AND ag.attribute_group_id = " . (int)$data['attribute_group_id'] : "")
				. (isset($data['attribute_group_status']) && $data['attribute_group_status'] ? " AND msag.attribute_group_status = '" . (int)$data['attribute_group_status'] . "'" : "")
				. (isset($data['seller_ids']) ? " AND msag.seller_id IN (" . $data['seller_ids'] . ")" . (in_array(0, explode(',', $data['seller_ids'])) ? " OR (msag.seller_id IS NULL AND agd.language_id = " . (isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id')) . ")" : "") : "")

				. $filters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->num_rows) {
			if(isset($data['single'])) {
				$res->row['total_rows'] = $total->row['total'];
			} else {
				$res->rows[0]['total_rows'] = $total->row['total'];
			}
		}

		if(isset($data['attribute_group_id'])) {
			$sql = "SELECT agd.*
					FROM " . DB_PREFIX . "attribute_group_description agd
					WHERE agd.attribute_group_id = " . (int)$data['attribute_group_id'] . "
					GROUP BY language_id";

			$names = $this->db->query($sql);

			$attribute_description_data = array();
			foreach ($names->rows as $result) {
				$attribute_description_data[$result['language_id']] = array(
					'name' => $result['name'],
				);
			}

			$res->row['languages'] = $attribute_description_data;
		}

		return ($res->num_rows && isset($data['single'])) ? $res->row : $res->rows;
	}

	/**
	 * Creates attribute group data in Multimerch table
	 *
	 * @param $attribute_group_id
	 * @param array $data
	 */
	public function createOrUpdateMsAttributeGroup($attribute_group_id, $data = array()) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_attribute_group
			SET attribute_group_id = '" . (int)$attribute_group_id . "',
				attribute_group_status = '" . (isset($data['attribute_group_status']) && $data['attribute_group_status'] ? (int)$data['attribute_group_status'] : MsAttribute::STATUS_DISABLED) . "',
				seller_id = " . (isset($data['seller_id']) ? (int)$data['seller_id'] : "0") . "
			ON DUPLICATE KEY UPDATE
				attribute_group_id = attribute_group_id"
			. (isset($data['attribute_group_status']) && $data['attribute_group_status'] ? ", attribute_group_status = '" . (int)$data['attribute_group_status'] . "'" : "")
			. (isset($data['seller_id']) ? ", seller_id = " . (int)$data['seller_id'] : ""));
	}

	/**
	 * Creates attribute group. This method must be used only at the frontend.
	 *
	 * @param array $data
	 * @return int $attribute_group_id
	 */
	public function sellerCreateAttributeGroup($data = array()) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group
			SET sort_order = '" . (int)$data['sort_order'] . "'"
				. (isset($data['attribute_group_id']) && $data['attribute_group_id'] ? ", attribute_group_id = " . (int)$data['attribute_group_id'] : "") . "
			ON DUPLICATE KEY UPDATE
				sort_order = '" . (int)$data['sort_order'] . "'");

		$attribute_group_id = isset($data['attribute_group_id']) && $data['attribute_group_id'] ? $data['attribute_group_id'] : $this->db->getLastId();

		foreach ($data['attribute_group_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description
				SET attribute_group_id = '" . (int)$attribute_group_id . "',
					language_id = '" . (int)$language_id . "',
					`name` = '" . $this->db->escape($value['name']) . "'
				ON DUPLICATE KEY UPDATE
					`name` = '" . $this->db->escape($value['name']) . "'");
		}

		$this->createOrUpdateMsAttributeGroup($attribute_group_id, $data);

		return $attribute_group_id;
	}

	/**
	 * Updates attribute group. This method must be used only at the frontend.
	 *
	 * @param int $attribute_group_id
	 * @param array $data
	 */
	public function sellerUpdateAttributeGroup($attribute_group_id, $data = array()) {
		$this->db->query("UPDATE " . DB_PREFIX . "attribute_group
			SET attribute_group_id = attribute_group_id"
				. (isset($data['sort_order']) ? ", sort_order = '" . (int)$data['sort_order'] . "'" : "") . "
			WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

		if(isset($data['attribute_group_description'])) {
			foreach ($data['attribute_group_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description
					SET attribute_group_id = '" . (int)$attribute_group_id . "',
						language_id = '" . (int)$language_id . "',
						`name` = '" . $this->db->escape($value['name']) . "'
					ON DUPLICATE KEY UPDATE
						`name` = '" . $this->db->escape($value['name']) . "'");
			}
		}

		$this->createOrUpdateMsAttributeGroup($attribute_group_id, $data);
	}

	/**
	 * Deletes seller's attribute group
	 *
	 * @param int $attribute_group_id
	 */
	public function deleteAttributeGroup($attribute_group_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_group WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_attribute_group WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
	}

	/**
	 * Checks whether attribute group is created by seller or by admin
	 *
	 * @param $attribute_group_id
	 * @return bool
	 */
	public function isMsAttributeGroup($attribute_group_id) {
		$sql = "SELECT 1 FROM " . DB_PREFIX. "ms_attribute_group
				WHERE attribute_group_id = " . (int)$attribute_group_id
					. (isset($data['seller_id']) ? " AND seller_id = " . (int)$data['seller_id'] : "");

		$res = $this->db->query($sql);

		return $res->num_rows ? true : false;
	}


	/************************************************************/


	// Helpers

	/**
	 * Gets product attributes. This is a copy of Opencart's getProductAttributes() method from admin/model/catalog/product.php
	 *
	 * @param $product_id
	 * @return array $product_attribute_data
	 */
	public function ocGetProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	/**
	 * Gets attribute data. This is a copy of Opencart's getAttribute() method from admin/model/catalog/attribute.php
	 *
	 * @param $attribute_id
	 * @return mixed
	 */
	public function ocGetAttribute($attribute_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE a.attribute_id = '" . (int)$attribute_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	/**
	 * Gets attributes data. This is a copy of Opencart's getAttributes() method from admin/model/catalog/attribute.php
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function ocGetAttributes($data = array()) {
		$sql = "SELECT *, (SELECT agd.name FROM " . DB_PREFIX . "attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_attribute_group_id'])) {
			$sql .= " AND a.attribute_group_id = '" . $this->db->escape($data['filter_attribute_group_id']) . "'";
		}

		$sort_data = array(
			'ad.name',
			'attribute_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY attribute_group, ad.name";
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

	/**
	 * Checks whether attribute is attached to any product. This is a copy of Opencart's getTotalProductsByAttributeId() method from admin/model/catalog/product.php
	 *
	 * @param int $attribute_id
	 * @return mixed
	 */
	public function ocGetTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	/**
	 * Checks whether attribute group is attached to any attribute. This is a copy of Opencart's getTotalProductsByAttributeId() method from admin/model/catalog/product.php
	 *
	 * @param int $attribute_group_id
	 * @return mixed
	 */
	public function ocGetTotalAttributesByAttributeGroupId($attribute_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

		return $query->row['total'];
	}
}
?>
