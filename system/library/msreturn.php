<?php
class MsReturn extends Model {
	const STATUS_OPENED = 1;
	const STATUS_CLOSED = 2;

	public function _createReturnProduct($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_return_product`
				SET return_id = " . (isset($data['return_id']) && $data['return_id'] ? (int)$data['return_id'] : '0') . ",
					product_id = " . (isset($data['product_id']) && $data['product_id'] ? (int)$data['product_id'] : '0') . ",
					order_product_id = " . (isset($data['order_product_id']) && $data['order_product_id'] ? (int)$data['order_product_id'] : '0') . ",
					product_quantity = " . (isset($data['seller_id']) && $data['product_quantity'] ? (int)$data['product_quantity'] : '0') . ",
					return_reason_id = " . (isset($data['return_reason_id']) && $data['return_reason_id'] ? (int)$data['return_reason_id'] : '0') . ",
					return_action_id = " . (isset($data['return_action_id']) && $data['return_action_id'] ? (int)$data['return_action_id'] : '0');

		$this->db->query($sql);
	}

	public function createReturn($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_return`
				SET order_id = " . (isset($data['order_id']) && $data['order_id'] ? (int)$data['order_id'] : '0') . ",
					suborder_id = " . (isset($data['suborder_id']) && $data['suborder_id'] ? (int)$data['suborder_id'] : '0') . ",
					seller_id = " . (isset($data['seller_id']) && $data['seller_id'] ? (int)$data['seller_id'] : '0') . ",
					customer_id = " . (isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : '0') . ",
					date_created = NOW()";

		$this->db->query($sql);

		$data['return_id'] = $this->db->getLastId();

		// Create return product info
		$this->_createReturnProduct($data);

		return $this->db->getLastId();
	}

	public function createReturnHistory($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_return_history`
				SET return_id = " . (isset($data['return_id']) && $data['return_id'] ? (int)$data['return_id'] : '0') . ",
					return_status_id = " . (isset($data['return_status_id']) && $data['return_status_id'] ? (int)$data['return_status_id'] : '0') . ",
					seller_comment = " . (isset($data['seller_comment']) && $data['seller_comment'] ? $data['seller_comment'] : 'NULL') . ",
					customer_comment = " . (isset($data['customer_comment']) && $data['customer_comment'] ? $data['customer_comment'] : 'NULL') . ",
					date_created = NOW()";

		$this->db->query($sql);
		return $this->db->getLastId();
	}
	
	public function getReturnsList($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
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
			mret.return_id,
			mret.order_id,
			retstatus.name,
			order.firstname,
			order.lastname,
			mret.date_created
			FROM `" . DB_PREFIX . "ms_return` mret
			LEFT JOIN `" . DB_PREFIX . "ms_return_product` mretprod 
				USING (return_id)
			LEFT JOIN `" . DB_PREFIX . "ms_return_history` mrethist 
				USING (return_id)
			LEFT JOIN `" . DB_PREFIX . "return_status` retstatus 
				USING (return_status_id)
			LEFT JOIN (SELECT firstname, lastname FROM `" . DB_PREFIX . "order`) order
				USING (order_id)
			WHERE 1 = 1"
			. (isset($data['return_id']) ? " AND mret.return_id =  " . (int)$data['return_id'] : '')
			. (isset($data['order_id']) ? " AND mret.order_id =  " . (int)$data['order_id'] : '')
			. (isset($data['customer_id']) ? " AND mret.customer_id =  " . (int)$data['customer_id'] : '')
			. (isset($data['return_status_id']) ? " AND mret.return_status_id =  " . (int)$data['return_status_id'] : '')
			. (isset($data['date_created']) ? " AND mret.date_created =  " . $this->db->escape($data['date_created']) : '')
			. (isset($data['seller_id']) ? " AND mret.seller_id =  " . (int)$data['seller_id'] : '')

			. $wFilters
			
			. " GROUP BY mret.return_id HAVING 1 = 1"
			
			. $hFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		echo $sql;
		die;
		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	public function getReturnData($data) {
		$sql = "SELECT
			mret.return_id,
			mret.order_id,
			retstatus.name,
			order.firstname,
			order.lastname,
			order.email,
			order.telephone,
			order.sh_address,
			order.pmnt_address,
			mret.date_created
			FROM `" . DB_PREFIX . "ms_return` mret
			LEFT JOIN `" . DB_PREFIX . "ms_return_product` mretprod 
				USING (return_id)
			LEFT JOIN `" . DB_PREFIX . "ms_return_history` mrethist 
				USING (return_id)
			LEFT JOIN `" . DB_PREFIX . "return_status` retstatus 
				USING (return_status_id)
			LEFT JOIN `" . DB_PREFIX . "customer` customer 
				USING (customer_id)
			LEFT JOIN (SELECT firstname, lastname, email, telephone, shipping_address_1 AS sh_address, payment_address_1 AS pmnt_address FROM `" . DB_PREFIX . "order`) order
				USING (order_id)
			WHERE 1 = 1"
			. (isset($data['return_id']) ? " AND mret.return_id =  " . (int)$data['return_id'] : '');

		echo $sql;
		die;
		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	public function getReturnHistory($data = array()) {

	}
}
?>