<?php
class MsSocialLink extends Model {
	public function createChannel($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_channel (image) VALUES('". $this->db->escape($data['image']) . "')");
		$channel_id = $this->db->getLastId();

		foreach ($data['description'] as $language_id => $value) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_channel_description
					SET channel_id = '" . (int)$channel_id . "',
						language_id = '" . (int)$language_id . "',
						name = '" . $this->db->escape($value['name']) . "',
						description = '" . $this->db->escape($value['description']) . "'";

			$this->db->query($sql);
		}
	}

	// Edit Channel
	public function editChannel($channel_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "ms_channel
				SET image = '" . $this->db->escape($data['image']) . "'
				WHERE channel_id = " . (int)$channel_id;
		$this->db->query($sql);

		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_channel_description WHERE channel_id = '" . (int)$channel_id . "'");

		foreach ($data['description'] as $language_id => $language) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_channel_description
					SET	channel_id = '" . (int)$channel_id . "',
						language_id = '" . (int)$language_id . "',
						name = '". $this->db->escape($language['name']) ."',
						description = '". $this->db->escape(htmlspecialchars(nl2br($language['description']), ENT_COMPAT)) . "'";

			$this->db->query($sql);
		}
	}

	public function getChannels($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*,
					mc.channel_id
				FROM " . DB_PREFIX . "ms_channel mc
				LEFT JOIN " . DB_PREFIX . "ms_channel_description mcd
					ON (mc.channel_id = mcd.channel_id)
				WHERE mcd.language_id = '" . (int)$this->config->get('config_language_id') . "'"
				. (isset($data['channel_id']) ? " AND mc.channel_id =  " .  (int)$data['channel_id'] : '')

				. $filters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	public function getTotalChannels() {
		$sql = "SELECT COUNT(*) as total
				FROM " . DB_PREFIX . "ms_channel mc
				WHERE 1 = 1"
				//. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['seller_group_id']) ? " AND seller_group_id =  " .  (int)$data['seller_group_id'] : '');

		$res = $this->db->query($sql);
		return $res->row['total'];
	}

	public function getChannelDescriptions($channel_id) {
		$seller_group_data = array();

		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_channel_description
				WHERE channel_id = '" . (int)$channel_id . "'";

		$res = $this->db->query($sql);

		foreach ($res->rows as $result) {
			$channel_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}

		return $channel_data;
	}

	// Delete channel
	public function deleteChannel($channel_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_channel WHERE channel_id = '" . (int)$channel_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_channel_description WHERE channel_id = '" . (int)$channel_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_channel WHERE channel_id = '" . (int)$channel_id . "'");
	}

	public function getSellerChannels($seller_id) {
		$sql = "SELECT *
				FROM `" . DB_PREFIX . "ms_seller_channel` msc
				LEFT JOIN " . DB_PREFIX . "ms_channel mc
					USING(channel_id)
				WHERE seller_id = " . (int)$seller_id;

		$res = $this->db->query($sql);

		$channels = array();
		foreach($res->rows as $row) {
			$channels[$row['channel_id']] = $row;
		}

		return $channels;
	}

	public function editSellerChannels($seller_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_channel WHERE seller_id = " . (int)$seller_id);

		if (isset($data['social_links'])) {
			foreach ($data['social_links'] as $channel_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ms_seller_channel (seller_id, channel_id, channel_value) VALUES(".(int)$seller_id.",".(int)$channel_id.",'".$this->db->escape($value)."')");
			}
		}
	}
}
?>