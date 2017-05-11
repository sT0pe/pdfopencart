<?php
class MsReview extends Model {

	public function createReview($data) {
		$this->db->query("
		INSERT INTO " . DB_PREFIX . "ms_review
		SET author_id=" . (int)$data['author_id'] . ",
		product_id = " . (int)$data['product_id'] . ",
		order_product_id = " . ($data['order_product_id'] ? (int)$data['order_product_id'] : NULL) . ",
		order_id = " . ($data['order_id'] ? (int)$data['order_id'] : NULL) . ",
		rating = " . (int)$data['rating'] . ",
		title = '" . $this->db->escape($data['title']) . "',
		comment = '" . $this->db->escape($data['comment']) . "',
		description_accurate = " . (int)$data['description_accurate'] . ",
		date_created = NOW(),
		status = 1");
	}

	public function getReviews($data = array(), $sort = array()) {
		$sql = "SELECT 
				SQL_CALC_FOUND_ROWS
				*
				FROM `" . DB_PREFIX . "ms_review` msr";

		if(isset($data['seller_id'])) {
			$sql .= "
				JOIN
				(SELECT product_id, seller_id FROM " . DB_PREFIX . "ms_product WHERE product_status = 1 AND product_approved = 1) msp
				ON (msp.product_id = msr.product_id)";
		}

		$sql .= "
				WHERE 1 = 1 "
			. (isset($data['seller_id']) ? " AND msp.seller_id =  " .  (int)$data['seller_id'] : '')
			. (isset($data['product_id']) ? " AND msr.product_id =  " .  (int)$data['product_id'] : '')
			. (isset($data['order_product_id']) ? " AND msr.order_product_id =  " .  (int)$data['order_product_id'] : '')
			. (isset($data['order_id']) ? " AND msr.order_id =  " .  (int)$data['order_id'] : '')
			. (isset($data['author_id']) ? " AND msr.author_id =  " .  (int)$data['author_id'] : '')
			. (isset($data['date']) ? " AND msr.date_created =  " .  $this->db->escape($data['date']) : '');

		$sql .= " ORDER BY msr.date_created DESC";

		$sql .= (isset($sort['limit']) ? " LIMIT " . (int)$sort['offset'] . ', ' . (int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return $res->rows;
	}

	public function updateReview($review_id, $data) {
		$this->db->query("
		UPDATE " . DB_PREFIX . "ms_review
		SET rating = " . (int)$data['rating'] . ",
		title = '" . $this->db->escape($data['title']) . "',
		comment = '" . $this->db->escape($data['comment']) . "',
		description_accurate = " . (int)$data['description_accurate'] . ",
		date_updated = NOW()
		WHERE review_id=" . (int)$review_id);
	}

	public function removeReview($review_id) {
		$this->db->query("
		DELETE FROM " . DB_PREFIX . "ms_review WHERE review_id=" . (int)$review_id);
	}

	public function setHelpful($review_id, $isHelpful) {
		if($isHelpful) {
			$this->db->query("UPDATE " . DB_PREFIX . "ms_review SET helpful=helpful+1 WHERE review_id = " . (int)$review_id);
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "ms_review SET unhelpful=unhelpful+1 WHERE review_id = " . (int)$review_id);
		}
	}

	public function getFeedbackHistory($data = array()) {

		$sql = "SELECT
				om.total as one_month, 
				tm.total as three_months, 
				sm.total as six_months, 
				twm.total as twelve_months 
				FROM `" . DB_PREFIX . "ms_review` r

				LEFT JOIN
					(SELECT 
					rating, 
					COUNT(*) as total 
					FROM `" . DB_PREFIX . "ms_review` r1
					LEFT JOIN
						(SELECT
							product_id,
							seller_id
						FROM `" . DB_PREFIX . "ms_product`
						WHERE
							product_status = 1
							AND product_approved = 1) as msp 
					ON (msp.product_id = r1.product_id)
					WHERE 
						DATEDIFF(DATE(date_created), DATE(NOW())) <= 0 
						AND DATEDIFF(DATE(NOW() - INTERVAL 1 MONTH), DATE(date_created)) < 0"
						. ((isset($data['seller_id'])) ? (" AND msp.seller_id = " . (int)$data['seller_id']) : "") . "
					GROUP BY rating) as om ON om.rating = r.rating
				LEFT JOIN 
					(SELECT 
					rating, 
					COUNT(*) as total 
					FROM `" . DB_PREFIX . "ms_review` r2
					LEFT JOIN
						(SELECT
							product_id,
							seller_id
						FROM `" . DB_PREFIX . "ms_product`
						WHERE
							product_status = 1
							AND product_approved = 1) as msp 
					ON (msp.product_id = r2.product_id)
					WHERE 
						DATEDIFF(DATE(date_created), DATE(NOW() - INTERVAL 1 MONTH)) <= 0 
						AND DATEDIFF(DATE(NOW() - INTERVAL 3 MONTH), DATE(date_created)) < 0"
						. ((isset($data['seller_id'])) ? (" AND msp.seller_id = " . (int)$data['seller_id']) : "") . "
					GROUP BY rating) as tm ON tm.rating = r.rating
				LEFT JOIN 
					(SELECT 
					rating, 
					COUNT(*) as total 
					FROM `" . DB_PREFIX . "ms_review` r3
					LEFT JOIN
						(SELECT
							product_id,
							seller_id
						FROM `" . DB_PREFIX . "ms_product`
						WHERE
							product_status = 1
							AND product_approved = 1) as msp 
					ON (msp.product_id = r3.product_id)
					WHERE 
						DATEDIFF(DATE(date_created), DATE(NOW() - INTERVAL 3 MONTH)) <= 0 
						AND DATEDIFF(DATE(NOW() - INTERVAL 6 MONTH), DATE(date_created)) < 0"
						. ((isset($data['seller_id'])) ? (" AND msp.seller_id = " . (int)$data['seller_id']) : "") . "
					GROUP BY rating) as sm ON sm.rating = r.rating
				LEFT JOIN 
					(SELECT 
					rating, 
					COUNT(*) as total 
					FROM `" . DB_PREFIX . "ms_review` r4
					LEFT JOIN
						(SELECT
							product_id,
							seller_id
						FROM `" . DB_PREFIX . "ms_product`
						WHERE
							product_status = 1
							AND product_approved = 1) as msp 
					ON (msp.product_id = r4.product_id)
					WHERE 
						DATEDIFF(DATE(date_created), DATE(NOW() - INTERVAL 6 MONTH)) <= 0 
						AND DATEDIFF(DATE(NOW() - INTERVAL 12 MONTH), DATE(date_created)) < 0"
						. ((isset($data['seller_id'])) ? (" AND msp.seller_id = " . (int)$data['seller_id']) : "") . "
					GROUP BY rating) as twm ON twm.rating = r.rating
					
				WHERE
					1 = 1"
				. ((isset($data['rating']) && $data['rating'] === 'positive') ? " AND r.rating IN (4,5)" : "")
				. ((isset($data['rating']) && $data['rating'] === 'neutral') ? " AND r.rating = 3" : "")
				. ((isset($data['rating']) && $data['rating'] === 'negative') ? " AND r.rating IN (1,2)" : "");

		$sql .= " GROUP BY r.rating";

		$res = $this->db->query($sql);
		return $res->rows;
	}

}
?>