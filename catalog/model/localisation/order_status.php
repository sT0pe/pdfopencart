<?php 
class ModelLocalisationOrderStatus extends Model {
	public function getOrderStatuses($data = array()) {
		$language_id = isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id');

		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = $language_id";

			$sql .= " ORDER BY name";	

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
		} else {
			$order_status_data = $this->cache->get('order_status.' . $language_id);

			if (!$order_status_data) {
				$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = $language_id ORDER BY name");

				$order_status_data = $query->rows;

				$this->cache->set('order_status.' . $language_id, $order_status_data);
			}	

			return $order_status_data;				
		}
	}

	public function getOrderStatusDescriptions($order_status_id) {
		$order_status_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "'");

		foreach ($query->rows as $result) {
			$order_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $order_status_data;
	}
}
?>