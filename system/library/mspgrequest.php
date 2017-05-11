<?php

class MsPgRequest extends Model {
	const TYPE_SIGNUP = 1;
	const TYPE_LISTING = 2;
	const TYPE_PAYOUT = 3;
	const TYPE_PAYOUT_REQUEST = 4;
	const TYPE_RECURRING = 5;
	const TYPE_SALE = 6;

	const STATUS_UNPAID = 1;
	const STATUS_PAID = 2;
	const STATUS_REFUND_REQUESTED = 3;
	const STATUS_REFUNDED = 4;

	/**
	 * Create payment request. Request is created after seller's registration and after he published any product.
	 * Request is also created for all sale transactions, with difference it is paid automatically.
	 *
	 * @param mixed $data
	 * @return int $request_id
	 */
	public function createRequest($data) {
		// add on duplicate key
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_pg_request`
				SET seller_id = " . (int)$data['seller_id'] . ",
					product_id = " . (isset($data['product_id']) ? (int)$data['product_id'] : 'NULL') . ",
					order_id = " . (isset($data['order_id']) ? (int)$data['order_id'] : 'NULL') . ",
					request_type = " . (int)$data['request_type'] . ",
					request_status = " . (int)$data['request_status'] . ",
					description = '" . $this->db->escape(htmlspecialchars(nl2br($data['description']), ENT_COMPAT)) . "',
					amount = ". (float)$this->currency->format($data['amount'], $this->config->get('config_currency'), '', FALSE) . ",
					currency_id = " . (int)$data['currency_id'] . ",
					currency_code = '" . $this->db->escape($data['currency_code']) . "',
					date_created = NOW(),
					date_modified = NULL";

		$this->db->query($sql);
		return $this->db->getLastId();
	}

	/**
	 * Update payment request.
	 *
	 * @param int $request_id
	 * @param mixed $data
	 * @return void
	 */
	public function updateRequest($request_id, $data) {
		$sql = "UPDATE `" . DB_PREFIX . "ms_pg_request`
				SET request_id = request_id"
					. (isset($data['payment_id']) ? ", payment_id = " . (int)$data['payment_id'] : "")
					. (isset($data['product_id']) ? ", product_id = " . (int)$data['product_id'] : "")
					. (isset($data['order_id']) ? ", order_id = " . (int)$data['order_id'] : "")
					. (isset($data['request_type']) ? ", request_type = " . (int)$data['request_type'] : "")
					. (isset($data['request_status']) ? ", request_status = " . (int)$data['request_status'] : "")
					. (isset($data['description']) ? ", description = '" . $this->db->escape(htmlspecialchars(nl2br($data['description']), ENT_COMPAT)) . "'" : "")
					. (isset($data['amount']) ? ", amount = " . (float)$this->currency->format($data['amount'], $this->config->get('config_currency'), '', FALSE) : "")
					. (isset($data['currency_id']) ? ", currency_id = " . (int)$data['currency_id'] : "")
					. (isset($data['currency_code']) ? ", currency_code = '" . $this->db->escape($data['currency_code']) . "'" : "")
					. (isset($data['date_created']) ? ", date_created = NOW()" : "")
					. (isset($data['date_modified']) ? ", date_modified = NOW()" : "") . "
				WHERE request_id = " . (int)$request_id;

		$this->db->query($sql);
	}

	/**
	 * Delete payment request.
	 *
	 * @param int $request_id
	 * @return void
	 */
	public function deleteRequest($request_id) {
		$sql = "DELETE FROM `" . DB_PREFIX . "ms_pg_request`
				WHERE request_id = " . (int)$request_id;

		$this->db->query($sql);
	}

	/**
	 * Get all the payment requests filtered by some condition (e.g., seller_id, request_id, request status etc.).
	 *
	 * @param mixed $data
	 * @param mixed $sort
	 * @return mixed
	 */
	public function getRequests($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					request_id,
					payment_id,
					seller_id,
					product_id,
					order_id,
					request_type,
					request_status,
					mpr.description as 'description',
					amount,
					currency_code,
					mpr.date_created as 'date_created',
					mpr.date_modified as 'date_modified',
					ms.seller_id as 'seller_id',
					ms.nickname
				FROM `" . DB_PREFIX . "ms_pg_request` mpr
				LEFT JOIN `" . DB_PREFIX . "ms_seller` ms
					USING (seller_id)
				WHERE 1 = 1 "
					. (isset($data['request_id']) ? " AND request_id =  " .  (int)$data['request_id'] : '')
					. (isset($data['payment_id']) ? " AND payment_id =  " .  (int)$data['payment_id'] : '')
					. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
					. (isset($data['product_id']) ? " AND product_id =  " .  (int)$data['product_id'] : '')
					. (isset($data['order_id']) ? " AND order_id =  " .  (int)$data['order_id'] : '')
					. (isset($data['currency_id']) ? " AND currency_id =  " .  (int)$data['currency_id'] : '')
					. (isset($data['request_type']) ? " AND request_type IN  (" .  $this->db->escape(implode(',', $data['request_type'])) . ")" : '')
					. (isset($data['request_status']) ? " AND request_status IN  (" .  $this->db->escape(implode(',', $data['request_status'])) . ")" : '')

					. $filters

					. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
					. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		if($res->num_rows) {
			foreach ($res->rows as &$row) {
				$row['description'] = html_entity_decode($row['description']);
			}
			$res->row['description'] = html_entity_decode($row['description']);
		}

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	/**
	 * Get amount of pending and paid money
	 *
	 * @param $data
	 * @return mixed
	 */
	public function getTotalAmount($data) {
		$sql = "SELECT SUM(amount) as 'total'
				FROM `" . DB_PREFIX . "ms_pg_request`
				WHERE 1 = 1 "
			. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['request_type']) ? " AND request_type IN  (" .  $this->db->escape(implode(',', $data['request_type'])) . ")" : '')
			. (isset($data['request_status']) ? " AND request_status IN  (" .  $this->db->escape(implode(',', $data['request_status'])) . ")" : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	/**
	 * Get type of request
	 *
	 * @param $request_id
	 * @return int $request_type
	 */
	public function getRequestType($request_id) {
		$sql = "SELECT request_type
				FROM `" . DB_PREFIX . "ms_pg_request`
				WHERE request_id = " . (int)$request_id;

		$res = $this->db->query($sql);
		$request_type = $res->rows ? (int)$res->row['request_type'] : 0;

		return $request_type;
	}
}
?>