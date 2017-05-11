<?php
class MsMessage extends Model {
	public function createMessage($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_message`
				SET conversation_id = " . (isset($data['conversation_id']) ? (int)$data['conversation_id'] : 'NULL') . ",
					`from` = " . (isset($data['from']) ? (int)$data['from'] : 'NULL') . ",
					`from_admin` = " . (isset($data['from_admin']) ? 1 : 0) . ",
					message = '" . (isset($data['message']) ? $this->db->escape($data['message']) : '') . "',
					date_created = NOW()";

		$this->db->query($sql);
		return $this->db->getLastId();
	}
	
	public function getMessages($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
			SQL_CALC_FOUND_ROWS
			message_id,
			conversation_id,
			message,
			from_admin,
			`from`,
			(SELECT CONCAT(c.firstname, ' ', c.lastname) FROM `" . DB_PREFIX . "customer` c WHERE customer_id = `from`) as customer_sender,
			(SELECT IFNULL(mss.nickname, '') FROM `" . DB_PREFIX . "ms_seller` mss WHERE seller_id = `from`) as seller_sender,
			(SELECT CONCAT(c.firstname, ' ', c.lastname) FROM `" . DB_PREFIX . "user` c WHERE user_id = `from`) as user_sender,
			date_created
			FROM `" . DB_PREFIX . "ms_message` mmesg
			WHERE 1 = 1 "
			. (isset($data['conversation_id']) ? " AND conversation_id =  " .  (int)$data['conversation_id'] : '')
			. (isset($data['from']) ? " AND `from` =  " .  (int)$data['from'] : '')
			. (isset($data['message_id']) ? " AND message_id =  " .  (int)$data['message_id'] : '')

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) {
			$res->rows[0]['total_rows'] = $total->row['total'];

			foreach ($res->rows as &$row) {
				$row['attachments'] = $this->getMessageAttachments($row['message_id']);
			}
		}

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
	
	public function deleteMessage($message_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_message` WHERE message_id = " . (int)$message_id);
	}

	public function createMessageAttachment($message_id, $upload_id) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_message_upload`
			SET message_id = '" . (int)$message_id . "',
				upload_id = '" . (int)$upload_id . "'"
		);

		return $this->db->getLastId();
	}

	public function getMessageAttachments($message_id) {
		$attachments = array();
		$res = $this->db->query("SELECT upload_id FROM `" . DB_PREFIX . "ms_message_upload` WHERE message_id = '" . (int)$message_id . "'");

		if($res->num_rows) {
			foreach ($res->rows as $row) {
				$upload = $this->db->query("SELECT name, code FROM `" . DB_PREFIX . "upload` WHERE upload_id = '" . (int)$row['upload_id'] . "'");

				$attachments[] = array(
					'name' => $upload->row['name'],
					'code' => $upload->row['code']
				);
			}
		}

		return $attachments;
	}
}
?>