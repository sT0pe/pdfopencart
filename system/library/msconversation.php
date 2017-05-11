<?php
class MsConversation extends Model {
	const SENDER_TYPE_CUSTOMER = 1;
	const SENDER_TYPE_SELLER = 2;
	const SENDER_TYPE_ADMIN = 3;

	public function createConversation($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_conversation` SET
				title = '" . (isset($data['title']) ? $this->db->escape($data['title']) : '') . "',
				conversation_from = " . (isset($data['conversation_from']) && $data['conversation_from'] ? (int)$data['conversation_from'] : 'NULL') . ",
				date_created = NOW()";
		$this->db->query($sql);
		$conversation_id = $this->db->getLastId();
		if (isset($data['order_id']) AND isset($data['suborder_id'])){
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_conversation_to_order SET
				order_id = '" . (int)$data['order_id'] . "',
				suborder_id = '" . (int)$data['suborder_id'] . "',
				conversation_id = '" . (int)$conversation_id . "'
			");
		}
		if (isset($data['product_id'])){
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_conversation_to_product SET
				product_id = '" . (int)$data['product_id'] . "',
				conversation_id = '" . (int)$conversation_id . "'
			");
		}
		return $conversation_id;
	}

	//don't use
	public function updateConversation($conversation_id, $data) {
		$sql = "UPDATE `" . DB_PREFIX . "ms_conversation`
				SET conversation_id = conversation_id"
					. (isset($data['title']) ? ", title = " . $this->db->escape($data['title']) : '')
					. (isset($data['product_id']) ? ", product_id = " . (int)$data['product_id'] : '')
					. (isset($data['order_id']) ? ", order_id = " . (int)$data['order_id'] : '') . "
				WHERE conversation_id = " . (int)$conversation_id;

		return $this->db->query($sql);
	}

	public function addConversationParticipants($conversation_id, $participant_ids, $admin = false) {
		if (is_array($participant_ids) AND $participant_ids){
			foreach ($participant_ids as $participant_id){
				if ($admin){
					$this->db->query("
				INSERT IGNORE INTO " . DB_PREFIX . "ms_conversation_participants SET
				conversation_id = '" . (int)$conversation_id . "',
				user_id = '" . (int)$participant_id . "'
				");
				}else{
					$this->db->query("
				INSERT IGNORE INTO " . DB_PREFIX . "ms_conversation_participants SET
				conversation_id = '" . (int)$conversation_id . "',
				customer_id = '" . (int)$participant_id . "'
				");
				}
			}
		}
	}

	public function getConversationParticipants($conversation_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_conversation_participants`
		WHERE conversation_id = " . (int)$conversation_id . "
		");
		return $query->rows;
	}

