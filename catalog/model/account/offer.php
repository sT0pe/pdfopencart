<?php
class ModelAccountOffer extends Model {

	public function getOffer($offer_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_offer` WHERE offer_id = '" . (int)$offer_id . "' AND seller_id = '" . (int)$this->customer->getId() . "'");

		if ($order_query->num_rows) {

			return array(
				'offer_id'                => $order_query->row['offer_id'],
				'seller_id'               => $order_query->row['seller_id'],
				'offer_name'              => $order_query->row['offer_name'],
				'delivery_cost'           => $order_query->row['delivery_cost'],
				'service_cost'            => $order_query->row['service_cost'],
				'date_start'              => $order_query->row['date_start'],
				'date_end'                => $order_query->row['date_end'],
				'prepayment'              => $order_query->row['prepayment'],
				'show_work_cost'          => $order_query->row['show_work_cost'],
				'offer_by_image'          => $order_query->row['offer_by_image'],
				'offer_by_name'           => $order_query->row['offer_by_name'],
				'offer_by_nip'            => $order_query->row['offer_by_nip'],
				'offer_by_address'        => $order_query->row['offer_by_address'],
				'offer_by_phone'          => $order_query->row['offer_by_phone'],
				'offer_for_image'         => $order_query->row['offer_for_image'],
				'offer_for_name'          => $order_query->row['offer_for_name'],
				'offer_for_nip'           => $order_query->row['offer_for_nip'],
				'offer_for_address'       => $order_query->row['offer_for_address'],
				'offer_for_phone'         => $order_query->row['offer_for_phone'],
				'date_created'            => $order_query->row['date_created']
			);
		} else {
			return false;
		}
	}

}