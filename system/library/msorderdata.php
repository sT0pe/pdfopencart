<?php
class MsOrderData extends Model {
	/** orders **/
	public function getOrders($data = array(), $sort = array(), $cols = array()) {
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
					*,"
					// additional columns
					. (isset($cols['total_amount']) ? "
						(SELECT IFNULL(
							(SELECT SUM(opd2.seller_net_amt) as 'total' FROM `" . DB_PREFIX . "order_product` op JOIN `" . DB_PREFIX . "ms_order_product_data` opd2 ON (op.order_id = opd2.order_id AND op.product_id = opd2.product_id AND opd2.order_product_id IS NULL) WHERE op.order_id=o.order_id" . (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '') . "),
							(SELECT SUM(opd.seller_net_amt) as 'total' FROM `" . DB_PREFIX . "order_product` op JOIN `" . DB_PREFIX . "ms_order_product_data` opd ON (op.order_product_id = opd.order_product_id AND opd.order_product_id IS NOT NULL) WHERE op.order_id=o.order_id" . (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '') . ")
						)) as total_amount,
						" : "")

					// product names for filtering
					. (isset($cols['products']) ? "
						(SELECT GROUP_CONCAT(name)
						FROM " . DB_PREFIX . "order_product
						LEFT JOIN " . DB_PREFIX . "ms_order_product_data
						USING(order_id, product_id)
						WHERE order_id = o.order_id
						AND seller_id = mopd.seller_id) as products,
					" : "")
				."1
		FROM `" . DB_PREFIX . "order` o
		INNER JOIN `" . DB_PREFIX . "ms_order_product_data` mopd
		USING (order_id)
		WHERE seller_id = " . (int)$data['seller_id']
		. (isset($data['order_status']) && $data['order_status'] ? " AND o.order_status_id IN  (" .  $this->db->escape(implode(',', $data['order_status'])) . ")" : " AND o.order_status_id > '0' ")
		
		. $wFilters
		
		. " GROUP BY order_id HAVING 1 = 1 "
		
		. $hFilters
		
		. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
		. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");

		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		return $res->rows;
	}

	public function getOrderTotal($order_id, $data) {
		$sql = "SELECT IFNULL(
					(SELECT SUM(opd.seller_net_amt) as 'total' FROM `" . DB_PREFIX . "order_product` op JOIN `" . DB_PREFIX . "ms_order_product_data` opd ON (op.order_product_id = opd.order_product_id AND opd.order_product_id IS NOT NULL) WHERE op.order_id=" . (int)$order_id . (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '') . "),
					(SELECT SUM(opd2.seller_net_amt) as 'total' FROM `" . DB_PREFIX . "order_product` op JOIN `" . DB_PREFIX . "ms_order_product_data` opd2 ON (op.order_id = opd2.order_id AND op.product_id = opd2.product_id AND opd2.order_product_id IS NULL) WHERE op.order_id=" . (int)$order_id . (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '') . ")
				) as total";

		$res = $this->db->query($sql);

		return $res->row['total'];		
	}

	public function getOrderShippingTotal($order_id, $data) {
		$sql = "SELECT IFNULL(
					(SELECT
							SUM(opsd.shipping_cost) as 'total'
						FROM `" . DB_PREFIX . "order_product` op
						JOIN `" . DB_PREFIX . "ms_order_product_data` opd
							ON (op.order_product_id = opd.order_product_id AND opd.order_product_id IS NOT NULL)
						JOIN `" . DB_PREFIX . "ms_order_product_shipping_data` opsd
							ON (op.order_product_id = opsd.order_product_id AND opsd.order_product_id IS NOT NULL)
						WHERE
							op.order_id=" . (int)$order_id . (isset($data['seller_id']) ? " AND opd.seller_id =  " .  (int)$data['seller_id'] : '') . "),
					(SELECT
							SUM(opsd2.shipping_cost) as 'total'
						FROM `" . DB_PREFIX . "order_product` op
						JOIN `" . DB_PREFIX . "ms_order_product_data` opd2
							ON (op.order_id = opd2.order_id AND op.product_id = opd2.product_id AND opd2.order_product_id IS NULL)
						JOIN `" . DB_PREFIX . "ms_order_product_shipping_data` opsd2
							ON (op.order_id = opsd2.order_id AND op.product_id = opsd2.product_id AND opsd2.order_product_id IS NULL)
						WHERE
							op.order_id=" . (int)$order_id . (isset($data['seller_id']) ? " AND opd2.seller_id =  " .  (int)$data['seller_id'] : '') . ")
				) as total";

		$res = $this->db->query($sql);

		return $res->row['total'];
	}

