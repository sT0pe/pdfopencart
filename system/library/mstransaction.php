<?php
class MsTransaction extends Model {
	private $sendmail;
	private $log;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->language('multiseller/multiseller');

		$this->sendmail = false;
		$this->log = new Log('ms_transactions.log');
	}

	/**
	 * Gets order products by given order_id
	 *
	 * @param $order_id
	 * @return mixed
	 */
	private function _getOrderProducts($order_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order_product
				WHERE order_id = " . (int)$order_id;

		$res = $this->db->query($sql);

		return $res->rows;
	}

	/**
	 * Processes operations with suborders of an order
	 *
	 * @param $order_id
	 * @param $seller_id
	 * @return mixed
	 */
	public function processSuborder($order_id, $seller_id) {
		// fetch suborder if it exists already
		$suborder = $this->MsLoader->MsSuborder->getSuborders(array(
			'order_id' => $order_id,
			'seller_id' => $seller_id,
			'include_abandoned' => 1,
			'single' => 1
		));
		// $this->log->write('Suborder: ' . print_r($suborder, true));

		if($suborder) {
			$suborder_id = $suborder['suborder_id'];
			// $this->log->write('Suborder exists... ' . $suborder_id);
		} else {
			$this->load->model('account/order');
			$orderData = $this->model_account_order->getOrder($order_id);
			// $this->log->write('Suborder not exist... OrderData: ' . print_r($orderData, true));

			$this->MsLoader->MsSuborder->createSuborder(array(
				'order_id' => $order_id,
				'seller_id' => $seller_id,
				'order_status_id' => 0,
				'date_added' => $orderData['date_added'],
				'date_modified' => $orderData['date_modified']
			));

			$suborder_id = $this->db->getLastId();
			// $this->log->write('Suborder created: ' . $suborder_id);

			$this->MsLoader->MsSuborder->addSuborderHistory(array(
				'suborder_id' => $suborder_id,
				'order_status_id' => 0,
				'comment' => $this->language->get('ms_transaction_order_created')
			));
			// $this->log->write('Suborder history created...');
		}

		return $suborder_id;
	}

	/**
	 * Calculates commissions for each order product
	 *
	 * @param $seller_id
	 * @param $order_product
	 * @param bool|array $coupon
	 * @return array
	 */
	public function processCommission($seller_id, $order_product, $coupon = false) {
		$flat = $pct = $slr_net_amt = 0;
		// $this->log->write('Initial values... Flat: ' . $flat . '; Pct: ' . $pct . '; Slr_net_amt: ' . $slr_net_amt);

		if ($order_product['total'] > 0) {
			$commission_rates = array();

			// Catalog fee priority
			if($this->config->get('msconf_fee_priority') == 1) {
				// $this->log->write('Catalog fee priority...');

				$msp_commission_id = $this->MsLoader->MsProduct->getProductCommissionId($order_product['product_id']);

				// Product fee
				if($msp_commission_id) {
					$commission_rates = $this->MsLoader->MsCommission->getCommissionRates($msp_commission_id);
					// $this->log->write('Product commission rates: ' . print_r($commission_rates, true));

					// Category fee
				} else {
					$msp_categories = $this->MsLoader->MsProduct->getProductOcCategories($order_product['product_id']);
					$commission_rates = $this->MsLoader->MsCategory->getOcCategoryCommission($msp_categories, MsCommission::RATE_SALE, array('price' => $order_product['total']));

					// $this->log->write('Category commission rates: ' . print_r($commission_rates, true));
				}
			}

			// Vendor fee priority
			if($this->config->get('msconf_fee_priority') == 2) {
				// $this->log->write('Vendor fee priority...');

				$commission_rates = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $seller_id));
				// $this->log->write('Vendor commission rates: ' . print_r($commission_rates, true));

			}

			if(!empty($commission_rates) && isset($commission_rates[MsCommission::RATE_SALE])) {
				$flat = $commission_rates[MsCommission::RATE_SALE]['flat'];
				$pct = $order_product['total'] * $commission_rates[MsCommission::RATE_SALE]['percent'] / 100;
			}

			$slr_net_amt = $order_product['total'] + $order_product['tax'] - ($flat + $pct);

			// $this->log->write('Updated values... Flat: ' . $flat . '; Pct: ' . $pct . '; Slr_net_amt: ' . $slr_net_amt);
		}

		// Calculate coupon discount if exists
		if ($coupon && $coupon['type'] == 'P'){
			$flat -= $flat / 100 * $coupon['discount'];
			$pct -= $pct / 100 * $coupon['discount'];
			$slr_net_amt -= $slr_net_amt / 100 * $coupon['discount'];
		}
		// $this->log->write('Coupon applied values... Flat: ' . $flat . '; Pct: ' . $pct . '; Slr_net_amt: ' . $slr_net_amt);

		return array(
			'flat' => $flat,
			'pct' => $pct,
			'slr_net_amt' => $slr_net_amt
		);
	}

	/**
	 * Processes shipping information for each order product
	 *
	 * @param $product_id
	 * @return array
	 */
	public function processShipping($product_id) {
		$fixed_method_id = $combined_method_id = $shipping_cost = NULL;

		$fixed_rules = isset($this->session->data['ms_cart_product_shipping']['fixed']) ? $this->session->data['ms_cart_product_shipping']['fixed'] : false;
		$combined_rules = isset($this->session->data['ms_cart_product_shipping']['combined']) ? $this->session->data['ms_cart_product_shipping']['combined'] : false;
		// $this->log->write('Session shipping values... $fixed_rules: ' . print_r($fixed_rules, true) . '; $combined_rules: ' . print_r($combined_rules, true));

		// Fixed shipping rules
		if(isset($fixed_rules[$product_id]['shipping_method_id'])) {
			$fixed_method_id = $fixed_rules[$product_id]['shipping_method_id'];
		} else if(isset($this->session->data['ms_cart_product_shipping']['free'][$product_id])) {
			$fixed_method_id = 0;
		}

		// Combined shipping rules
		if(isset($combined_rules[$product_id]['shipping_method_id'])) {
			$combined_method_id = $combined_rules[$product_id]['shipping_method_id'];
		}

		// Shipping cost
		if(isset($fixed_rules[$product_id]['cost'])) {
			$shipping_cost = $fixed_rules[$product_id]['cost'];
		} else if(isset($combined_rules[$product_id]['cost'])) {
			$shipping_cost = $combined_rules[$product_id]['cost'];
		}
		// $this->log->write('Shipping values... $fixed_method_id: ' . $fixed_method_id . '; $combined_method_id: ' . $combined_method_id . '; $shipping_cost: ' . $shipping_cost);

		return array(
			'fixed_method_id' => $fixed_method_id,
			'combined_method_id' => $combined_method_id,
			'shipping_cost' => $shipping_cost
		);
	}

	/**
	 * Creates MsOrder data for each order product
	 *
	 * @param array $data
	 */
	public function processMsOrderData($data = array()) {
		$seller_id = isset($data['seller_id']) ? (int)$data['seller_id'] : false;
		$suborder_id = isset($data['suborder_id']) ? (int)$data['suborder_id'] : false;
		$order_product = isset($data['order_product']) ? $data['order_product'] : false;
		$commissions = isset($data['commissions']) ? $data['commissions'] : false;
		$shipping = isset($data['shipping']) ? $data['shipping'] : false;

		// $this->log->write('MsOrder data values... $seller_id: ' . $seller_id . '; $suborder_id: ' . $suborder_id . '; $order_product: ' . print_r($order_product, true) . '; $commissions: ' . print_r($commissions, true) . '; $shipping: ' . print_r($shipping, true));

		$order_data = $this->MsLoader->MsOrderData->getOrderData(array(
			'product_id' => $order_product['product_id'],
			'order_id' => $order_product['order_id'],
			'order_product_id' => $order_product['order_product_id']
		));
		// $this->log->write('Order data: ' . print_r($order_data, true));

		if (!$order_data) {
			$this->MsLoader->MsOrderData->addOrderProductData(
				$order_product['order_id'],
				$order_product['product_id'],
				array(
					'order_product_id' => $order_product['order_product_id'],
					'seller_id' => $seller_id,
					'suborder_id' => $suborder_id,
					'store_commission_flat' => $commissions['flat'],
					'store_commission_pct' => $commissions['pct'],
					'seller_net_amt' => $commissions['slr_net_amt'],
					'order_status_id' => isset($data['order_status_id']) ? (int)$data['order_status_id'] : 0
				)
			);
			// $this->log->write('Product order data added...');

			$this->MsLoader->MsOrderData->addOrderProductShippingData(
				$order_product['order_id'],
				$order_product['product_id'],
				array(
					'order_product_id' => $order_product['order_product_id'],
					'fixed_shipping_method_id' => $shipping['fixed_method_id'],
					'combined_shipping_method_id' => $shipping['combined_method_id'],
					'shipping_cost' => $shipping['shipping_cost']
				)
			);
			// $this->log->write('Product shipping data added...');
		}
	}

	/**
	 * Processes operations with coupon if it was used
	 *
	 * @return array|bool
	 */
	public function getOrderCoupon() {
		//@todo TEST COUPONS

		if (isset($this->session->data['coupon'])){
			$coupon_query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "coupon` WHERE `code` = '" . $this->db->escape($this->session->data['coupon']) . "'");
			$coupon = $coupon_query->row;
		} else {
			$coupon = false;
		}

		return $coupon;
	}

	/**
	 * Processes operations with seller balance
	 *
	 * @param $type
	 * @param bool $subtype
	 * @param array $data
	 */
	public function processBalance($type, $subtype = false, $data = array()) {
		//@todo CHECK MAILS
		// $this->log->write("In processBalance... Data: " . print_r($data, true));

		$seller_id = isset($data['seller_id']) ? (int)$data['seller_id'] : false;
		$order_id = isset($data['order_id']) ? (int)$data['order_id'] : false;
		$order_product = isset($data['order_product']) ? $data['order_product'] : false;

		if(!$seller_id || !$order_id || !$order_product) {
			$this->log->write("ERROR: Unable to create balance record. Some of the required conditions are not specified: seller_id - " . (int)$seller_id . "; order_id - " . (int)$order_id . "; order_product - " . print_r($order_product, true));
			return;
		}

		$balance_entry = $this->MsLoader->MsBalance->getBalanceEntry(array(
			'seller_id' => $seller_id,
			'product_id' => $order_product['product_id'],
			'order_id' => $order_id,
			'order_product_id' => $order_product['order_product_id'],
			'balance_type' => (int)$type
		));

		if (!$balance_entry) {
			$order_data = $this->MsLoader->MsOrderData->getOrderData(array(
				'product_id' => $order_product['product_id'],
				'order_id' => $order_product['order_id'],
				'order_product_id' => $order_product['order_product_id'],
				'single' => 1
			));

			$amount = 0;
			$description = '';

			if(!isset($order_data['seller_net_amt']) || !isset($order_data['store_commission_flat']) || !isset($order_data['store_commission_pct'])) {
				$this->log->write("ERROR: Unable to calculate seller's and store's commissions. Order info: " . print_r($order_data, true));
			}
			$commission = isset($order_data['store_commission_flat']) && isset($order_data['store_commission_pct']) ? $order_data['store_commission_flat'] + $order_data['store_commission_pct'] : 0;

			switch($type) {
				case MsBalance::MS_BALANCE_TYPE_SALE:
					$amount = isset($order_data['seller_net_amt']) ? $order_data['seller_net_amt'] : 0;
					$description .= sprintf($this->language->get('ms_transaction_sale'), ($order_product['quantity'] > 1 ? $order_product['quantity'] . ' x ' : '')  . $order_product['name'], $this->currency->format($commission, $this->config->get('config_currency')));

					$this->sendmail = true;
					break;

				case MsBalance::MS_BALANCE_TYPE_SHIPPING:
					$amount = $order_data['shipping_cost'];
					$description .= sprintf($this->language->get('ms_transaction_shipping'), ($order_product['quantity'] > 1 ? $order_product['quantity'] . ' x ' : '')  . $order_product['name'], $this->currency->format($commission, $this->config->get('config_currency')));
					break;

				case MsBalance::MS_BALANCE_TYPE_REFUND:
					if($subtype) {
						$subtype_balance_entry = $this->MsLoader->MsBalance->getBalanceEntry(array(
							'seller_id' => $seller_id,
							'product_id' => $order_product['product_id'],
							'order_id' => $order_id,
							'order_product_id' => $order_product['order_product_id'],
							'balance_type' => (int)$subtype
						));

						if($subtype_balance_entry) {
							$amount = -1 * $subtype_balance_entry['amount'];

							switch ($subtype) {
								case MsBalance::MS_BALANCE_TYPE_SALE:
									$description .= sprintf($this->language->get('ms_transaction_refund'), ($order_product['quantity'] > 1 ? $order_product['quantity'] . ' x ' : '')  . $order_product['name']);
									break;

								case MsBalance::MS_BALANCE_TYPE_SHIPPING:
									$description .= sprintf($this->language->get('ms_transaction_shipping_refund'), ($order_product['quantity'] > 1 ? $order_product['quantity'] . ' x ' : '')  . $order_product['name']);
									break;

								default:
									$this->log->write("ERROR: Unable to create balance record. Wrong subtype for refund has been passed: " . (int)$subtype);
									break;
							}
						}
					}
					break;

				default:
					$this->log->write("ERROR: Unable to create balance record. Wrong type has been passed: " . (int)$type);
					break;
			}

			$this->MsLoader->MsBalance->addBalanceEntry($seller_id, array(
				'order_id' => $order_product['order_id'],
				'product_id' => $order_product['product_id'],
				'order_product_id' => $order_product['order_product_id'],
				'balance_type' => (int)$type,
				'amount' => $amount,
				'description' => $description
			));
		}
	}

	/**
	 * Represents main flow for MsOrder creation
	 *
	 * @param $order_id
	 * @return array
	 */
	public function createMsOrderDataEntries($order_id) {
		// $this->log->write('In createMsOrderDataEntries... Order_id: ' . $order_id);

		$ms_order_products = $this->_getOrderProducts($order_id);
		// $this->log->write('MsOrderProducts: ' . print_r($ms_order_products, true));

		// fetch order sellers
		$sellers = array();
		$result = array(
			'sellers_net_amt_total' => 0,
			'commission_total' => 0,
			'sellers_shipping_cost_total' => 0
		);

		$coupon = $this->getOrderCoupon();

		foreach ($ms_order_products as $order_product) {
			// $this->log->write('In $ms_order_products loop. Product: ' . print_r($order_product, true));

			$seller_id = $this->MsLoader->MsProduct->getSellerId($order_product['product_id']);

			// product doesn't belong to any seller - go to the next product
			if (!$seller_id) continue;

			// $this->log->write('Start processSuborder... ');
			$suborder_id = $this->processSuborder($order_id, $seller_id);
			// $this->log->write('End processSuborder... ');

			// $this->log->write('Start processCommission... ');
			$commissions = $this->processCommission($seller_id, $order_product, $coupon);
			// $this->log->write('End processCommission... ');

			// $this->log->write('Start processShipping... ');
			$shipping = $this->processShipping($order_product['product_id']);
			// $this->log->write('End processShipping... ');

			// $this->log->write('Product before processMsOrderData: ' . print_r($order_product, true));

			// $this->log->write('Start processMsOrderData... ');
			$this->processMsOrderData(array(
				'seller_id' => $seller_id,
				'suborder_id' => $suborder_id,
				'order_product' => $order_product,
				'commissions' => $commissions,
				'shipping' => $shipping
			));
			// $this->log->write('End processMsOrderData... ');

			// For ms_pp_adaptive
			if(!isset($sellers[$seller_id])) {
				$sellers[$seller_id] = array(
					'seller_net_amt_total' => 0,
					'seller_commission_total' => 0,
					'shipping_cost_total' => 0,
					'products' => array()
				);
			}

			$sellers[$seller_id]['seller_net_amt_total'] += $commissions['slr_net_amt'];
			$sellers[$seller_id]['seller_commission_total'] += ($commissions['flat'] + $commissions['pct']);
			$sellers[$seller_id]['shipping_cost_total'] += $shipping['shipping_cost'];
			$sellers[$seller_id]['products'][] = array(
				'store_commission_flat' => $commissions['flat'],
				'store_commission_pct' => $commissions['pct'],
				'seller_net_amt' => $commissions['slr_net_amt'],
				'fixed_shipping_method_id' => $shipping['fixed_method_id'],
				'combined_shipping_method_id' => $shipping['combined_method_id'],
				'shipping_cost' => $shipping['shipping_cost']
			);

			$result['sellers_net_amt_total'] += $commissions['slr_net_amt'];
			$result['commission_total'] += ($commissions['flat'] + $commissions['pct']);
			$result['sellers_shipping_cost_total'] += $shipping['shipping_cost'];
		}

		// $this->log->write('End $ms_order_products loop... ');

		$result['sellers'] = $sellers;

		// $this->log->write('Return... ');

		return $result;
	}

	/**
	 * Represents flow for MsOrder update from admin-side
	 *
	 * @param $order_id
	 * @return bool
	 */
	public function adminUpdateMsOrderDataEntries($order_id) {
		$ms_order_products = $this->_getOrderProducts($order_id);
		// $this->log->write('In Balance Updates... MsOrderProducts: ' . print_r($ms_order_products, true));

		foreach ($ms_order_products as $order_product) {
			$seller_id = $this->MsLoader->MsProduct->getSellerId($order_product['product_id']);

			if (!$seller_id) continue;

			$suborder = $this->MsLoader->MsSuborder->getSuborders(array('order_id' => $order_id, 'seller_id' => $seller_id, 'include_abandoned' => 1, 'single' => 1));

			if ($suborder['suborder_id']){
				$this->db->query("UPDATE " . DB_PREFIX . "ms_order_product_data
					SET order_product_id = " . (int)$order_product['order_product_id'] . "
					WHERE order_id = " . (int)$order_product['order_id'] . "
						AND product_id = " . (int)$order_product['product_id']
				);

				$this->db->query("UPDATE " . DB_PREFIX . "ms_order_product_shipping_data SET
					order_product_id = " . (int)$order_product['order_product_id'] . " WHERE
					order_id = " . (int)$order_product['order_id'] . " AND
					product_id = " . (int)$order_product['product_id']
				);
			}
		}

		return true;
	}

	/**
	 * Represents flow for MsOrder seller balance entries creation for each order product
	 *
	 * @param $order_id
	 * @param $order_status_id
	 */
	public function createMsOrderBalanceEntries($order_id, $order_status_id) {
		$ms_order_products = $this->_getOrderProducts($order_id);

		// $this->log->write('In createMsOrderBalanceEntries...');
		// $this->log->write('$ms_order_products: ' . print_r($ms_order_products, true));

		foreach ($ms_order_products as $order_product) {
			$seller_id = $this->MsLoader->MsProduct->getSellerId($order_product['product_id']);

			if (!$seller_id) continue;

			// For balance entry creation
			$conditions = array(
				'order_id' => $order_id,
				'seller_id' => $seller_id,
				'order_product' => $order_product
			);

			// $this->log->write('$conditions: ' . print_r($conditions, true));

			if (in_array($order_status_id, $this->config->get('msconf_credit_order_statuses'))) {
				// check adaptive payments
				$request = $this->MsLoader->MsPgRequest->getRequests(array(
					'seller_id' => $seller_id,
					'order_id' => $order_id,
					'request_type' => array(MsPgRequest::TYPE_SALE),
					'request_status' => array(MsPgRequest::STATUS_PAID),
					'single' => 1
				));
				if ($request) {
					$this->sendmail = true;
					continue;
				}

				$this->processBalance(MsBalance::MS_BALANCE_TYPE_SALE, false, $conditions);
				$this->processBalance(MsBalance::MS_BALANCE_TYPE_SHIPPING, false, $conditions);
			} else if (in_array($order_status_id, $this->config->get('msconf_debit_order_statuses'))) {
				$this->sendmail = false;

				$this->processBalance(MsBalance::MS_BALANCE_TYPE_REFUND, MsBalance::MS_BALANCE_TYPE_SALE, $conditions);
				$this->processBalance(MsBalance::MS_BALANCE_TYPE_REFUND, MsBalance::MS_BALANCE_TYPE_SHIPPING, $conditions);
			}
		}

		if($this->sendmail) $this->MsLoader->MsMail->sendOrderMails($order_id);
	}
}