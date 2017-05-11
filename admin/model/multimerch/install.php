<?php
class ModelMultimerchInstall extends Model {
	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model('localisation/language');
		$this->load->model('extension/extension');
		$this->load->model('extension/module');
	}
	
	public function createSchema() {
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_db_schema` (
		`schema_change_id` int(11) NOT NULL AUTO_INCREMENT,
		`major` TINYINT NOT NULL,
		`minor` TINYINT NOT NULL,
		`build` TINYINT NOT NULL,
		`revision` SMALLINT NOT NULL,
		`date_applied` DATETIME NOT NULL,
		PRIMARY KEY (`schema_change_id`)) default CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_commission` (
		`commission_id` int(11) NOT NULL AUTO_INCREMENT,
		PRIMARY KEY (`commission_id`)) default CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_commission_rate` (
		`rate_id` int(11) NOT NULL AUTO_INCREMENT,
		`rate_type` int(11) NOT NULL,
		`commission_id` int(11) NOT NULL,
		`flat` DECIMAL(15,4),
		`percent` DECIMAL(15,2),
		`payment_method` TINYINT DEFAULT NULL,
		PRIMARY KEY (`rate_id`)) default CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_group` (
		`seller_group_id` int(11) NOT NULL AUTO_INCREMENT,
		`commission_id` int(11) DEFAULT NULL,
		`product_period` int(5) DEFAULT 0,
		`product_quantity` int(5) DEFAULT 0,
		PRIMARY KEY (`seller_group_id`)) default CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_group_description` (
		`seller_group_description_id` int(11) NOT NULL AUTO_INCREMENT,
		`seller_group_id` int(11) NOT NULL,
		`name` VARCHAR(32) NOT NULL DEFAULT '',
		`description` TEXT NOT NULL DEFAULT '',
		`language_id` int(11) DEFAULT NULL,
		PRIMARY KEY (`seller_group_description_id`)) default CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_product` (
		`product_id` int(11) NOT NULL,
		`seller_id` int(11) DEFAULT NULL,
		`product_status` TINYINT NOT NULL,
		`product_approved` TINYINT NOT NULL,
		`list_until` DATE DEFAULT NULL,
		`commission_id` int(11) DEFAULT NULL,
		PRIMARY KEY (`product_id`)) default CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller` (
		`seller_id` int(11) NOT NULL AUTO_INCREMENT,
		`nickname` VARCHAR(32) NOT NULL DEFAULT '',
		`company` VARCHAR(32) NOT NULL DEFAULT '',
		`website` VARCHAR(2083) NOT NULL DEFAULT '',
		`description` TEXT NOT NULL DEFAULT '',
		`country_id` INT(11) NOT NULL DEFAULT '0',
		`zone_id` INT(11) NOT NULL DEFAULT '0',
		`avatar` VARCHAR(255) DEFAULT NULL,
		`banner` VARCHAR(255) DEFAULT NULL,
		`date_created` DATETIME NOT NULL,
		`seller_status` TINYINT NOT NULL,
		`seller_approved` TINYINT NOT NULL,
		`product_validation` tinyint(4) NOT NULL DEFAULT '1',
		`seller_group` int(11) NOT NULL DEFAULT '1',
		`commission_id` int(11) DEFAULT NULL,
		PRIMARY KEY (`seller_id`)) default CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_description` (
		`seller_description_id` int(11) NOT NULL AUTO_INCREMENT,
		`seller_id` int(11) NOT NULL,
		`language_id` int(11) NOT NULL,
		`description` text DEFAULT '',
		PRIMARY KEY (`seller_description_id`),
		UNIQUE KEY `seller_language_id` (`seller_id`,`language_id`)) DEFAULT CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_balance` (
		`balance_id` int(11) NOT NULL AUTO_INCREMENT,
		`seller_id` int(11) NOT NULL,
		`order_id` int(11) DEFAULT NULL,
		`product_id` int(11) DEFAULT NULL,
		`order_product_id` int(11) DEFAULT NULL,
		`withdrawal_id` int(11) DEFAULT NULL,
		`balance_type` int(11) DEFAULT NULL,
		`amount` DECIMAL(15,4) NOT NULL,
		`balance` DECIMAL(15,4) NOT NULL,
		`description` TEXT NOT NULL DEFAULT '',
		`date_created` DATETIME NOT NULL,
		`date_modified` DATETIME DEFAULT NULL,
		PRIMARY KEY (`balance_id`)) default CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_order_product_data` (
		`order_product_data_id` int(11) NOT NULL AUTO_INCREMENT,
		`order_id` int(11) NOT NULL,
		`product_id` int(11) NOT NULL,
		`order_product_id` int(11) DEFAULT NULL,
		`seller_id` int(11) DEFAULT NULL,
		`store_commission_flat` DECIMAL(15,4) NOT NULL,
		`store_commission_pct` DECIMAL(15,4) NOT NULL,
		`seller_net_amt` DECIMAL(15,4) NOT NULL,
		`suborder_id` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`order_product_data_id`)) default CHARSET=utf8");
	
		// ms_criteria - criterias table
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_criteria` (
		`criteria_id` int(11) NOT NULL AUTO_INCREMENT,
		`criteria_type` TINYINT NOT NULL,
		`range_id` int(11) NOT NULL,
		PRIMARY KEY (`criteria_id`)) default CHARSET=utf8");
	
		// ms_range_int - int criteria range table
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_range_int` (
		`range_id` int(11) NOT NULL AUTO_INCREMENT,
		`from` int(11) NOT NULL,
		`to` int(11) NOT NULL,
		PRIMARY KEY (`range_id`)) default CHARSET=utf8");
	
		// ms_range_decimal - decimal criteria range table
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_range_decimal` (
		`range_id` int(11) NOT NULL AUTO_INCREMENT,
		`from` DECIMAL(15,4) NOT NULL,
		`to` DECIMAL(15,4) NOT NULL,
		PRIMARY KEY (`range_id`)) default CHARSET=utf8");
	
		// ms_range_periodic - periodic criteria range table
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_range_date` (
		`range_id` int(11) NOT NULL AUTO_INCREMENT,
		`from` DATETIME,
		`to` DATETIME NOT NULL,
		PRIMARY KEY (`range_id`)) default CHARSET=utf8");
	
		// ms_seller_group_criteria - table, which connects concrete commissions for criterias in the seller groups
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_group_criteria` (
		`seller_group_criteria_id` int(11) NOT NULL AUTO_INCREMENT,
		`commission_id` int(11) NOT NULL,
		`criteria_id` int(11) NOT NULL,
		PRIMARY KEY (`seller_group_criteria_id`)) default CHARSET=utf8");
	
		// ms_return - seller's products returns
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_return` (
		`return_id` int(11) NOT NULL AUTO_INCREMENT,
		`order_id` int(11) NOT NULL,
		`suborder_id` int(11) NOT NULL,
		`seller_id` int(11) NOT NULL,
		`customer_id` int(11) DEFAULT 0 NOT NULL,
		`date_created` DATETIME NOT NULL,
		PRIMARY KEY (`return_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_return_product` (
		`return_product_id` int(11) NOT NULL AUTO_INCREMENT,
		`return_id` int(11) NOT NULL,
		`product_id` int(11) NOT NULL,
		`order_product_id` int(11) NOT NULL,
		`product_quantity` int(4) NOT NULL,
		`return_reason_id` int(11) NOT NULL,
		`return_action_id` int(11) NOT NULL,
		PRIMARY KEY (`return_product_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_return_history` (
		`return_history_id` int(11) NOT NULL AUTO_INCREMENT,
		`return_id` int(11) NOT NULL,
		`return_status_id` int(11) NOT NULL,
		`seller_comment` TEXT NULL DEFAULT '',
		`customer_comment` TEXT NULL DEFAULT '',
		`date_created` DATETIME NOT NULL,
		PRIMARY KEY (`return_history_id`)) default CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_order_comment` (
		`order_comment_id` int(11) NOT NULL AUTO_INCREMENT,
		`order_id` int(11) NOT NULL,
		`product_id` int(11) NOT NULL,
		`seller_id` int(11) NOT NULL,
		`comment` text NOT NULL,
		PRIMARY KEY (`order_comment_id`)
		) DEFAULT CHARSET=utf8");
	
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_suborder` (
		`suborder_id` int(11) NOT NULL AUTO_INCREMENT,
		`order_id` int(11) NOT NULL,
		`seller_id` int(11) NOT NULL,
		`invoice_no` int(11) NOT NULL DEFAULT '0',
		`invoice_prefix` varchar(26) NOT NULL DEFAULT '',
		`order_status_id` int(11) NOT NULL,
		`date_added` datetime NOT NULL,
		`date_modified` datetime NOT NULL,
		PRIMARY KEY (`suborder_id`)
		) DEFAULT CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_suborder_history` (
		`suborder_history_id` int(5) NOT NULL AUTO_INCREMENT,
		`suborder_id` int(5) NOT NULL,
		`order_status_id` int(5) NOT NULL,
		`comment` text NOT NULL DEFAULT '',
		`date_added` datetime NOT NULL,
		PRIMARY KEY (`suborder_history_id`)
		) DEFAULT CHARSET=utf8");
		
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_setting` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`seller_id` int(11) unsigned DEFAULT NULL,
		`name` varchar(50) DEFAULT NULL,
		`value` varchar(250) DEFAULT NULL,
		`is_encoded` smallint(1) unsigned DEFAULT NULL,
		PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8;");

		$this->db->query("CREATE UNIQUE INDEX slr_id_name ON " . DB_PREFIX ."ms_seller_setting (seller_id, name)");

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

		/* xt */
		/* badges */
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ms_badge` (
		`badge_id` int(11) NOT NULL AUTO_INCREMENT,
		`image` varchar(255) DEFAULT NULL,
		PRIMARY KEY (`badge_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ms_badge_description` (
		`badge_id` int(11) NOT NULL,
		`name` varchar(32) NOT NULL DEFAULT '',
		`description` text NOT NULL,
		`language_id` int(11) NOT NULL,
		PRIMARY KEY (`badge_id`, `language_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ms_badge_seller_group` (
		`badge_seller_group_id` INT(11) NOT NULL AUTO_INCREMENT,
		`badge_id` INT(11) NOT NULL,
		`seller_id` int(11) DEFAULT NULL,
		`seller_group_id` int(11) DEFAULT NULL,
		PRIMARY KEY (`badge_seller_group_id`)) default CHARSET=utf8");


		/* social links */
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ms_channel` (
		`channel_id` int(11) NOT NULL AUTO_INCREMENT,
		`image` varchar(255) DEFAULT NULL,
		PRIMARY KEY (`channel_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ms_channel_description` (
		`channel_id` int(11) NOT NULL,
		`language_id` int(11) NOT NULL,
		`name` VARCHAR(32) NOT NULL DEFAULT '',
		`description` TEXT NOT NULL DEFAULT '',
		PRIMARY KEY (`channel_id`, `language_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ms_seller_channel` (
		`seller_id` int(11) NOT NULL,
		`channel_id` int(11) NOT NULL,
		`channel_value` varchar(255) DEFAULT NULL,
		PRIMARY KEY (`seller_id`, `channel_id`)) default CHARSET=utf8");

		/* messaging */
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_conversation` (
		`conversation_id` int(11) NOT NULL AUTO_INCREMENT,
		`conversation_from` int(11) DEFAULT NULL,
		`title` varchar(256) NOT NULL DEFAULT '',
		`date_created` DATETIME NOT NULL,
		PRIMARY KEY (`conversation_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_message` (
		`message_id` int(11) NOT NULL AUTO_INCREMENT,
		`conversation_id` int(11) NOT NULL,
		`from` int(11) DEFAULT NULL,
		`from_admin` tinyint(1) NOT NULL DEFAULT 0,
		`message` text NOT NULL DEFAULT '',
		`read` tinyint(1) NOT NULL DEFAULT 0,
		`date_created` DATETIME NOT NULL,
		PRIMARY KEY (`message_id`)) default CHARSET=utf8");

		/* questions */
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

		/* Ratings and reviews */
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

		/* Shipping methods */
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
		`override` int(11) DEFAULT 0,
		PRIMARY KEY (`product_shipping_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_product_shipping_location` (
		`product_shipping_location_id` int(11) NOT NULL AUTO_INCREMENT,
		`product_id` int(11) NOT NULL,
		`to_geo_zone_id` int(11) NOT NULL,
		`shipping_method_id` int(11) NOT NULL DEFAULT 0,
		`delivery_time_id` int(11) NOT NULL DEFAULT 1,
		`cost` DECIMAL(15,4) NOT NULL,
		`additional_cost` DECIMAL(15,4) NOT NULL,
		PRIMARY KEY (`product_shipping_location_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_order_product_shipping_data` (
		`order_product_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
		`order_id` int(11) NOT NULL,
		`product_id` int(11) NOT NULL,
		`order_product_id` int(11) NOT NULL,
		`fixed_shipping_method_id` int(11) DEFAULT NULL,
		`combined_shipping_method_id` int(11) DEFAULT NULL,
		`shipping_cost` DECIMAL(15,4) DEFAULT NULL,
		PRIMARY KEY (`order_product_shipping_id`)) default CHARSET=utf8");

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

		/* Weight-based combined shipping */
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_shipping` (
		`seller_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
		`seller_id` int(11) NOT NULL,
		`from_country_id` int(11) NOT NULL,
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
		`cost_fixed` DECIMAL(15,4) NOT NULL,
		`cost_pwu` DECIMAL(15,4) NOT NULL DEFAULT 0,
		PRIMARY KEY (`seller_shipping_location_id`)) default CHARSET=utf8");

		/* Category-based fees */
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_commission` (
		`category_commission_id` int(11) NOT NULL AUTO_INCREMENT,
		`category_id` int(11) NOT NULL,
		`commission_id` int(11) DEFAULT NULL,
		PRIMARY KEY (`category_commission_id`)) default CHARSET=utf8");

		$this->db->query("CREATE UNIQUE INDEX cat_id ON " . DB_PREFIX ."ms_category_commission (category_id)");

		/* Seller attributes */
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

		/* Seller options */
		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_option` (
		`option_id` int(11) NOT NULL AUTO_INCREMENT,
		`seller_id` int(11) DEFAULT 0,
		`option_status` int(11) NOT NULL,
		PRIMARY KEY (`option_id`)) default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_customer_ppakey` (
		`customer_id` int(11) NOT NULL,
        `preapprovalkey` varchar(255) NOT NULL,
        `active` smallint(1) NOT NULL DEFAULT '0',
         PRIMARY KEY (`customer_id`)) DEFAULT CHARSET=utf8;");

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

		/* Messaging */
		$this->db->query("
		CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_to_order (
		`order_id` int(11) NOT NULL,
		`suborder_id` int(11) NOT NULL,
		`conversation_id` int(11) NOT NULL,
		PRIMARY KEY (`order_id`,`suborder_id`,`conversation_id`))
		default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_participants (
		`conversation_id` int(11) NOT NULL,
		`customer_id` int(11) NOT NULL DEFAULT '0',
		`user_id` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`conversation_id`,`customer_id`,`user_id`))
		default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_to_product (
		`product_id` int(11) NOT NULL,
		`conversation_id` int(11) NOT NULL,
		PRIMARY KEY (`product_id`,`conversation_id`))
		default CHARSET=utf8");

		$this->db->query("
		CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_message_upload (
		`message_id` int(11) NOT NULL,
		`upload_id` int(11) NOT NULL,
		PRIMARY KEY (`message_id`, `upload_id`))
		default CHARSET=utf8");
	}
	
	public function createData() {
		$schema = explode(".", $this->MsLoader->dbVer);
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_db_schema (major, minor, build, revision, date_applied) VALUES({$schema[0]},{$schema[1]},{$schema[2]},{$schema[3]}, NOW())");
	
		// create default fees
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_commission () VALUES()");
		$commission_id = $this->db->getLastId();
	
		// default fee rates
		foreach (array(MsCommission::RATE_SALE, MsCommission::RATE_SIGNUP, MsCommission::RATE_LISTING) as $type) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_commission_rate` (rate_type, commission_id, flat, percent, payment_method) VALUES(" . $type . ", $commission_id, 0,0," . MsPgPayment::METHOD_BALANCE . ")");
		}
	
		// default seller group fees
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_seller_group (commission_id) VALUES($commission_id)");
		$seller_group_id = $this->db->getLastId();
	
		// default seller group description
		$languages = $this->model_localisation_language->getLanguages();
		foreach ($languages as $code => $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ms_seller_group_description SET seller_group_id = '" . (int)$seller_group_id . "', language_id = '" . (int)$language['language_id'] . "', name = 'Default', description = 'Default seller group'");
		}
	
		// multimerch routes
		// seller
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout SET name = '[MultiMerch] Seller Account Pages'");
		$layout_id = $this->db->getLastId();
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module (`layout_id`, `code`, `position`, `sort_order`) VALUES($layout_id, 'account', 'column_right', 1);");

		$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', route = 'seller/account-%'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout SET name = '[MultiMerch] Sellers List'");
		$layout_id = $this->db->getLastId();

		$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', route = 'seller/catalog-seller'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout SET name = '[MultiMerch] Seller Profile Page'");
		$layout_id = $this->db->getLastId();
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', route = 'seller/catalog-seller/profile'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout SET name = '[MultiMerch] Seller Products List'");
		$layout_id = $this->db->getLastId();
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', route = 'seller/catalog-seller/products'");

		// customer
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout SET name = '[MultiMerch] Customer Account Pages'");
		$layout_id = $this->db->getLastId();
		$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module (`layout_id`, `code`, `position`, `sort_order`) VALUES($layout_id, 'customer', 'column_right', 1);");

		$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', route = 'customer/%'");


		$account = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code`='account' AND `key`='account_status'")->row;
		if(empty($account)) {
			$sql = "INSERT INTO " . DB_PREFIX . "setting SET `store_id` = 0, `code` = 'account', `key` = 'account_status', `value` = 1, `serialized` = 0";
		} else {
			$sql = "UPDATE " . DB_PREFIX . "setting SET `store_id` = 0, `code` = 'account', `key` = 'account_status', `value` = 1, `serialized` = 0 WHERE `setting_id` = " . (int)$account['setting_id'];
		}
		$this->db->query($sql);

		/* social links @todo */
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$sql = "SELECT channel_id FROM " . DB_PREFIX . "ms_channel WHERE 1";
		$query = $this->db->query($sql);
		if(empty($query->rows)) {
			foreach(array('Facebook', 'Twitter', 'LinkedIn', 'Google+') as $channel) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ms_channel SET image = 'catalog/multimerch/social_links/GraphicBurger/{$channel}.png'");
				$channel_id = $this->db->getLastId();

				foreach ($languages as $code => $language) {
					$this->db->query("
					INSERT INTO " . DB_PREFIX . "ms_channel_description
					SET channel_id = $channel_id,
					 	language_id = ". (int)$language['language_id'] . ",
						name = '" . $this->db->escape($channel) . "',
						description = 'Please specify your " . $this->db->escape($channel) . " link'
					");
				}
			}
		}

		// MM Order total module install
		$this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = 'total', `code` = 'mm_shipping_total'");
	}
	
	public function deleteSchema() {
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_product`,
		`" . DB_PREFIX . "ms_seller`,
		`" . DB_PREFIX . "ms_seller_description`,
		`" . DB_PREFIX . "ms_order_product_data`,
		`" . DB_PREFIX . "ms_balance`,
		`" . DB_PREFIX . "ms_seller_group`,
		`" . DB_PREFIX . "ms_seller_group_description`,
		`" . DB_PREFIX . "ms_seller_group_criteria`,
		`" . DB_PREFIX . "ms_seller_setting`,
		`" . DB_PREFIX . "ms_seller_group_setting`,
		`" . DB_PREFIX . "ms_commission_rate`,
		`" . DB_PREFIX . "ms_commission`,
		`" . DB_PREFIX . "ms_category_commission`,
		`" . DB_PREFIX . "ms_criteria`,
		`" . DB_PREFIX . "ms_range_int`,
		`" . DB_PREFIX . "ms_range_decimal`,
		`" . DB_PREFIX . "ms_range_date`,
		`" . DB_PREFIX . "ms_return`,
		`" . DB_PREFIX . "ms_return_product`,
		`" . DB_PREFIX . "ms_return_history`,
		`" . DB_PREFIX . "ms_review`,
		`" . DB_PREFIX . "ms_suborder`,
		`" . DB_PREFIX . "ms_suborder_history`,
		`" . DB_PREFIX . "ms_order_comment`,
		`" . DB_PREFIX . "ms_db_schema`,
		`" . DB_PREFIX . "ms_version`");

		/* badges */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_badge`,
		`" . DB_PREFIX . "ms_badge_description`,
		`" . DB_PREFIX . "ms_badge_seller_group`");

		/* social links */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_channel`,
		`" . DB_PREFIX . "ms_channel_description`,
		`" . DB_PREFIX . "ms_seller_channel`");

		/* messaging */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_message`,
		`" . DB_PREFIX . "ms_message_upload`,
		`" . DB_PREFIX . "ms_conversation_to_order`,
		`" . DB_PREFIX . "ms_conversation_participants`,
		`" . DB_PREFIX . "ms_conversation_to_product`,
		`" . DB_PREFIX . "ms_conversation`");

		/* questions */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_question`,
		`" . DB_PREFIX . "ms_answer`,
		`" . DB_PREFIX . "ms_user_vote`");

		/* shipping */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_shipping_method`,
		`" . DB_PREFIX . "ms_shipping_method_description`,
		`" . DB_PREFIX . "ms_shipping_delivery_time`,
		`" . DB_PREFIX . "ms_shipping_delivery_time_description`,
		`" . DB_PREFIX . "ms_product_shipping`,
		`" . DB_PREFIX . "ms_product_shipping_location`,
		`" . DB_PREFIX . "ms_order_product_shipping_data`,
		`" . DB_PREFIX . "ms_seller_shipping`,
		`" . DB_PREFIX . "ms_seller_shipping_location`");

		/* payments */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_pg_request`,
		`" . DB_PREFIX . "ms_customer_ppakey`,
		`" . DB_PREFIX . "ms_pg_payment`,
		`" . DB_PREFIX . "ms_pg_payment_request`");

		/* attributes */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_attribute`,
		`" . DB_PREFIX . "ms_attribute_group`");

		/* options */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_option`");

		/* categories */
		$this->db->query("DROP TABLE IF EXISTS
		`" . DB_PREFIX . "ms_category`,
		`" . DB_PREFIX . "ms_category_description`,
		`" . DB_PREFIX . "ms_category_filter`,
		`" . DB_PREFIX . "ms_category_to_store`,
		`" . DB_PREFIX . "ms_category_path`,
		`" . DB_PREFIX . "ms_product_to_category`");
	}
	
	public function deleteData() {
		//@todo

		// remove MultiMerch routes
		$this->db->query("DELETE FROM " . DB_PREFIX . "layout WHERE name = '[MultiMerch] Seller Account Pages'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE route = 'seller/account-%'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "layout WHERE name = '[MultiMerch] Sellers List'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE route = 'seller/catalog-seller'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "layout WHERE name = '[MultiMerch] Seller Profile Page'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE route = 'seller/catalog-seller/profile'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "layout WHERE name = '[MultiMerch] Seller Products List'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE route = 'seller/catalog-seller/products'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "layout WHERE name = '[MultiMerch] Customer Account Pages'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE route = 'customer/%'");

		$mm_layout_modules = array('account', 'customer');
		foreach ($mm_layout_modules as $module_code) {
			$modules = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE code = '" . $this->db->escape($module_code) . "'");
			if($modules->num_rows) {
				foreach ($modules->rows as $module) {
					$layout_exists = $this->db->query("SELECT 1 FROM " . DB_PREFIX . "layout WHERE layout_id = " . (int)$module['layout_id']);
					if(!$layout_exists->num_rows) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "layout_module WHERE code = '" . $this->db->escape($module_code) . "' AND layout_id = " . (int)$module['layout_id']);
					}
				}
			}
		}

		// MM Order total module uninstall
		$this->db->query("DELETE FROM " . DB_PREFIX . "extension WHERE `type` = 'total' AND `code` = 'mm_shipping_total'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = 'mm_shipping_total'");

		// Uninstall MultiMerch modules
		$extensions = $this->model_extension_extension->getInstalled('module');
		foreach ($extensions as $key => $value) {
			if(strpos($value,'multimerch_') !== FALSE) {
				$this->model_extension_extension->uninstall('module', $value);
				$this->model_extension_module->deleteModulesByCode($value);
			}
		}

		// Delete payment gateways settings
		$this->db->query("DELETE FROM " . DB_PREFIX . "extension WHERE `type` = 'ms_payment'");
	}
}