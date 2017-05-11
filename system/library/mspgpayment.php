<?php

class MsPgPayment extends Model {
	// Admin's id in database
	const ADMIN_ID = 0;

	// Pay fee using '[MM] Payment Gateways' method or by deducting amount from seller's balance
	const METHOD_PG = 1;
	const METHOD_BALANCE = 2;

	const TYPE_PAID_REQUESTS = 1;
	const TYPE_SALE = 2;

	const STATUS_INCOMPLETE = 1;
	const STATUS_COMPLETE = 2;
	const STATUS_WAITING_CONFIRMATION = 3;

	const ADMIN_SETTING_PREFIX = 'ms_pg_';
	const SELLER_SETTING_PREFIX = 'slr_pg_';

	/**
	 * Create payment record. Payment record assigned to one or many payment requests means request is paid or refunded.
	 *
	 * @param mixed $data
	 * @return int $payment_id
	 */
	public function createPayment($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_pg_payment`
				SET seller_id = " . (int)$data['seller_id'] . ",
					payment_type = '" . (int)$data['payment_type'] . "',
					payment_code = '" . $this->db->escape($data['payment_code']) . "',
					payment_status = '" . (isset($data['payment_status']) ? (int)$data['payment_status'] : MsPgPayment::STATUS_COMPLETE) . "',
					amount = ". (float)$this->currency->format($data['amount'], $this->config->get('config_currency'), '', FALSE) . ",
					currency_id = " . (int)$data['currency_id'] . ",
					currency_code = '" . $this->db->escape($data['currency_code']) . "',
					sender_data = '" . (is_array($data['sender_data']) ? $this->db->escape(json_encode($data['sender_data'])) : $this->db->escape($data['sender_data'])) . "',
					receiver_data = '" . (is_array($data['receiver_data']) ? $this->db->escape(json_encode($data['receiver_data'])) : $this->db->escape($data['receiver_data'])) . "',
					description = '" . (is_array($data['description']) ? $this->db->escape(json_encode($data['description'])) : $this->db->escape($data['description'])) . "',
					date_created = NOW()";

		$this->db->query($sql);
		return $this->db->getLastId();
	}

	/**
	 * Update payment record.
	 *
	 * @param int $payment_id
	 * @param mixed $data
	 */
	public function updatePayment($payment_id, $data) {
		$sql = "UPDATE `" . DB_PREFIX . "ms_pg_payment`
				SET payment_id = payment_id"
			. (isset($data['payment_type']) ? ", payment_type = " . (int)$data['payment_type'] : "")
			. (isset($data['payment_code']) ? ", payment_code = " . $this->db->escape($data['payment_code']) : "")
			. (isset($data['payment_status']) ? ", payment_status = " . (int)$data['payment_status'] : "")
			. (isset($data['amount']) ? ", amount = " . (float)$this->currency->format($data['amount'], $this->config->get('config_currency'), '', FALSE) : "")
			. (isset($data['currency_id']) ? ", currency_id = " . (int)$data['currency_id'] : "")
			. (isset($data['currency_code']) ? ", currency_code = " . $this->db->escape($data['currency_code']) : "")
			. (isset($data['sender_data']) ? ", sender_data = " . (is_array($data['sender_data']) ? $this->db->escape(json_encode($data['sender_data'])) : $this->db->escape($data['sender_data'])) :  "")
			. (isset($data['receiver_data']) ?", receiver_data = " . (is_array($data['receiver_data']) ? $this->db->escape(json_encode($data['receiver_data'])) : $this->db->escape($data['receiver_data'])) :  "")
			. (isset($data['date_created']) ? ", date_created = NOW()" : '') . "
				WHERE payment_id = " . (int)$payment_id;

		return $this->db->query($sql);
	}

	/**
	 * Delete payment request.
	 *
	 * @param int $payment_id
	 * @return void
	 */
	public function deletePayment($payment_id) {
		$sql = "DELETE FROM `" . DB_PREFIX . "ms_pg_payment`
				WHERE payment_id = " . (int)$payment_id;

		$this->db->query($sql);
	}

	/**
	 *
	 * Get all the payment records filtered by some condition.
	 *
	 * @param mixed $data
	 * @param mixed $sort
	 * @return mixed
	 */
	public function getPayments($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					payment_id,
					payment_type,
					mpp.seller_id as 'seller_id',
					payment_code,
					payment_status,
					amount,
					currency_code,
					sender_data,
					receiver_data,
					mpp.description as 'description',
					mpp.date_created as 'date_created',
					ms.nickname
				FROM `" . DB_PREFIX . "ms_pg_payment` mpp
				LEFT JOIN `" . DB_PREFIX . "ms_seller` ms
					USING (seller_id)
				WHERE 1 = 1 "
			. (isset($data['payment_id']) ? " AND payment_id =  " .  (int)$data['payment_id'] : "")
			. (isset($data['payment_type']) ? " AND payment_type =  " .  (int)$data['payment_type'] : "")
			. (isset($data['seller_id']) ? " AND mpp.seller_id =  " .  (int)$data['seller_id'] : "")
			. (isset($data['payment_code']) ? " AND payment_code =  " .  $this->db->escape($data['payment_code']) : "")
			. (isset($data['payment_status']) ? " AND payment_status IN  (" .  $this->db->escape(implode(',', $data['payment_status'])) . ")" : "")
			. (isset($data['currency_id']) ? " AND currency_id =  " .  (int)$data['currency_id'] : "")
//			. (isset($data['receiver_id']) ? " AND receiver_data REGEXP '\"" . $data['receiver_id'] . "\":'" : "")

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		foreach ($res->rows as &$row) {
			$row['sender_data'] = json_decode($row['sender_data']);
			$row['receiver_data'] = json_decode($row['receiver_data']);
			$row['description'] = json_decode($row['description']);
		}

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}
}
?>