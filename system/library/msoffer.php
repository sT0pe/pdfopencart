<?php
class MsOffer extends Model {
	/** offers **/

	public function addOffer($seller_id, $data) {

		$checkbox = isset($data['work_cost']) ? TRUE : FALSE;

		$sql = "INSERT INTO " . DB_PREFIX . "ms_offer
				SET seller_id = " . $seller_id . ",
					offer_name = '" . $data['name'] . "',
					delivery_cost = " . (float)$data['delivery'] . ",
					date_start = '" . $data['date_start'] . "',
					date_end = '" . $data['date_end'] . "',
					prepayment = " . (float)$data['prepayment'] . ",
					service_cost = " . (float)$data['service_cost'] . ",
					show_work_cost = '" . $checkbox . "',
					offer_by_image = '" . $data['by_image'] . "',
					offer_by_name = '" . $data['by_name'] . "',
					offer_by_nip = " . $data['by_nip'] . ",
					offer_by_address = '" . $data['by_address'] . "',
					offer_by_phone = '" . $data['by_phone'] . "',
					offer_for_image = '" . $data['for_image'] . "',
					offer_for_name = '" . $data['for_name'] . "',
					offer_for_nip = " . $data['for_nip'] . ",
					offer_for_address = '" . $data['for_address'] . "',
					offer_for_phone = '" . $data['for_phone'] . "'";

		$this->db->query($sql);
		return $this->db->getLastId();
	}

	public function saveOffer($data) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_offer_product WHERE offer_id = '" . (int)$data['offer_id'] . "'");

		$checkbox = isset($data['work_cost']) ? TRUE : FALSE;

		$sql = "UPDATE " . DB_PREFIX . "ms_offer
				SET	offer_name = '" . $data['name'] . "',
					delivery_cost = " . (float)$data['delivery'] . ",
					date_start = '" . $data['date_start'] . "',
					date_end = '" . $data['date_end'] . "',
					prepayment = " . (float)$data['prepayment'] . ",
					service_cost = " . (float)$data['service_cost'] . ",
					show_work_cost = '" . $checkbox . "',
					offer_by_image = '" . $data['by_image'] . "',
					offer_by_name = '" . $data['by_name'] . "',
					offer_by_nip = " . $data['by_nip'] . ",
					offer_by_address = '" . $data['by_address'] . "',
					offer_by_phone = '" . $data['by_phone'] . "',
					offer_for_image = '" . $data['for_image'] . "',
					offer_for_name = '" . $data['for_name'] . "',
					offer_for_nip = " . $data['for_nip'] . ",
					offer_for_address = '" . $data['for_address'] . "',
					offer_for_phone = '" . $data['for_phone'] . "' 
				WHERE offer_id = " . $data['offer_id'];

		$this->db->query($sql);
		$this->addOfferProducts($data['offer_id'], $data);

