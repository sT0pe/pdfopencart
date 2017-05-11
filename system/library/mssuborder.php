<?php
class MsSuborder extends Model {
	/** suborder histories **/
	public function addSuborderHistory($data = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_suborder_history
				SET suborder_id = " . (int)$data['suborder_id'] . ",
					order_status_id = " . (int)$data['order_status_id'] . ",
					comment = '" . $this->db->escape(isset($data['comment']) ? $data['comment'] : '') . "',
					date_added = NOW()";

		return $this->db->query($sql);
	}

	public function getSuborderHistory($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "ms_suborder_history
				WHERE 1 = 1"
			. (isset($data['suborder_id']) ? " AND suborder_id =  " .  (int)$data['suborder_id'] : '');

		$res = $this->db->query($sql);
		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	/** suborders **/
	public function createSuborder($data = array())	{
		//$total = $this->MsLoader->MsOrderData->getOrderTotal((int)$data['order_id'], array('seller_id' => $data['seller_id']));

		$sql = "INSERT INTO " . DB_PREFIX . "ms_suborder
				SET order_id = " . (int)$data['order_id'] . ",
					seller_id = " . (int)$data['seller_id'] . ",
					order_status_id = " . (int)$data['order_status_id'] . ",
					date_added = '" . $this->db->escape($data['date_added']) . "',
					date_modified = '" . $this->db->escape($data['date_modified']) . "'";

		return $this->db->query($sql);
	}

	public function getSuborders($data = array()) {
		$sql = "SELECT
				SQL_CALC_FOUND_ROWS
				*
				FROM " . DB_PREFIX . "ms_suborder mso
				LEFT JOIN `" . DB_PREFIX . "order` o
					ON (o.order_id = mso.order_id)
				WHERE 1 = 1 "
					. (isset($data['include_abandoned']) ? "" : " AND o.order_status_id > '0'")
					. (isset($data['seller_id']) ? " AND mso.seller_id =  " .  (int)$data['seller_id'] : "")
					. (isset($data['order_id']) ? " AND mso.order_id =  " .  (int)$data['order_id'] : "")
					. (isset($data['suborder_id']) ? " AND mso.suborder_id =  " .  (int)$data['suborder_id'] : "")
					. (isset($data['period_start']) ? " AND DATEDIFF(mso.date_added, '{$data['period_start']}') >= 0" : "");

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	public function getSuborderStatus($data = array()) {
		$res = $this->db->query("SELECT order_status_id
			FROM " . DB_PREFIX . "ms_suborder
			WHERE order_id = " . (int)$data['order_id'] . "
				AND seller_id = " . (int)$data['seller_id']
		);

		return isset($res->row['order_status_id']) ? $res->row['order_status_id'] : false;
	}

	public function updateSuborderStatus($data = array()) {
		$sql = "UPDATE " . DB_PREFIX . "ms_suborder
				SET order_status_id = " . (int)$data['order_status_id'] . "
				WHERE suborder_id = " . (int)$data['suborder_id'];

		$this->db->query($sql);
	}
	
	public function getSuborderTotal($order_id, $data) {
		$sql = 
			"SELECT SUM(opd.seller_net_amt) as 'total',
			SUM(opd.store_commission_pct) as 'tax' FROM `" . DB_PREFIX . "order_product` op 
			JOIN `" . DB_PREFIX . "ms_order_product_data` opd
			ON (op.order_product_id = opd.order_product_id AND opd.order_product_id IS NOT NULL)
			WHERE op.order_id=" . (int)$order_id . (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);

		return $res->row;
	}

	public function isValidSeller($suborder_id, $seller_id) {
		$sql = "SELECT
				seller_id
				FROM " . DB_PREFIX . "ms_suborder
				WHERE suborder_id = " . (int)$suborder_id;

		$res = $this->db->query($sql);

		return $seller_id == $res->row['seller_id'] ? true : false;
	}
}