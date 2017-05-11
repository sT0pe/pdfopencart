<?php
class MsCategory extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_APPROVED = 3;
	const STATUS_DISABLED = 4;


	/**
	 * Gets category(ies) created by seller(s)
	 *
	 * @param array $data
	 * @param array $sort
	 * @return array|mixed
	 */
	public function getCategories($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					msc.*,
					mscd.*"

				. (isset($data['category_id']) ?
					", (SELECT DISTINCT keyword
						FROM " . DB_PREFIX . "url_alias
						WHERE `query` = 'ms_category_id=" . (int)$data['category_id'] . "'
					) AS keyword"
				: "")

				. ", (SELECT GROUP_CONCAT(mscd1.name ORDER BY `level` SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;')
					FROM `" . DB_PREFIX . "ms_category_path` mscp
					LEFT JOIN `" . DB_PREFIX . "ms_category_description` mscd1
						ON (mscp.path_id = mscd1.category_id AND mscp.category_id != mscp.path_id)
					WHERE
						mscp.category_id = msc.category_id
						AND mscd1.language_id = '" . (int)$this->config->get('config_language_id') . "'
					GROUP BY mscp.category_id) AS path,
					mss.nickname
				FROM `" . DB_PREFIX . "ms_category` msc
				LEFT JOIN `" . DB_PREFIX . "ms_category_description` mscd
					USING (category_id)
				LEFT JOIN `" . DB_PREFIX . "ms_seller` mss
					ON (mss.seller_id = msc.seller_id)
				WHERE mscd.language_id = '" . (int)$this->config->get('config_language_id') . "'"

				. (isset($data['category_id']) ? " AND msc.category_id = '" . (int)$data['category_id'] . "'" : "")
				. (isset($data['parent_id']) ? " AND msc.parent_id = '" . (int)$data['parent_id'] . "'" : "")
				. (isset($data['category_status']) ? " AND msc.category_status = '" . (int)$data['category_status'] . "'" : "")
				. (isset($data['seller_ids']) ? " AND msc.seller_id IN (" . $data['seller_ids'] . ")" : "")

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

		if(isset($data['category_id'])) {
			$res->row['languages'] = $this->_getDescriptions($data['category_id']);
			$res->row['filters'] = $this->_getFilters($data['category_id']);
			$res->row['stores'] = $this->_getStores($data['category_id']);
		}

		return ($res->num_rows && isset($data['single'])) ? $res->row : $res->rows;
	}

	/**
	 * Creates seller's category
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function createCategory($data = array()) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category`
				SET parent_id = '" . (int)$data['parent_id'] . "',
					seller_id = '" . (int)$data['seller_id'] . "',
					sort_order = '" . (int)$data['sort_order'] . "',
					category_status = '" . (int)$data['status'] . "'"
				. (isset($data['image']) ? ", `image` = '" . $this->db->escape($data['image']) . "'" : ""));

		$category_id = $this->db->getLastId();

		// descriptions
		if (isset($data['category_description'])) $this->_saveDescriptions($category_id, $data['category_description']);

		// filters
		if (isset($data['category_filter'])) $this->_saveFilters($category_id, $data['category_filter']);

		// category to store
		if (isset($data['category_store'])) $this->_saveStores($category_id, $data['category_store']);

		// seo keyword
		if (!empty($data['keyword'])) $this->_saveKeyword($category_id, $data['keyword']);

		// category path
		if(isset($data['parent_id'])) $this->_savePath($category_id, $data);

		return $category_id;
	}

	/**
	 * Updates seller's category
	 *
	 * @param $category_id
	 * @param array $data
	 */
	public function updateCategory($category_id, $data = array()) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_description WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_filter WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_to_store WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'ms_category_id=" . (int)$category_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "ms_category
			SET parent_id = '" . (int)$data['parent_id'] . "',
				seller_id = '" . (int)$data['seller_id'] . "',
				sort_order = '" . (int)$data['sort_order'] . "',
				category_status = '" . (int)$data['status'] . "'"
			. (isset($data['image']) ? ", `image` = '" . $this->db->escape($data['image']) . "'" : "")

			. " WHERE category_id = '" . (int)$category_id . "'");

		// descriptions
		if (isset($data['category_description'])) $this->_saveDescriptions($category_id, $data['category_description']);

		// filters
		if (isset($data['category_filter'])) $this->_saveFilters($category_id, $data['category_filter']);

		// category to store
		if (isset($data['category_store'])) $this->_saveStores($category_id, $data['category_store']);

		// seo keyword
		if (!empty($data['keyword'])) $this->_saveKeyword($category_id, $data['keyword']);

		// category path
		if(isset($data['parent_id'])) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_category_path`WHERE path_id = '" . (int)$category_id . "' ORDER BY level ASC");

			if ($query->rows) {
				foreach ($query->rows as $category_path) {
					// Delete the path below the current one
					$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' AND level < '" . (int)$category_path['level'] . "'");

					$path = array();

					// Get the nodes new parents
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

					foreach ($query->rows as $result) {
						$path[] = $result['path_id'];
					}

					// Get whats left of the nodes current path
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' ORDER BY level ASC");

					foreach ($query->rows as $result) {
						$path[] = $result['path_id'];
					}

					// Combine the paths with a new level
					$level = 0;

					foreach ($path as $path_id) {
						$this->db->query("REPLACE INTO `" . DB_PREFIX . "ms_category_path` SET category_id = '" . (int)$category_path['category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

						$level++;
					}
				}
			} else {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_category_path` WHERE category_id = '" . (int)$category_id . "'");

				// Fix for records with no paths
				$this->_savePath($category_id, $data);
			}
		}
	}

	/**
	 * Deletes seller's category
	 *
	 * @param $category_id
	 */
	public function deleteCategory($category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_description WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_filter WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_to_store WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'ms_category_id=" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_path WHERE category_id = '" . (int)$category_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_category_path WHERE path_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteCategory($result['category_id']);
		}
	}

	/**
	 * Gets commission of Opencart's category
	 *
	 * @param string $categories_id
	 * @param int $type
	 * @param array $data
	 * @return array|bool
	 */
	public function getOcCategoryCommission($categories_id, $type = 0, $data = array()) {
		if(!$categories_id) return false;

		$res = $this->db->query("SELECT commission_id FROM " . DB_PREFIX . "ms_category_commission WHERE category_id IN (" . $categories_id . ")");

		if(!$res->num_rows) return false;

		/**	@var array $temp_rates - Temporary array for commission rates. Used for finding the most appropriate commission in multiple categories case */
		$temp_rates = array();

		/**	@var int $commission_id - Id of the selected commission */
		$commission_id = 0;

		// Convert to int just in case
		$type = (int)$type;

		foreach ($res->rows as $row) {
			if(!isset($row['commission_id'])) continue;

			$commission_id = $row['commission_id'];

			$rates[$commission_id] = $this->MsLoader->MsCommission->getCommissionRates($commission_id);
			$rates[$commission_id]['commission_id'] = $commission_id;

			if(isset($data['price']) && isset($rates[$commission_id][$type])) {
				$commission_fee = (float)$rates[$commission_id][$type]['flat'] + ((float)$rates[$commission_id][$type]['percent'] * (float)$data['price'] / 100);

				$rates[$commission_id][$type]['calculated_fee'] = $temp_rates[$commission_id] = $commission_fee;
			}
		}

		// Find commission_id with max rates based on product price
		if(!empty($temp_rates)) {
			arsort($temp_rates);
			$commission_id = key($temp_rates);
		}

		return isset($rates[$commission_id]) ? $rates[$commission_id] : false;
	}

	/**
	 * Saves commission settings for Opencart's category
	 *
	 * @param $category_id
	 * @param $commission_id
	 */
	public function saveCategoryCommission($category_id, $commission_id) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_category_commission
             	SET category_id = " . (int)$category_id . ",
					commission_id = " . (is_null($commission_id) ? 'NULL' : (int)$commission_id) . "
                ON DUPLICATE KEY UPDATE
                	commission_id = " . (is_null($commission_id) ? 'NULL' : (int)$commission_id);

		$this->db->query($sql);
	}


	// Helpers

	/**
	 * Checks the legitimacy of the category
	 *
	 * @param $category_id
	 * @return bool
	 */
	public function isMsCategory($category_id) {
		$sql = "SELECT 1 FROM " . DB_PREFIX. "ms_category
				WHERE category_id = " . (int)$category_id
			. (isset($data['seller_id']) ? " AND seller_id = " . (int)$data['seller_id'] : "");

		$res = $this->db->query($sql);

		return $res->num_rows ? true : false;
	}

	/**
	 * Changes category to seller relation
	 *
	 * @param $category_id
	 * @param $seller_id
	 */
	public function changeSeller($category_id, $seller_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "ms_category`
			SET `seller_id` = '" . (int)$seller_id . "'
			WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Changes category status
	 *
	 * @param $category_id
	 * @param $status_id
	 */
	public function changeStatus($category_id, $status_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "ms_category`
			SET `category_status` = '" . (int)$status_id . "'
			WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Gets name, description, meta_title, meta_description and meta_keyword of MsCategory
	 *
	 * @param $category_id
	 * @return array
	 */
	private function _getDescriptions($category_id) {
		$category_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_category_description WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $category_description_data;
	}

	/**
	 * Gets filters of MsCategory
	 *
	 * @param $category_id
	 * @return array
	 */
	private function _getFilters($category_id) {
		$category_filter_data = array();

		$query = $this->db->query("SELECT
				mscf.oc_filter_id,
				fd.name,
				(SELECT `name` FROM `" . DB_PREFIX . "filter_group_description` fgd WHERE f.filter_group_id = fgd.filter_group_id AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS `group`
			FROM `" . DB_PREFIX . "ms_category_filter` mscf
			LEFT JOIN `" . DB_PREFIX . "filter` f
				ON (mscf.oc_filter_id = f.filter_id)
			LEFT JOIN `" . DB_PREFIX . "filter_description` fd
				ON (mscf.oc_filter_id = fd.filter_id)
			WHERE mscf.category_id = '" . (int)$category_id . "'
				AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $result) {
			$category_filter_data[] = array(
				'filter_id' => $result['oc_filter_id'],
				'name' => strip_tags(html_entity_decode($result['group'] . ' &gt; ' . $result['name'], ENT_QUOTES, 'UTF-8'))
			);
		}

		return $category_filter_data;
	}

	/**
	 * Gets MsCategory to stores relation
	 *
	 * @param $category_id
	 * @return array
	 */
	private function _getStores($category_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_category_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}

	/**
	 * Saves category descriptions
	 *
	 * @param $category_id
	 * @param array $descriptions
	 */
	private function _saveDescriptions($category_id, $descriptions = array()) {
		foreach ($descriptions as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_description`
				SET category_id = '" . (int)$category_id . "',
					language_id = '" . (int)$language_id . "',
					`name` = '" . $this->db->escape($value['name']) . "',
					`description` = '" . $this->db->escape($value['description']) . "'"
				. (isset($value['meta_title']) ? ", meta_title = '" . $this->db->escape($value['meta_title']) . "'" : "")
				. (isset($value['meta_description']) ? ", meta_description = '" . $this->db->escape($value['meta_description']) . "'" : "")
				. (isset($value['meta_keyword']) ? ", meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'" : ""));
		}
	}

	/**
	 * Saves category filters
	 *
	 * @param $category_id
	 * @param array $filters
	 */
	private function _saveFilters($category_id, $filters = array()) {
		foreach ($filters as $filter_id) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_filter`
				SET category_id = '" . (int)$category_id . "',
					oc_filter_id = '" . (int)$filter_id . "'");
		}
	}

	/**
	 * Saves category to store relation
	 *
	 * @param $category_id
	 * @param array $stores
	 */
	private function _saveStores($category_id, $stores = array()) {
		foreach ($stores as $store_id) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_to_store`
				SET category_id = '" . (int)$category_id . "',
					store_id = '" . (int)$store_id . "'");
		}
	}

	/**
	 * Saves seo url keyword for category
	 *
	 * @param $category_id
	 * @param $keyword
	 */
	private function _saveKeyword($category_id, $keyword) {
		$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($keyword) . "%'");
		$number = $similarity_query->num_rows;

		if ($number > 0) {
			$keyword = $keyword . "-" . $number;
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'ms_category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($keyword) . "'");
	}

	/**
	 * Saves categories hierarchy. Uses MySQL Hierarchical Data Closure Table Pattern.
	 *
	 * @param $category_id
	 * @param array $data
	 * @return int $level
	 */
	private function _savePath($category_id, $data = array()) {
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_category_path` WHERE `category_id` = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_path`
			SET `category_id` = '" . (int)$category_id . "',
				`path_id` = '" . (int)$category_id . "',
				`level` = '" . (int)$level . "'
			ON DUPLICATE KEY UPDATE
				`level` = '" . (int)$level . "'");
	}

	/**
	 * Gets full MsCategory hierarchy
	 *
	 * @param $category_id
	 * @return string Comma separated categories ids
	 */
	public function getMsCategoryPath($category_id) {
		$sql = "SELECT GROUP_CONCAT(mscp.path_id ORDER BY `level` SEPARATOR ',') as category_path
			FROM `" . DB_PREFIX . "ms_category_path` mscp
			WHERE mscp.category_id = '" . (int)$category_id . "'
			GROUP BY mscp.category_id";

		$res = $this->db->query($sql);

		return $res->num_rows ? $res->row['category_path'] : '';
	}

	/**
	 * Gets full OcCategory hierarchy
	 *
	 * @param $category_id
	 * @return string Comma separated categories ids
	 */
	public function getOcCategoryPath($category_id) {
		$sql = "SELECT GROUP_CONCAT(cp.path_id ORDER BY `level` SEPARATOR ',') as category_path
			FROM `" . DB_PREFIX . "category_path` cp
			WHERE cp.category_id = '" . (int)$category_id . "'
			GROUP BY cp.category_id";

		$res = $this->db->query($sql);

		return $res->num_rows ? $res->row['category_path'] : '';
	}


}