	public function getConversationParticipantsIds($conversation_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_conversation_participants`
		WHERE conversation_id = " . (int)$conversation_id . "
		");
		$customer_ids = array();
		foreach($query->rows as $row){
			if ($row["customer_id"]){
				$customer_ids[] = $row["customer_id"];
			}
		}
		return $customer_ids;
	}

	public function sendMailForParticipants($conversation_id, $message, $from_admin = false){
		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();
		$conversation_participants = $this->MsLoader->MsConversation->getConversationParticipants($conversation_id);

		if ($from_admin){
			$customer_id = 0;
			$customer_name = $this->user->getUserName();
			$title = sprintf($this->language->get('ms_conversation_title_order'), $customer_name);
		}else{
			$customer_id = $this->customer->getId();
			$customer_name = $this->customer->getFirstname() . ' ' . $this->customer->getLastname();
			$title = sprintf($this->language->get('ms_conversation_title_order'), $customer_name);
		}

		foreach ($conversation_participants as $conversation_participant){
			if ($conversation_participant['customer_id'] AND $conversation_participant['customer_id'] != $customer_id){
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$conversation_participant['customer_id'] . "'");
				$customer = $query->row;
				$addressee_name = $customer['firstname'] . ' ' . $customer['lastname'];
				$MailSellerPrivateMessage = $serviceLocator->get('MailSellerPrivateMessage', false)
					->setTo($customer['email'])
					->setData(array(
						'customer_name' => $customer_name,
						'customer_message' => $message,
						'title' => $title,
						'addressee' =>$addressee_name
					));
				$mails->add($MailSellerPrivateMessage);
			}
		}
		$mailTransport->sendMails($mails);
	}

	public function getOrderConversation($order_id, $suborder_id){
		$query = $this->db->query("SELECT cto.*,c.title FROM " . DB_PREFIX . "ms_conversation_to_order cto
		LEFT JOIN " . DB_PREFIX . "ms_conversation c USING (conversation_id) WHERE
		cto.order_id = " . (int)$order_id . " AND
		cto.suborder_id = " . (int)$suborder_id);
		return $query->row;
	}
	
	public function getConversations($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("last_message_date" => 1));
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
			conversation_id,
			title,
			mconv.date_created,
			conversation_from,
			mconvo.order_id,
			mconvp.product_id,
			ms.nickname as conversation_from_nickname,
			(SELECT date_created FROM `" . DB_PREFIX . "ms_message` WHERE conversation_id = mconv.conversation_id ORDER BY message_id DESC LIMIT 1) as last_message_date
			FROM `" . DB_PREFIX . "ms_conversation` mconv
			LEFT JOIN " . DB_PREFIX . "ms_conversation_to_order mconvo USING(conversation_id)
			LEFT JOIN " . DB_PREFIX . "ms_conversation_to_product mconvp USING(conversation_id)
			LEFT JOIN " . DB_PREFIX . "ms_seller ms ON(ms.seller_id = conversation_from)
			WHERE 1 = 1 "
			. (isset($data['conversation_id']) ? " AND conversation_id =  " . (int)$data['conversation_id'] : '')
			. (isset($data['participant_id']) ? " AND conversation_id IN (SELECT conversation_id FROM `" . DB_PREFIX . "ms_conversation_participants` WHERE `customer_id` = " .  (int)$data['participant_id'] . ")" : '')
			. $wFilters
			
			. " GROUP BY mconv.conversation_id HAVING 1 = 1 "
			
			. $hFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
	
	public function getWith($conversation_id, $data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "ms_message`
		WHERE conversation_id = " . (int)$conversation_id . "
		ORDER BY message_id DESC LIMIT 1";
		
		$res = $this->db->query($sql);
		
		if (!$res->num_rows) return false;
		
		if ($res->rows[0]['from'] == $data['participant_id'])
			return $res->rows[0]['to'];
		else
			return $res->rows[0]['from'];
	}
	
	public function isParticipant($conversation_id, $data = array()) {
		if (!isset($data['participant_id'])){
			return false;
		}

		$query = $this->db->query("
		SELECT * FROM `" . DB_PREFIX . "ms_conversation_participants`
		WHERE conversation_id = " . (int)$conversation_id . "
		");

		$participants = array();
		foreach ($query->rows as $participant){
			if($participant['customer_id']){
				$participants[] = $participant['customer_id'];
			}
			if($participant['user_id']){
				$participants[] = $participant['user_id'];
			}
		}

		if (in_array($data['participant_id'], $participants)){
			return true;
		}else{
			return false;
		}
	}

	public function deleteConversation($conversation_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_conversation` WHERE conversation_id = " . (int)$conversation_id);

		$messages = $this->db->query("SELECT message_id FROM `" . DB_PREFIX . "ms_message` WHERE conversation_id = " . (int)$conversation_id);
		if($messages->num_rows) {
			foreach ($messages->rows as $message_id) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_message_upload` WHERE message_id = " . (int)$message_id);
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_message` WHERE conversation_id = " . (int)$conversation_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_conversation_participants` WHERE conversation_id = " . (int)$conversation_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_conversation_to_product` WHERE conversation_id = " . (int)$conversation_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_conversation_to_order` WHERE conversation_id = " . (int)$conversation_id);
		return true;
	}
}
?>