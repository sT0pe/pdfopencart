<?php
class ModelMultimerchUpgrade extends Model {
	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model('localisation/language');
	}

	public function getDbVersion() {
		$res = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "ms_db_schema'");
		if (!$res->num_rows) return '0.0.0.0';
		
		$res = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_db_schema` ORDER BY schema_change_id DESC LIMIT 1");

		if ($res->num_rows)
			return $res->row['major'] . '.' . $res->row['minor'] . '.' . $res->row['build'] . '.' . $res->row['revision'];
		else
			return '0.0.0.0';
	}
	
	public function isDbLatest() {
		$current = $this->getDbVersion();
		if ($this->MsLoader->dbVer > $current) return false;
		return true;
	}

	public function isFilesLatest() {
		$current = $this->getDbVersion();
		if ($this->MsLoader->dbVer < $current) return false;
		return true;
	}

	private function _createSchemaEntry($version) {
		$schema = explode(".", $version);
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_db_schema` (major, minor, build, revision, date_applied) VALUES({$schema[0]},{$schema[1]},{$schema[2]},{$schema[3]}, NOW())");
	}
	
	public function upgradeDb()
	{
		$version = $this->getDbVersion();

		if (version_compare($version, '1.0.0.0') < 0) {
			$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "ms_db_schema` (
				`schema_change_id` int(11) NOT NULL AUTO_INCREMENT,
				`major` TINYINT NOT NULL,
				`minor` TINYINT NOT NULL,
				`build` TINYINT NOT NULL,
				`revision` SMALLINT NOT NULL,
				`date_applied` DATETIME NOT NULL,
			PRIMARY KEY (`schema_change_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_suborder` (
			`suborder_id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`seller_id` int(11) NOT NULL,
			`order_status_id` int(11) NOT NULL,
			PRIMARY KEY (`suborder_id`)
			) DEFAULT CHARSET=utf8");

			$this->_createSchemaEntry('1.0.0.0');
		}

		if (version_compare($version, '1.0.1.0') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_seller` ADD (
				`banner` VARCHAR(255) DEFAULT NULL)");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multiseller/addon');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multiseller/addon');

			$this->_createSchemaEntry('1.0.1.0');
		}

		if (version_compare($version, '1.0.2.0') < 0) {
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_suborder_history` (
				`suborder_history_id` int(5) NOT NULL AUTO_INCREMENT,
				`suborder_id` int(5) NOT NULL,
				`order_status_id` int(5) NOT NULL,
				`comment` text NOT NULL DEFAULT '',
				`date_added` datetime NOT NULL,
				PRIMARY KEY (`suborder_history_id`)
				) DEFAULT CHARSET=utf8");

			$this->_createSchemaEntry('1.0.2.0');
		}

		if (version_compare($version, '1.0.2.1') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multiseller/debug');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multiseller/debug');

			$this->_createSchemaEntry('1.0.2.1');
		}

		if (version_compare($version, '1.0.2.2') < 0) {
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_setting` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`seller_id` int(11) unsigned DEFAULT NULL,
				`seller_group_id` int(11) unsigned DEFAULT NULL,
				`name` varchar(50) DEFAULT NULL,
				`value` varchar(250) DEFAULT NULL,
				`is_encoded` smallint(1) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;");

			$this->_createSchemaEntry('1.0.2.2');
		}

		if (version_compare($version, '1.0.3.1') < 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "ms_order_product_data` ADD `order_product_id` int(11) DEFAULT NULL AFTER `product_id`");

			$this->db->query("ALTER TABLE `" . DB_PREFIX . "ms_balance` ADD `order_product_id` int(11) DEFAULT NULL AFTER `product_id`");

			$this->_createSchemaEntry('1.0.3.1');
		}

		if (version_compare($version, '1.0.3.2') < 0) {
			$this->db->query("
				CREATE UNIQUE INDEX slr_id_name
				ON " . DB_PREFIX . "ms_setting (seller_id, name)");


			//replace data from ms_seller to ms_setting
			$getDataQuery = "SELECT seller_id, company, website FROM " . DB_PREFIX . "ms_seller WHERE 1 GROUP BY seller_id";
			$seller_data = $this->db->query($getDataQuery)->rows;
			foreach ($seller_data as $row) {
				$company = $row['company'];
				$website = $row['website'];
				$seller_id = $row['seller_id'];
//                $seller_group = $this->MsLoader->MsSellerGroup->getSellerGroupBySellerId($seller_id);

				$insertDataQuery =
					"INSERT INTO " . DB_PREFIX . "ms_setting
					SET seller_id = " . (int)$seller_id . ", name = 'slr_company', value = '" . $this->db->escape($company) . "'
					ON DUPLICATE KEY UPDATE
					value = '" . $this->db->escape($company) . "'";
				$this->db->query($insertDataQuery);

				$insertDataQuery =
					"INSERT INTO " . DB_PREFIX . "ms_setting
					SET seller_id = " . (int)$seller_id . ", name = 'slr_website', value = '" . $this->db->escape($website) . "'
					ON DUPLICATE KEY UPDATE
					value = '" . $this->db->escape($website) . "'";
				$this->db->query($insertDataQuery);
			}

			$this->_createSchemaEntry('1.0.3.2');
		}

		if (version_compare($version, '1.0.4.0') < 0) {
			/*ADD `total` decimal(15,4) NOT NULL AFTER `invoice_no`,*/
			$suborderSql = "ALTER TABLE " . DB_PREFIX . "ms_suborder
				ADD `invoice_no` int(11) NOT NULL DEFAULT '0' AFTER `seller_id`,
				ADD `invoice_prefix` varchar(26) NOT NULL DEFAULT '' AFTER `invoice_no`,
				ADD `date_added` datetime NOT NULL AFTER `order_status_id`,
				ADD `date_modified` datetime NOT NULL AFTER `date_added`";
			$this->db->query($suborderSql);

			$orderProductSql = "ALTER TABLE " . DB_PREFIX . "ms_order_product_data ADD `suborder_id` int(11) NOT NULL DEFAULT '0'";
			$this->db->query($orderProductSql);

			$sqlOrders = "SELECT * FROM " . DB_PREFIX . "order WHERE 1";
			$ordersData = $this->db->query($sqlOrders);
			foreach ($ordersData->rows as $row) {
				$order_id = $row['order_id'];
				$dateAdded = $row['date_added'];
				$dateModified = $row['date_modified'];
				$customerSql = "SELECT seller_id FROM " . DB_PREFIX . "ms_suborder WHERE order_id = " . (int)$order_id . " GROUP BY seller_id";
				$seller_ids = $this->db->query($customerSql)->rows;

				foreach ($seller_ids as $seller_id) {
					//$total = $this->MsLoader->MsOrderData->getOrderTotal($order_id, $seller_id);
					/*total = '" . $total . "'*/
					$sqlSubOrders = "UPDATE " . DB_PREFIX . "ms_suborder
					SET date_added = '" . $dateAdded . "',
					date_modified = '" . $dateModified . "'
					WHERE order_id = " . (int)$order_id . " AND seller_id = " . (int)$seller_id['seller_id'];
					$this->db->query($sqlSubOrders);
				}
			}

			$sqlSubOrders = "SELECT suborder_id, order_id, seller_id FROM " . DB_PREFIX . "ms_suborder WHERE 1";
			$subOrders = $this->db->query($sqlSubOrders)->rows;
			foreach ($subOrders as $subOrder) {
				$sqlOrderProduct = "UPDATE " . DB_PREFIX . "ms_order_product_data
					SET suborder_id = " . (int)$subOrder['suborder_id'] . " WHERE
					seller_id = " . (int)$subOrder['seller_id'] . " AND order_id = " . (int)$subOrder['order_id'];
				$this->db->query($sqlOrderProduct);
			}

			$this->_createSchemaEntry('1.0.4.0');
		}

		if (version_compare($version, '1.0.4.1') < 0) {
			$layout_id = $this->db->query("SELECT layout_id FROM " . DB_PREFIX . "layout WHERE `name` = 'Account'")->row['layout_id'];
			$sql = "INSERT INTO " . DB_PREFIX . "layout_module (`layout_id`, `code`, `position`, `sort_order`) VALUES($layout_id, 'account', 'column_left', 1);";
			$this->db->query($sql);

			$account = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code`='account' AND `key`='account_status'")->row;
			if (empty($account)) {
				$sql = "INSERT INTO " . DB_PREFIX . "setting SET `store_id` = 0, `code` = 'account', `key` = 'account_status', `value` = 1, `serialized` = 0";
			} else {
				$sql = "UPDATE " . DB_PREFIX . "setting SET `store_id` = 0, `code` = 'account', `key` = 'account_status', `value` = 1, `serialized` = 0 WHERE `setting_id` = " . (int)$account['setting_id'];
			}

			$this->db->query($sql);

			$this->_createSchemaEntry('1.0.4.1');
		}

		if (version_compare($version, '2.0.0.0') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_badge` (
			`badge_id` int(11) NOT NULL AUTO_INCREMENT,
			`image` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`badge_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_badge_description` (
			`badge_id` int(11) NOT NULL,
			`name` varchar(32) NOT NULL DEFAULT '',
			`description` text NOT NULL,
			`language_id` int(11) NOT NULL,
			PRIMARY KEY (`badge_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_badge_seller_group` (
			`badge_seller_group_id` INT(11) NOT NULL AUTO_INCREMENT,
			`badge_id` INT(11) NOT NULL,
			`seller_id` int(11) DEFAULT NULL,
			`seller_group_id` int(11) DEFAULT NULL,
			PRIMARY KEY (`badge_seller_group_id`)) default CHARSET=utf8");


			/* social links */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_channel` (
			`channel_id` int(11) NOT NULL AUTO_INCREMENT,
			`image` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`channel_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_channel_description` (
			`channel_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` VARCHAR(32) NOT NULL DEFAULT '',
			`description` TEXT NOT NULL DEFAULT '',
			PRIMARY KEY (`channel_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_channel` (
			`seller_id` int(11) NOT NULL,
			`channel_id` int(11) NOT NULL,
			`channel_value` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`seller_id`, `channel_id`)) default CHARSET=utf8");

			/* messaging */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_conversation` (
			`conversation_id` int(11) NOT NULL AUTO_INCREMENT,
			`product_id` int(11) DEFAULT NULL,
			`order_id` int(11) DEFAULT NULL,
			`title` varchar(256) NOT NULL DEFAULT '',
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`conversation_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_message` (
			`message_id` int(11) NOT NULL AUTO_INCREMENT,
			`conversation_id` int(11) NOT NULL,
			`from` int(11) DEFAULT NULL,
			`to` int(11) DEFAULT NULL,
			`message` text NOT NULL DEFAULT '',
			`read` tinyint(1) NOT NULL DEFAULT 0,
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`message_id`)) default CHARSET=utf8");

			$this->_createSchemaEntry('2.0.0.0');
		}

		if (version_compare($version, '2.0.0.1') < 0) {
			//Questions
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX ."ms_question` (
			`question_id` int(11) NOT NULL AUTO_INCREMENT,
			`author_id` int(11) NOT	NULL,
			`product_id` int(11) NOT NULL,
			`text` text NOT NULL DEFAULT '',
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`question_id`)) DEFAULT CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_answer` (
			`answer_id` int(11) NOT NULL AUTO_INCREMENT,
			`question_id` int(11) NOT NULL,
			`author_id` int(11) NOT NULL,
			`date_created` DATETIME NOT NULL,
			`rating` int(11) DEFAULT NULL,
			`text` text NOT NULL DEFAULT '',
			PRIMARY KEY (`answer_id`)) DEFAULT CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_user_vote` (
			`answer_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL,
			`type` tinyint(1) NOT NULL) DEFAULT CHARSET=utf8");

			/*Index part for questions */
			$this->db->query("
				CREATE UNIQUE INDEX user
				ON " . DB_PREFIX ."ms_user_vote (user_id, answer_id)");

			$this->db->query("
				CREATE INDEX question_id
				ON " . DB_PREFIX ."ms_answer (question_id)");

			$this->db->query("
				CREATE INDEX answer_id
				ON " . DB_PREFIX ."ms_answer (answer_id)");

			$this->db->query("
				CREATE INDEX product_id
				ON " . DB_PREFIX ."ms_question (product_id)");

			$this->_createSchemaEntry('2.0.0.1');
		}

		if(version_compare($version, '2.0.0.2') < 0) {
			// Ratings/reviews
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_review` (
			`review_id` int(11) NOT NULL AUTO_INCREMENT,
			`author_id` int(11) NOT NULL,
			`product_id` int(11) NOT NULL,
			`order_product_id` int(11) NOT NULL,
			`order_id` int(11) DEFAULT NULL,
			`rating` int(1) NOT NULL,
			`title` varchar(128) NOT NULL DEFAULT '',
			`comment` text NOT NULL DEFAULT '',
			`description_accurate` int(1) NOT NULL,
			`helpful` int(11) DEFAULT NULL,
			`unhelpful` int(11) DEFAULT NULL,
			`date_created` DATETIME NOT NULL,
			`date_updated` DATETIME DEFAULT NULL,
			`status` tinyint DEFAULT 0,
			PRIMARY KEY (`review_id`)) default CHARSET=utf8");

			$this->db->query("CREATE UNIQUE INDEX idx_ms_review_order_product ON `" . DB_PREFIX ."ms_review` (order_id, product_id, order_product_id)");

			// drop indexes
			$this->db->query("DROP INDEX `user` ON `" . DB_PREFIX ."ms_user_vote`");
			$this->db->query("DROP INDEX `question_id` ON `" . DB_PREFIX ."ms_answer`");
			$this->db->query("DROP INDEX `answer_id` ON `" . DB_PREFIX ."ms_answer`");
			$this->db->query("DROP INDEX `product_id` ON `" . DB_PREFIX ."ms_question`");

			$this->_createSchemaEntry('2.0.0.2');
		}

		if(version_compare($version, '2.0.0.3') < 0) {
			// Shipping methods
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_shipping_method` (
			`shipping_method_id` int(11) NOT NULL AUTO_INCREMENT,
			`logo` TEXT DEFAULT '',
			`status` tinyint(1) NOT NULL DEFAULT 0,
			PRIMARY KEY (`shipping_method_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_shipping_method_description` (
			`shipping_method_description_id` int(11) NOT NULL AUTO_INCREMENT,
			`shipping_method_id` int(11) NOT NULL,
			`name` VARCHAR(32) NOT NULL DEFAULT '',
			`description` TEXT DEFAULT '',
			`language_id` int(11) DEFAULT NULL,
			PRIMARY KEY (`shipping_method_description_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_shipping_delivery_time` (
			`delivery_time_id` int(11) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`delivery_time_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_shipping_delivery_time_description` (
			`delivery_time_desc_id` int(11) NOT NULL AUTO_INCREMENT,
			`delivery_time_id` int(11) NOT NULL,
			`name` TEXT DEFAULT '',
			`language_id` int(11) NOT NULL DEFAULT 1,
			PRIMARY KEY (`delivery_time_desc_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_product_shipping` (
			`product_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
			`product_id` int(11) NOT NULL,
			`from_country` int(11) NOT NULL DEFAULT 0,
			`free_shipping` int(11) NOT NULL DEFAULT 0,
			`processing_time` int(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (`product_shipping_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_product_shipping_location` (
			`product_shipping_location_id` int(11) NOT NULL AUTO_INCREMENT,
			`product_id` int(11) NOT NULL,
			`to_country` int(11) NOT NULL,
			`shipping_method_id` int(11) NOT NULL DEFAULT 0,
			`delivery_time_id` int(11) NOT NULL DEFAULT 1,
			`cost` DECIMAL(15,4) NOT NULL,
			`additional_cost` DECIMAL(15,4) NOT NULL,
			PRIMARY KEY (`product_shipping_location_id`)) default CHARSET=utf8");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_order_product_data` 
			ADD `shipping_location_id` int(11) DEFAULT NULL,
			ADD `shipping_cost` DECIMAL(15,4) DEFAULT NULL");

			// MM Order total module install
			$this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = 'total', `code` = 'mm_shipping_total'");

			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('mm_shipping_total', array(
				'mm_shipping_total_status' => 1,
				'mm_shipping_total_sort_order' => 1
			));

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/shipping-method');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/shipping-method');

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/mm_shipping_total');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/mm_shipping_total');

			$this->_createSchemaEntry('2.0.0.3');
		}

		if(version_compare($version, '2.0.0.4') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_product_shipping_location`
			CHANGE `to_country` `to_geo_zone_id` int(11) NOT NULL;");

			$this->_createSchemaEntry('2.0.0.4');
		}

		if(version_compare($version, '2.0.0.5') < 0) {
			// Create new table for order product shipping data
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_order_product_shipping_data` (
			`order_product_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`product_id` int(11) NOT NULL,
			`order_product_id` int(11) NOT NULL,
			`shipping_location_id` int(11) DEFAULT NULL,
			`shipping_cost` DECIMAL(15,4) DEFAULT NULL,
			PRIMARY KEY (`order_product_shipping_id`)) default CHARSET=utf8");

			// Get shipping data from oc_ms_order_product_data
			$sql = "SELECT
						order_id,
						product_id,
						order_product_id,
						shipping_location_id,
						shipping_cost
					FROM " . DB_PREFIX . "ms_order_product_data";
			$old_shipping_data = $this->db->query($sql);

			foreach ($old_shipping_data->rows as $row) {
				$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_order_product_shipping_data
				SET
					order_id = " . (int)$row['order_id'] . ",
					product_id = " . (int)$row['product_id'] . ",
					order_product_id = " . (int)$row['order_product_id'] . ",
					shipping_location_id = " . (is_null($row['shipping_location_id']) ? "NULL" : (int)$row['shipping_location_id']) . ",
					shipping_cost = " . (is_null($row['shipping_cost']) ? "NULL" : (float)$row['shipping_cost']));
			}

			// Drop shipping columns from ms_order_product_data
			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_order_product_data
			DROP COLUMN shipping_location_id,
			DROP COLUMN shipping_cost;");

			$this->_createSchemaEntry('2.0.0.5');
		}

		if(version_compare($version, '2.0.1.0') < 0) {
			// Copy everything from oc_ms_payment to oc_ms_pg_payment and oc_ms_pg_request. And then delete oc_ms_payment
			/* Payment gateways, requests and payments */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_pg_payment` (
			`payment_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) NOT NULL,
			`payment_type` int(11) NOT NULL,
			`payment_code` VARCHAR(128) NOT NULL,
			`payment_status` int(11) NOT NULL,
			`amount` DECIMAL(15,4) NOT NULL,
			`currency_id` int(11) NOT NULL,
			`currency_code` VARCHAR(3) NOT NULL,
			`sender_data` TEXT NOT NULL,
			`receiver_data` TEXT NOT NULL,
			`description` TEXT NOT NULL,
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`payment_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_pg_request` (
			`request_id` int(11) NOT NULL AUTO_INCREMENT,
			`payment_id` VARCHAR(128) DEFAULT NULL,
			`seller_id` int(11) NOT NULL,
			`product_id` int(11) DEFAULT NULL,
			`order_id` int(11) DEFAULT NULL,
			`request_type` int(11) NOT NULL,
			`request_status` int(11) NOT NULL,
			`description` TEXT NOT NULL,
			`amount` DECIMAL(15,4) NOT NULL,
			`currency_id` int(11) NOT NULL,
			`currency_code` VARCHAR(3) NOT NULL,
			`date_created` DATETIME NOT NULL,
			`date_modified` DATETIME DEFAULT NULL,
			PRIMARY KEY (`request_id`)) default CHARSET=utf8");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/payment-gateway');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/payment-gateway');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/payment-request');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/payment-request');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/payment');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/payment');

			/**
			 * Backward compatibility for payments
			*/
			$old_payments = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_payment`");

			// Old payment types => new payment types
			$payment_types = array(
				'1' => MsPgPayment::TYPE_PAID_REQUESTS, // Signup fee => Paid requests
				'2' => MsPgPayment::TYPE_PAID_REQUESTS, // Product listing fee => Paid requests
				'3' => MsPgPayment::TYPE_PAID_REQUESTS, // Payout => Paid requests
				'4' => MsPgPayment::TYPE_PAID_REQUESTS, // Payout request => Paid requests
				'5' => MsPgPayment::TYPE_PAID_REQUESTS, // Recurring => Paid requests
				'6' => MsPgPayment::TYPE_SALE  			// Sales => Sale
			);

			// Payment method => new payment code
			$payment_codes = array(
				'1' => NULL, // Balance
				'2' => 'ms_pg_paypal', // PayPal
				'3' => 'ms_pp_adaptive' // PayPal Adaptive payments
			);

			foreach ($old_payments->rows as $old_payment_data) {
				// If record is balance related, skip it
				if(is_null($payment_codes[$old_payment_data['payment_method']])) continue;

				// If payment type is Payout, set seller_id = 0 that means payment was created by admin.
				// Else seller_id means id of a target participant of payment
				$payment_seller_id = ($old_payment_data['payment_type'] == 3 ? 0 :(int)$old_payment_data['seller_id']);

				// Payment method => description
				$receiver_data = array(
					'2' => array($old_payment_data['seller_id'] => array('pp_address' => $old_payment_data['payment_data'])),
					'3' => array('pp_address' => $old_payment_data['payment_data'])
				);

				// Create new record in oc_ms_pg_payment
				$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_pg_payment
				SET
					seller_id = '" . (int)$payment_seller_id . "',
					payment_type = " . (int)$payment_types[$old_payment_data['payment_type']] . ",
					payment_code = '" . $this->db->escape($payment_codes[$old_payment_data['payment_method']]) . "',
					payment_status = " . (int)$old_payment_data['payment_status'] . ",
					amount = " . (float)$old_payment_data['amount'] . ",
					currency_id = " . (int)$old_payment_data['currency_id'] . ",
					currency_code = '" . $this->db->escape($old_payment_data['currency_code']) . "',
					sender_data = '" . json_encode(array()) . "',
					receiver_data = '" . json_encode($receiver_data[$old_payment_data['payment_method']]) . "',
					description = '" . json_encode(array()) . "',
					date_created = '" . $this->db->escape($old_payment_data['date_created']) . "'
				");

				$payment_id = $this->db->getLastId();

				// Create new record in oc_ms_pg_request
				$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_pg_request
				SET
					payment_id = '" . (int)$payment_id . "',
					seller_id = " . (int)$old_payment_data['seller_id'] . ",
					product_id = " . (isset($old_payment_data['product_id']) ? (int)$old_payment_data['product_id'] : 'NULL') . ",
					order_id = " . (isset($old_payment_data['order_id']) ? (int)$old_payment_data['order_id'] : 'NULL') . ",
					request_type = " . (int)$old_payment_data['payment_type'] . ",
					request_status = " . (int)$old_payment_data['payment_status'] . ",
					description = '" . $this->db->escape($old_payment_data['description']) . "',
					amount = " . (float)$old_payment_data['amount'] . ",
					currency_id = " . (int)$old_payment_data['currency_id'] . ",
					currency_code = '" . $this->db->escape($old_payment_data['currency_code']) . "',
					date_created = '" . $this->db->escape($old_payment_data['date_created']) . "',
					date_modified = " . (is_null($old_payment_data['date_paid']) ? 'NULL' : ("'" . $this->db->escape($old_payment_data['date_paid']) . "'")) . "
				");

				$request_id = $this->db->getLastId();

				// Payment method => description
				$description = array(
					$request_id => $old_payment_data['description']
				);

				// Update Payment record
				$this->db->query("UPDATE " . DB_PREFIX . "ms_pg_payment SET description = '" . json_encode($description) . "'	WHERE payment_id = '" . $payment_id . "'");
			}

			// Drop oc_ms_payment
			$this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "ms_payment");

			/**
			 * Update paypal information for each seller
			 */
			$sellers = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_seller`");
			foreach ($sellers as $seller_data) {
				if(isset($seller_data['paypal']) && $seller_data['paypal']) {
					$pp_setting_name = 'slr_pg_paypal_pp_address';

					$this->db->query("
					INSERT INTO `" . DB_PREFIX . "ms_setting`
					SET
						seller_id = " . $seller_data['seller_id'] . ",
						seller_group_id = " . $seller_data['seller_group'] . ",
						name = '" . $pp_setting_name . "', 
						value = '" . $this->db->escape($seller_data['paypal']) . "',
						is_encoded = NULL
					ON DUPLICATE KEY UPDATE
						value = '" . $this->db->escape($seller_data['paypal']) . "'
					");
				}
			}

			// Drop column `paypal` from oc_ms_seller
			$this->db->query("ALTER TABLE " . DB_PREFIX . "ms_seller DROP COLUMN paypal;");

			$this->_createSchemaEntry('2.0.1.0');
		}

		if(version_compare($version, '2.1.0.0') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_shipping` (
			`seller_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) NOT NULL,
			`from_geo_zone_id` int(11) NOT NULL,
			`processing_time` int(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (`seller_shipping_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_shipping_location` (
			`seller_shipping_location_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_shipping_id` int(11) NOT NULL,
			`shipping_method_id` int(11) NOT NULL,
			`delivery_time_id` int(11) NOT NULL,
			`to_geo_zone_id` int(11) NOT NULL,
			`weight_from` DECIMAL(15,4) NOT NULL,
			`weight_to` DECIMAL(15,4) NOT NULL,
			`weight_class_id` int(11) NOT NULL,
			`cost` DECIMAL(15,4) NOT NULL,
			PRIMARY KEY (`seller_shipping_location_id`)) default CHARSET=utf8");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_order_product_shipping_data`
			CHANGE `shipping_location_id` `fixed_shipping_method_id` int(11) NOT NULL");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_order_product_shipping_data`
			ADD `combined_shipping_method_id` int(11) DEFAULT NULL AFTER `fixed_shipping_method_id`");

			$this->_createSchemaEntry('2.1.0.0');
		}

		if(version_compare($version, '2.1.0.1') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_product_shipping`
			ADD `override` int(11) DEFAULT 0");

			$this->_createSchemaEntry('2.1.0.1');
		}

		if(version_compare($version, '2.2.0.0') < 0) {
			// Category and product based commissions
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_commission` (
			`category_commission_id` int(11) NOT NULL AUTO_INCREMENT,
			`category_id` int(11) NOT NULL,
			`commission_id` int(11) DEFAULT NULL,
			PRIMARY KEY (`category_commission_id`)) default CHARSET=utf8");

			$this->db->query("CREATE UNIQUE INDEX cat_id ON " . DB_PREFIX ."ms_category_commission (category_id)");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_product`
			ADD `commission_id` int(11) DEFAULT NULL");

			// Multi-language seller description
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_description` (
			`seller_description_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`description` text DEFAULT '',
			PRIMARY KEY (`seller_description_id`)) default CHARSET=utf8");

			// Add seller descriptions from ms_seller to ms_seller_description
			$getDataQuery = "SELECT seller_id, description FROM " . DB_PREFIX . "ms_seller";
			$seller_data = $this->db->query($getDataQuery)->rows;

			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();

			foreach ($seller_data as $row) {
				foreach ($languages as $code => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_seller_description
					SET seller_id = " . (int)$row['seller_id'] . ",
						language_id = " . (int)$language['language_id'] . ",
						description = '" . $this->db->escape($row['description']) . "'");
				}
			}

			// Refactor seller's and seller's group settings structure
			$this->db->query("ALTER TABLE " . DB_PREFIX . "ms_setting DROP COLUMN seller_group_id;");
			$this->db->query("RENAME TABLE `" . DB_PREFIX . "ms_setting` TO `" . DB_PREFIX . "ms_seller_setting`");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_group_setting` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`seller_group_id` int(11) unsigned DEFAULT NULL,
			`name` varchar(50) DEFAULT NULL,
			`value` varchar(250) DEFAULT NULL,
			`is_encoded` smallint(1) unsigned DEFAULT NULL,
			PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;");

			$this->db->query("CREATE UNIQUE INDEX slr_gr_id_name ON " . DB_PREFIX ."ms_seller_group_setting (seller_group_id, name)");

			// Set default fee priority
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_fee_priority',
				'value' => 2
			));

			$this->_createSchemaEntry('2.2.0.0');
		}

		if(version_compare($version, '2.2.0.1') < 0) {
			// cleanup duplicate descriptions
			$getDataQuery = "SELECT * FROM " . DB_PREFIX . "ms_seller_description ORDER BY seller_description_id DESC";
			$description_data = $this->db->query($getDataQuery)->rows;

			$seller_descriptions = array();
			$removeids = array();

			foreach ($description_data as $d) {
				$seller_id = $d['seller_id'];
				$language_id = $d['language_id'];
				$description = $d['description'];
				if (!isset($seller_descriptions[$seller_id][$language_id])) {
					$seller_descriptions[$seller_id][$language_id] = $description;
				} else {
					$removeids[] = $d['seller_description_id'];
				}
			}

			foreach ($removeids as $id) {
				$sql = "DELETE FROM " . DB_PREFIX . "ms_seller_description WHERE seller_description_id = ".(int)$id;
				$this->db->query($sql);
			}

			$this->db->query("ALTER TABLE `" . DB_PREFIX . "ms_seller_description` ADD UNIQUE `seller_language_id`(`seller_id`, `language_id`)");
			$this->_createSchemaEntry('2.2.0.1');
		}

		if(version_compare($version, '2.2.0.2') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_seller_shipping`
			CHANGE `from_geo_zone_id` `from_country_id` int(11) NOT NULL;");

			$this->_createSchemaEntry('2.2.0.2');
		}

		if(version_compare($version, '2.2.0.3') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_seller_shipping_location`
			CHANGE `cost` `cost_fixed` DECIMAL(15,4) NOT NULL;");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_seller_shipping_location`
			ADD (`cost_pwu` DECIMAL(15,4) NOT NULL DEFAULT 0);");

			$this->_createSchemaEntry('2.2.0.3');
		}

		if(version_compare($version, '2.2.0.4') < 0) {
			if($this->config->get('config_weight_class_id')) {
				$weights_to_convert = $this->db->query("SELECT seller_shipping_location_id, weight_from, weight_to, weight_class_id FROM `" . DB_PREFIX . "ms_seller_shipping_location`;");

				if($weights_to_convert->num_rows) {
					foreach ($weights_to_convert->rows as $row) {
						$weight_from_converted = $this->weight->convert($row['weight_from'], $row['weight_class_id'], $this->config->get('config_weight_class_id'));
						$weight_to_converted = $this->weight->convert($row['weight_to'], $row['weight_class_id'], $this->config->get('config_weight_class_id'));

						$this->db->query("UPDATE `" . DB_PREFIX . "ms_seller_shipping_location`
							SET weight_from = " . (float)$weight_from_converted . ",
								weight_to = " . (float)$weight_to_converted . ",
								weight_class_id = " . (int)$this->config->get('config_weight_class_id') ."
							WHERE seller_shipping_location_id = " . (int)$row['seller_shipping_location_id']);
					}
				}
			}

			$this->_createSchemaEntry('2.2.0.4');
		}

		if(version_compare($version, '2.3.0.0') < 0) {
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute_description`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute_value`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute_value_description`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute_attribute`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_product_attribute`");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_attribute` (
			`attribute_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) DEFAULT 0,
			`attribute_status` int(11) NOT NULL,
			PRIMARY KEY (`attribute_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_attribute_group` (
			`attribute_group_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) DEFAULT 0,
			`attribute_group_status` int(11) NOT NULL,
			PRIMARY KEY (`attribute_group_id`)) default CHARSET=utf8");

			$this->_createSchemaEntry('2.3.0.0');
		}

		if(version_compare($version, '2.3.0.1') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_option` (
			`option_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) DEFAULT 0,
			`option_status` int(11) NOT NULL,
			PRIMARY KEY (`option_id`)) default CHARSET=utf8");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/option');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/option');

			$this->_createSchemaEntry('2.3.0.1');
		}

		if(version_compare($version, '2.3.0.2') < 0) {
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allow_seller_attributes',
				'value' => 0
			));

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allow_seller_options',
				'value' => 0
			));

			$this->_createSchemaEntry('2.3.0.2');
		}

		if(version_compare($version, '2.3.0.3') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_customer_ppakey` (
			`customer_id` int(11) NOT NULL,
            `preapprovalkey` varchar(255) NOT NULL,
            `active` smallint(1) NOT NULL DEFAULT '0',
             PRIMARY KEY (`customer_id`)
			) DEFAULT CHARSET=utf8;");

			$this->_createSchemaEntry('2.3.0.3');
		}

		if(version_compare($version, '2.3.0.4') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_to_order (
  			`order_id` int(11) NOT NULL,
  			`suborder_id` int(11) NOT NULL,
  			`conversation_id` int(11) NOT NULL,
 			PRIMARY KEY (`order_id`,`suborder_id`,`conversation_id`))
			default CHARSET=utf8");

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_conversation DROP COLUMN order_id;");

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_conversation
			ADD conversation_from INT(11) DEFAULT NULL");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_participants (
  			`conversation_id` int(11) NOT NULL,
  			`customer_id` int(11) NOT NULL DEFAULT '0',
  			`user_id` int(11) NOT NULL DEFAULT '0',
 			PRIMARY KEY (`conversation_id`,`customer_id`,`user_id`))
			default CHARSET=utf8");

			$conversation_data = $this->db->query("
			SELECT conv.*,
			(SELECT msm.from  FROM `" . DB_PREFIX . "ms_message` as msm WHERE msm.conversation_id = conv.conversation_id ORDER BY message_id ASC LIMIT 1) as data_conversation_from
			FROM " . DB_PREFIX . "ms_conversation conv
			");

			if ($conversation_data->num_rows){
				foreach ($conversation_data->rows as $data){
					$this->db->query("UPDATE " . DB_PREFIX . "ms_conversation SET
					conversation_from = '" . (int)$data["data_conversation_from"] . "'
					WHERE conversation_id = '" .  (int)$data["conversation_id"] . "'");
				}
			}

			$this->_createSchemaEntry('2.3.0.4');
		}

		if(version_compare($version, '2.3.0.5') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/conversation');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/conversation');

			$this->db->query("
			CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_to_product (
  			`product_id` int(11) NOT NULL,
  			`conversation_id` int(11) NOT NULL,
 			PRIMARY KEY (`product_id`,`conversation_id`))
			default CHARSET=utf8");

			$conversation_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_conversation conv WHERE product_id IS NOT NULL");
			if ($conversation_data->num_rows){
				foreach ($conversation_data->rows as $data){
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_conversation_to_product SET
					product_id = '" . (int)$data["product_id"] . "',
					conversation_id = '" . (int)$data["conversation_id"] . "'
					");
				}
			}

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_conversation DROP COLUMN product_id;");

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_message DROP COLUMN `to`;");

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_message
			ADD from_admin INT(1) DEFAULT 0");

			$this->_createSchemaEntry('2.3.0.5');
		}

		if(version_compare($version, '2.3.0.6') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_message_upload (
  			`message_id` int(11) NOT NULL,
  			`upload_id` int(11) NOT NULL,
			PRIMARY KEY (`message_id`, `upload_id`))
			default CHARSET=utf8");

			$this->_createSchemaEntry('2.3.0.6');
		}

		if(version_compare($version, '2.4.0.0') < 0) {
			/* Seller categories */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category` (
			`category_id` int(11) NOT NULL AUTO_INCREMENT,
			`parent_id` int(11) NOT NULL DEFAULT 0,
			`seller_id` int(11) NOT NULL DEFAULT 0,
			`image` VARCHAR(255) DEFAULT NULL,
			`sort_order` int(11) NOT NULL DEFAULT 0,
			`category_status` int(11) NOT NULL,
			PRIMARY KEY (`category_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_description` (
			`category_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` VARCHAR(255) NOT NULL DEFAULT '',
			`description` TEXT NOT NULL DEFAULT '',
			`meta_title` VARCHAR(255) NOT NULL DEFAULT '',
			`meta_description` VARCHAR(255) NOT NULL DEFAULT '',
			`meta_keyword` VARCHAR(255) NOT NULL DEFAULT '',
			PRIMARY KEY (`category_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_filter` (
			`category_id` int(11) NOT NULL,
			`oc_filter_id` int(11) NOT NULL,
			PRIMARY KEY (`category_id`, `oc_filter_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_to_store` (
			`category_id` int(11) NOT NULL,
			`store_id` int(11) NOT NULL,
			PRIMARY KEY (`category_id`, `store_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_path` (
			`category_id` int(11) NOT NULL,
			`path_id` int(11) NOT NULL,
			`level` int(11) NOT NULL,
			PRIMARY KEY (`category_id`, `path_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_product_to_category` (
			`product_id` int(11) NOT NULL,
			`ms_category_id` int(11) NOT NULL,
			PRIMARY KEY (`product_id`, `ms_category_id`)) default CHARSET=utf8");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/category');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/category');

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allow_seller_categories',
				'value' => 0
			));

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_product_category_type',
				'value' => 1
			));

			$this->_createSchemaEntry('2.4.0.0');
		}

		if(version_compare($version, '2.4.0.1') < 0) {
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_msg_allowed_file_types',
				'value' => 'png,jpg,jpeg,zip,rar,pdf'
			));

			$this->_createSchemaEntry('2.4.0.1');
		}
	}
}