	public function getOrderData($data = array()) {
		$sql = "SELECT opd.*, opsd.shipping_cost
				FROM " . DB_PREFIX . "ms_order_product_data opd
				JOIN " . DB_PREFIX . "ms_order_product_shipping_data opsd
					ON (opd.order_id = opsd.order_id AND opd.product_id = opsd.product_id AND opd.order_product_id = opsd.order_product_id)
				WHERE 1 = 1"
				. (isset($data['product_id']) ? " AND opd.product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['order_product_id']) ? " AND opd.order_product_id =  " .  (int)$data['order_product_id'] : '')
				. (isset($data['order_id']) ? " AND opd.order_id =  " .  (int)$data['order_id'] : '');
		
		$res = $this->db->query($sql);

		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	public function getOrderComment($data = array()) {
		$sql = "SELECT *
				FROM " . DB_PREFIX . "ms_order_comment
				WHERE 1 = 1 "
			. (isset($data['product_id']) ? " AND product_id =  " .  (int)$data['product_id'] : '')
			. (isset($data['seller_id']) ? " AND seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['order_id']) ? " AND order_id =  " .  (int)$data['order_id'] : '');

		$res = $this->db->query($sql);

		return isset($res->row['comment']) ? $res->row['comment'] : '';
	}

	public function getOrderProducts($data) {
		$sql = "SELECT
				*
				FROM (
					SELECT
						op.*,
						opd1.seller_id,
						opd1.seller_net_amt,
						opsd1.shipping_cost
					FROM " . DB_PREFIX . "order_product op
					JOIN " . DB_PREFIX . "ms_order_product_data opd1
						ON (op.order_product_id = opd1.order_product_id AND opd1.order_product_id IS NOT NULL)
					JOIN " . DB_PREFIX . "ms_order_product_shipping_data opsd1
						ON (op.order_product_id = opsd1.order_product_id AND opsd1.order_product_id IS NOT NULL)
				UNION
					SELECT
						op.*,
						opd2.seller_id,
						opd2.seller_net_amt,
						opsd2.shipping_cost
					FROM " . DB_PREFIX . "order_product op
					JOIN " . DB_PREFIX . "ms_order_product_data opd2
						ON (op.order_id = opd2.order_id AND op.product_id = opd2.product_id AND opd2.order_product_id IS NULL)
					JOIN " . DB_PREFIX . "ms_order_product_shipping_data opsd2
						ON (op.order_id = opsd2.order_id AND op.product_id = opsd2.product_id AND opsd2.order_product_id IS NULL)
				) AS u
				WHERE 1 = 1"

				. (isset($data['order_id']) ? " AND u.order_id =  " .  (int)$data['order_id'] : '')
				. (isset($data['seller_id']) ? " AND u.seller_id =  " .  (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);
		return $res->rows;
	}

	public function addOrderProductData($order_id, $product_id, $data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_order_product_data
				SET order_id = " . (int)$order_id . ",
					product_id = " . (int)$product_id . ",
					order_product_id = " . (int)$data['order_product_id'] . ",
					suborder_id = " . (int)$data['suborder_id'] . ",
					seller_id = " . (int)$data['seller_id'] . ",
					store_commission_flat = " . (float)$data['store_commission_flat'] . ",
					store_commission_pct = " . (float)$data['store_commission_pct'] . ",
					seller_net_amt = " . (float)$data['seller_net_amt'];

		$this->db->query($sql);
		return $this->db->getLastId();
	}

	/**
	 * Add shipping information for each order product
	 *
	 * @param $order_id
	 * @param $product_id
	 * @param $data
	 */
	public function addOrderProductShippingData($order_id, $product_id, $data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_order_product_shipping_data
				SET order_id = " . (int)$order_id . ",
					product_id = " . (int)$product_id . ",
					order_product_id = " . (int)$data['order_product_id']

			. (isset($data['fixed_shipping_method_id']) ? ", fixed_shipping_method_id = " . (int)$data['fixed_shipping_method_id'] : "")
			. (isset($data['combined_shipping_method_id']) ? ", combined_shipping_method_id = " . (int)$data['combined_shipping_method_id'] : "")
			. (isset($data['shipping_cost']) ? ", shipping_cost = " . (float)$data['shipping_cost'] : "");

		$this->db->query($sql);
	}

	public function addOrderComment($order_id, $product_id, $data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_order_comment
				SET order_id = " . (int)$order_id . ",
					product_id = " . (int)$product_id . ",
					seller_id = " . (int)$data['seller_id'] . ",
					comment = '" . $data['comment']. "'";

		$this->db->query($sql);

		$order_comment_id = $this->db->getLastId();
		return $order_comment_id;
	}

	/**
	 * Check if order was created by the customer. Customers can only see their orders.
	 *
	 * @param int $order_id
	 * @param int $customer_id
	 * @return mixed
	 */
	public function isOrderCreatedByCustomer($order_id = 0, $customer_id = 0) {
		$sql = "SELECT 1
				FROM `" . DB_PREFIX . "order`
				WHERE 
					order_id = " . (int)$order_id . "
					AND customer_id = " . (int)$customer_id;

		$res = $this->db->query($sql);

		return $res->num_rows;
	}

	/**
	 *  Check if order product has shipping cost.
	 *
	 * @param array $data
	 * @return int
	 */
	public function getOrderProductShippable($data = array()) {
		$sql = "SELECT
				shipping_cost
				FROM " . DB_PREFIX . "ms_order_product_shipping_data
				WHERE 1 = 1
				AND shipping_cost IS NOT NULL
				AND shipping_cost > 0"

			. (isset($data['order_id']) ? " AND order_id = " . (int)$data['order_id'] : '')
			. (isset($data['product_id']) ? " AND product_id = " . (int)$data['product_id'] : '')
			. (isset($data['order_product_id']) ? " AND order_product_id = " . (int)$data['order_product_id'] : '');

		$res = $this->db->query($sql);

		return $res->num_rows ? 1 : 0;
	}
}
?>