		return true;
	}


	public function addOfferProducts($offer_id, $data){

		foreach ( $data['product_id'] as $id ){

			$sql = "INSERT INTO " . DB_PREFIX . "ms_offer_product
				SET offer_id = " . $offer_id . ",
					product_id = " . $id . ",
					retail_price = " . $data['retail_price'][$id] . ",
					discount = " . $data['discount'][$id] . ",
					quantity = " . $data['quantity'][$id];

			$this->db->query($sql);
		}

		return true;
	}


	public function getOffers($data = array(), $cols = array()) {

		$sql = "SELECT * FROM " . DB_PREFIX . "ms_offer WHERE seller_id = " . (int)$data['seller_id'];

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");

		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		return $res->rows;
	}


	public function getOfferProducts($data) {
		$sql = "SELECT
						pd.product_id, pd.name,
						op.retail_price, op.quantity,
						offer.*
					FROM " . DB_PREFIX . "product_description pd
					JOIN " . DB_PREFIX . "ms_offer_product op
						ON (op.product_id = pd.product_id)
					JOIN " . DB_PREFIX . "ms_offer offer
						ON (op.offer_id = offer.offer_id)
				WHERE 1 = 1"

		       . (isset($data['offer_id']) ? " AND offer.offer_id =  " .  (int)$data['offer_id'] : '')
		       . (isset($data['seller_id']) ? " AND offer.seller_id =  " .  (int)$data['seller_id'] : '');

		$res = $this->db->query($sql);
		return $res->rows;
	}


	public function getProducts($offer_id) {
		$product_data = array();

		$offer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_offer_product WHERE offer_id = '" . (int)$offer_id . "'");


		foreach ($offer_query->rows as $offer) {
			$stock = true;

			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.product_id = '" . (int)$offer['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");

			if ($product_query->num_rows && ($offer['quantity'] > 0)) {

				$offer_product = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_offer_product WHERE product_id = '" . (int)$offer['product_id'] . "' AND offer_id = '" . (int)$offer_id . "'");

				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;

				$option_data = array();

				if(isset($offer['option'])){
					foreach (json_decode($offer['option']) as $product_option_id => $value) {
						$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($option_query->num_rows) {
							if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $value,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
								foreach ($value as $product_option_value_id) {
									$option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

									if ($option_value_query->num_rows) {
										if ($option_value_query->row['price_prefix'] == '+') {
											$option_price += $option_value_query->row['price'];
										} elseif ($option_value_query->row['price_prefix'] == '-') {
											$option_price -= $option_value_query->row['price'];
										}

										if ($option_value_query->row['points_prefix'] == '+') {
											$option_points += $option_value_query->row['points'];
										} elseif ($option_value_query->row['points_prefix'] == '-') {
											$option_points -= $option_value_query->row['points'];
										}

										if ($option_value_query->row['weight_prefix'] == '+') {
											$option_weight += $option_value_query->row['weight'];
										} elseif ($option_value_query->row['weight_prefix'] == '-') {
											$option_weight -= $option_value_query->row['weight'];
										}

										if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
											$stock = false;
										}

										$option_data[] = array(
											'product_option_id'       => $product_option_id,
											'product_option_value_id' => $product_option_value_id,
											'option_id'               => $option_query->row['option_id'],
											'option_value_id'         => $option_value_query->row['option_value_id'],
											'name'                    => $option_query->row['name'],
											'value'                   => $option_value_query->row['name'],
											'type'                    => $option_query->row['type'],
											'quantity'                => $option_value_query->row['quantity'],
											'subtract'                => $option_value_query->row['subtract'],
											'price'                   => $option_value_query->row['price'],
											'price_prefix'            => $option_value_query->row['price_prefix'],
											'points'                  => $option_value_query->row['points'],
											'points_prefix'           => $option_value_query->row['points_prefix'],
											'weight'                  => $option_value_query->row['weight'],
											'weight_prefix'           => $option_value_query->row['weight_prefix']
										);
									}
								}
							} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => '',
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => '',
									'name'                    => $option_query->row['name'],
									'value'                   => $value,
									'type'                    => $option_query->row['type'],
									'quantity'                => '',
									'subtract'                => '',
									'price'                   => '',
									'price_prefix'            => '',
									'points'                  => '',
									'points_prefix'           => '',
									'weight'                  => '',
									'weight_prefix'           => ''
								);
							}
						}
					}
				}

				$price = $product_query->row['price'];

				// Product Discounts
				$discount_quantity = 0;

				foreach ($offer_query->rows as $offer_2) {
					if ($offer_2['product_id'] == $offer['product_id']) {
						$discount_quantity += $offer_2['quantity'];
					}
				}

				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$offer['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}

				// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$offer['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}

				// Reward Points
				$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$offer['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($product_reward_query->num_rows) {
					$reward = $product_reward_query->row['points'];
				} else {
					$reward = 0;
				}

				// Downloads
				$download_data = array();

				$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$offer['product_id'] . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

				foreach ($download_query->rows as $download) {
					$download_data[] = array(
						'download_id' => $download['download_id'],
						'name'        => $download['name'],
						'filename'    => $download['filename'],
						'mask'        => $download['mask']
					);
				}

				// Stock
				if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $offer['quantity'])) {
					$stock = false;
				}

				$recurring_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r LEFT JOIN " . DB_PREFIX . "product_recurring pr ON (r.recurring_id = pr.recurring_id) LEFT JOIN " . DB_PREFIX . "recurring_description rd ON (r.recurring_id = rd.recurring_id) WHERE r.recurring_id = '" . (int)$offer['recurring_id'] . "' AND pr.product_id = '" . (int)$offer['product_id'] . "' AND rd.language_id = " . (int)$this->config->get('config_language_id') . " AND r.status = 1 AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($recurring_query->num_rows) {
					$recurring = array(
						'recurring_id'    => $offer['recurring_id'],
						'name'            => $recurring_query->row['name'],
						'frequency'       => $recurring_query->row['frequency'],
						'price'           => $recurring_query->row['price'],
						'cycle'           => $recurring_query->row['cycle'],
						'duration'        => $recurring_query->row['duration'],
						'trial'           => $recurring_query->row['trial_status'],
						'trial_frequency' => $recurring_query->row['trial_frequency'],
						'trial_price'     => $recurring_query->row['trial_price'],
						'trial_cycle'     => $recurring_query->row['trial_cycle'],
						'trial_duration'  => $recurring_query->row['trial_duration']
					);
				} else {
					$recurring = false;
				}

				$product_data[] = array(
					'offer_id'        => $offer['offer_id'],
					'product_id'      => $product_query->row['product_id'],
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'shipping'        => $product_query->row['shipping'],
					'image'           => $product_query->row['image'],
					'option'          => $option_data,
					'download'        => $download_data,
					'quantity'        => $offer['quantity'],
					'minimum'         => $product_query->row['minimum'],
					'subtract'        => $product_query->row['subtract'],
					'stock'           => $stock,
					'price'           => ($price + $option_price),
					'total'           => ($price + $option_price) * $offer['quantity'],
					'reward'          => $reward * $offer['quantity'],
					'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $offer['quantity'] : 0),
					'tax_class_id'    => $product_query->row['tax_class_id'],
					'weight'          => ($product_query->row['weight'] + $option_weight) * $offer['quantity'],
					'weight_class_id' => $product_query->row['weight_class_id'],
					'length'          => $product_query->row['length'],
					'width'           => $product_query->row['width'],
					'height'          => $product_query->row['height'],
					'length_class_id' => $product_query->row['length_class_id'],
					'recurring'       => $recurring,
					'retail_price'    => $offer_product->row['retail_price'],
					'discount'        => $offer_product->row['discount']
				);

			}
		}

		return $product_data;
	}

	public function hasProducts($offer_id) {
		return count($this->getProducts($offer_id));
	}

	public function hasStock($offer_id) {
		foreach ($this->getProducts($offer_id) as $product) {
			if (!$product['stock']) {
				return false;
			}
		}
		return true;
	}

	public function removeOffer($offer_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_offer WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_offer_product WHERE offer_id = '" . (int)$offer_id . "'");

		return true;
	}
}
?>
