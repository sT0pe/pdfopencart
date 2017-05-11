<?php

// General
$_['ms_enabled'] = 'Enabled';
$_['ms_disabled'] = 'Disabled';
$_['ms_apply'] = 'Apply';
$_['ms_type'] = 'Type';
$_['ms_type_checkbox'] = 'Checkbox';
$_['ms_type_date'] = 'Date';
$_['ms_type_datetime'] = 'Date &amp; Time';
$_['ms_type_file'] = 'File';
$_['ms_type_image'] = 'Image';
$_['ms_type_radio'] = 'Radio';
$_['ms_type_select'] = 'Select';
$_['ms_type_text'] = 'Text';
$_['ms_type_textarea'] = 'Textarea';
$_['ms_type_time'] = 'Time';
$_['ms_store'] = 'Store';
$_['ms_store_default'] = 'Default';
$_['ms_id'] = '#';
$_['ms_login_as_vendor'] = '<a href="%s">Log in as vendor</a> to manage shipping information';
$_['ms_sort_order'] = 'Sort order';

$_['ms_button_approve'] = 'Approve';
$_['ms_button_decline'] = 'Decline';

$_['ms_fixed_coupon_warning'] = "<b>Warning:</b> Fixed (whole cart) coupons can not be applied to multivendor shopping carts and will prevent vendor commissions from being calculated correctly! Percentage coupons will work as expected.";
$_['ms_voucher_warning'] = "<b>Warning:</b> Gift Vouchers can not be applied to multivendor shopping carts and will prevent vendor commissions from being calculated correctly!";
$_['ms_error_directory'] = "Warning: Could not create directory: %s. Please create it manually and make it server-writable before proceeding. <br />";
$_['ms_error_directory_notwritable'] = "Warning: Directory already exists and is not writable: %s. Please make sure it's empty and make it server-writable before proceeding. <br />";
$_['ms_error_directory_exists'] = "Warning: Directory already exists: %s. Please make sure it's empty before proceeding. <br />";
$_['ms_error_product_publish'] = 'Failed to publish some products: seller account not active.';
$_['ms_success_installed'] = 'Extension successfully installed';
$_['ms_success_product_status'] = 'Successfully changed product status.';

$_['ms_db_upgrade'] = ' Please <a href="%s">click here</a> to upgrade your MultiMerch Marketplace database to the latest version (%s -> %s) .';
$_['ms_files_upgrade'] = ' Warning: The version of your MultiMerch files (%s) is older than the version required by your database structure (%s). This can be caused by uploading older MultiMerch version on top of a newer one. Please update your MultiMerch files or reinstall MultiMerch';
$_['ms_db_success'] = 'Your MultiMerch Marketplace database is now up to date!';
$_['ms_db_latest'] = 'Your MultiMerch Marketplace database is already up to date!';

$_['ms_error_php_version'] = ' MultiMerch requires PHP 5.4 or newer. Please contact your hosting provider to upgrade your PHP installation or change it via CPanel > Select PHP version';

$_['heading_title'] = '<b>[MultiMerch]</b> Digital Marketplace';
$_['text_no_results'] = 'No results';
$_['error_permission'] = 'Warning: You do not have permission to modify module!';

$_['ms_error_withdraw_response'] = 'Error: no response';
$_['ms_error_withdraw_status'] = 'Error: unsuccessful transaction';
$_['ms_success'] = 'Success';
$_['ms_success_transactions'] = 'Transactions successfully completed';
$_['ms_success_payment_deleted'] = 'Payment deleted';
$_['text_success']                 = 'Success: You have modified settings!';

$_['ms_none'] = 'None';
$_['ms_seller'] = 'Seller';
$_['ms_all_sellers'] = 'All sellers';
$_['ms_amount'] = 'Amount';
$_['ms_product'] = 'Product';
$_['ms_quantity'] = 'Quantity';
$_['ms_sales'] = 'Sales';
$_['ms_price'] = 'Price';
$_['ms_net_amount'] = 'Net amount';
$_['ms_from'] = 'From';
$_['ms_to'] = 'To';
$_['ms_paypal'] = 'PayPal';
$_['ms_date_created'] = 'Date created';
$_['ms_status'] = 'Status';
$_['ms_date_modified'] = 'Date modified';
$_['ms_date_paid'] = 'Date paid';
$_['ms_date'] = 'Date';
$_['ms_description'] = 'Description';
$_['ms_confirm'] = 'Confirm';
$_['ms_total'] = 'Total';
$_['ms_method'] = 'Method';

$_['ms_commission'] = 'Commission';
$_['ms_commissions_fees'] = 'Commissions & fees';

$_['ms_user_settings'] = 'User settings';
$_['ms_seller_full_name'] = "Full name";
$_['ms_seller_address1'] = "Address Line 1";
$_['ms_seller_address1_placeholder'] = 'Street address, P.O. box, company name, c/o';
$_['ms_seller_address2'] = "Address Line 2";
$_['ms_seller_address2_placeholder'] = 'Apartment, suite, unit, building, floor etc.';
$_['ms_seller_city'] = "City";
$_['ms_seller_state'] = "State/Province/Region";
$_['ms_seller_zip'] = "ZIP/Postal Code";
$_['ms_seller_country'] = "Country";
$_['ms_seller_company'] = 'Company';
$_['ms_seller_website'] = 'Website';
$_['ms_seller_phone'] = 'Phone';

$_['ms_commission_' . MsCommission::RATE_SALE] = 'Sale fee';
$_['ms_commission_' . MsCommission::RATE_LISTING] = 'Listing fee / method';
$_['ms_commission_' . MsCommission::RATE_SIGNUP] = 'Signup fee / method';

$_['ms_commission_short_' . MsCommission::RATE_SALE] = 'S';
$_['ms_commission_short_' . MsCommission::RATE_LISTING] = 'L';
$_['ms_commission_short_' . MsCommission::RATE_SIGNUP] = 'SU';
$_['ms_commission_actual'] = 'Actual fee rates';

$_['ms_name'] = 'Name';
$_['ms_config_width'] = 'Width';
$_['ms_config_height'] = 'Height';
$_['ms_description'] = 'Description';

$_['ms_enable'] = 'Enable';
$_['ms_disable'] = 'Disable';
$_['ms_delete'] = 'Delete';
$_['ms_view_in_store'] = 'View in store';
$_['ms_view'] = 'View';

$_['ms_button_pay'] = 'Pay';

$_['ms_logo'] = 'Logo';

// Menu
$_['ms_menu_multiseller'] = 'MultiMerch';
$_['ms_menu_sellers'] = 'Sellers';
$_['ms_menu_seller_groups'] = 'Seller groups';
$_['ms_menu_catalog'] = 'Catalog';
$_['ms_menu_attributes'] = 'Attributes';
$_['ms_menu_categories'] = 'Categories';
$_['ms_menu_options'] = 'Options';
$_['ms_menu_products'] = 'Products';
$_['ms_menu_transactions'] = 'Transactions';
$_['ms_menu_conversations'] = 'Conversations';
$_['ms_menu_payment'] = 'Payments';
$_['ms_menu_shipping_method'] = 'Shipping methods';
$_['ms_menu_payment_gateway'] = 'Payment gateways';
$_['ms_menu_payment_gateway_settings'] = 'Payment gateway settings';
$_['ms_menu_payment_request'] = 'Payment requests';
$_['ms_menu_settings'] = 'Settings';
$_['ms_menu_install'] = 'Install';

// Settings
$_['ms_settings_heading'] = 'Settings';
$_['ms_settings_breadcrumbs'] = 'Settings';
$_['ms_config_seller_validation'] = 'Seller validation';
$_['ms_config_seller_validation_note'] = 'Seller validation';
$_['ms_config_seller_validation_none'] = 'No validation';
$_['ms_config_seller_validation_activation'] = 'Activation via email';
$_['ms_config_seller_validation_approval'] = 'Manual approval';

$_['ms_config_general'] = 'General';
$_['ms_config_limits'] = 'Limits';
$_['ms_config_file_types'] = 'File types';
$_['ms_config_shipping'] = 'Shipping';
$_['ms_config_product_fields'] = 'Product fields';
$_['ms_config_order_statuses'] = 'Order statuses';

$_['ms_config_product_validation'] = 'Product validation';
$_['ms_config_product_validation_note'] = 'Product validation';
$_['ms_config_product_validation_none'] = 'No validation';
$_['ms_config_product_validation_approval'] = 'Manual approval';


$_['ms_config_allow_free_products'] = 'Allow free products';

$_['ms_config_allow_digital_products'] = 'Allow digital products';
$_['ms_config_allow_digital_products_note'] = 'Products will be created with OC \'Shipping\' option disabled';

$_['ms_config_minmax_product_price'] = 'Minimum and maximum product price';
$_['ms_config_minmax_product_price_note'] = 'Minimum and maximum product price (0 for no limits)';

$_['ms_config_product_attributes_options'] = 'Product attributes / options';
$_['ms_config_allow_attributes'] = 'Allow vendors to create attributes';
$_['ms_config_allow_attributes_note'] = 'Allow vendors to create their own attributes and attribute groups';
$_['ms_config_allow_options'] = 'Allow vendors to create options';
$_['ms_config_allow_options_note'] = 'Allow vendors to create their own options';

$_['ms_config_product_questions'] = 'Questions';
$_['ms_config_allow_question'] = "Allow questions on product page";

$_['ms_config_allowed_image_types'] = 'Allowed image extensions';
$_['ms_config_allowed_image_types_note'] = 'Allowed image extensions';

$_['ms_config_images_limits'] = 'Product image limits';
$_['ms_config_images_limits_note'] = 'Minimum and maximum number of images (incl. thumbnail) required/allowed for products (0 = no limit)';

$_['ms_config_downloads_limits'] = 'Product download limits';
$_['ms_config_downloads_limits_note'] = 'Minimum and maximum number of downloads required/allowed for products (0 = no limit)';

$_['ms_config_allowed_download_types'] = 'Allowed download extensions';
$_['ms_config_allowed_download_types_note'] = 'Allowed download extensions';

$_['ms_config_credit_order_statuses'] = 'Completed orders';
$_['ms_config_credit_order_statuses_note'] = 'When the order changes its status to one of the statuses selected here, seller\'s commission is released to their balance';

$_['ms_config_debit_order_statuses'] = 'Refunded orders';
$_['ms_config_debit_order_statuses_note'] = 'When the order changes its status to one of the statuses selected here, seller\'s commission is refunded from their balance';


$_['ms_config_paypal_sandbox'] = 'PayPal Sandbox mode';
$_['ms_config_paypal_sandbox_note'] = 'Use PayPal in Sandbox mode for testing and debugging';

$_['ms_config_paypal_address'] = 'PayPal address';
$_['ms_config_paypal_address_note'] = 'Your PayPal address for listing and signup fees';


$_['ms_config_product_categories'] = 'Product categories';
$_['ms_config_allow_seller_categories'] = 'Allow vendors to create categories';
$_['ms_config_allow_seller_categories_note'] = 'Allow vendors to create their own categories structure';
$_['ms_config_allow_multiple_categories'] = 'Allow multiple categories';
$_['ms_config_allow_multiple_categories_note'] = 'Allow sellers to add products to multiple categories';
$_['ms_config_restrict_categories'] = 'Disallowed categories';
$_['ms_config_restrict_categories_note'] = '<u>Disallow</u> sellers to list products in these categories';

$_['ms_config_product_included_fields'] = 'Include fields for products';
$_['ms_config_product_included_fields_note'] = 'Fields to be added in the product form';


$_['ms_config_seller_terms_page'] = 'Seller account terms';
$_['ms_config_seller_terms_page_note'] = 'Sellers have to agree to the terms when creating a seller account.';


$_['ms_config_finances'] = 'Finances';
$_['ms_config_miscellaneous'] = 'Miscellaneous';

// MM > Settings > Shipping
$_['ms_config_shipping'] = 'Shipping';
$_['ms_config_shipping_type'] = 'Shipping type';
$_['ms_config_enable_store_shipping'] = 'Store Shipping';
$_['ms_config_enable_vendor_shipping'] = 'Vendor Shipping';
$_['ms_config_disable_shipping'] = 'Disabled';
$_['ms_config_shipping_type_note'] = 'With \'Store Shipping\' option selected, new products will be created with OpenCart\'s \'Shippable\' field enabled. With \'Vendor shipping\' option selected, Multimerch\'s custom shipping functionality is enabled. With \'Disabled \' option selected, Multimerch\'s shipping functionality and OpenCart\'s \'Shippable\' field are disabled';
$_['ms_config_shipping_delivery'] = 'Delivery';
$_['ms_config_shipping_delivery_times'] = 'Delivery times';
$_['ms_config_shipping_delivery_time_add_btn'] = '+ Add delivery time';
$_['ms_config_shipping_delivery_time_comment'] = 'Double click in cell to edit';

$_['ms_config_vendor_shipping_type'] = 'Vendor Shipping type';
$_['ms_config_vendor_shipping_combined'] = 'Combined shipping';
$_['ms_config_vendor_shipping_per_product'] = 'Per-Product shipping';
$_['ms_config_vendor_shipping_both'] = 'Both';
$_['ms_config_vendor_shipping_type_note'] = 'With \'Combined shipping\' option selected, seller will be able to only set combined shipping rules. With \'Per-Product shipping\' option selected, he can set only fixed per-product shipping rules. \'Both\' option allows to set \'Combined\' as well as \'Per-Product\' shipping rules';

// MM > Settings > Products
$_['ms_config_reviews'] = 'Reviews';
$_['ms_config_reviews_enable'] = 'Enable reviews';
$_['ms_config_reviews_enable_note'] = 'Enable or disable reviews on product page';

$_['ms_config_product_categories_type'] = 'Type of product categories';
$_['ms_config_product_categories_type_note'] = 'Which type of categories vendor is allowed to use when listing his product';
$_['ms_config_product_category_store'] = 'Store';
$_['ms_config_product_category_seller'] = 'Vendor';
$_['ms_config_product_category_both'] = 'Both';

// Sales > Order > Info > Shipping
$_['ms_sale_order_shipping_cost'] = 'Shipping cost';


$_['ms_config_status'] = 'Status';
$_['ms_config_top'] = 'Content Top';
$_['ms_config_limit'] = 'Limit:';
$_['ms_config_image'] = 'Image (W x H):';

$_['ms_config_enable_rte'] = 'Enable Rich Text Editor for descriptions';
$_['ms_config_enable_rte_note'] = 'Enable Summernote Rich Text Editor for product and seller description fields.';

$_['ms_config_rte_whitelist'] = 'Tag whitelist';
$_['ms_config_rte_whitelist_note'] = 'Permitted tags in RTE (empty = all tags permitted)';

$_['ms_config_image_sizes'] = 'Image sizes';
$_['ms_config_seller_avatar_image_size'] = 'Avatar image size';
$_['ms_config_seller_avatar_image_size_seller_profile'] = 'Seller profile';
$_['ms_config_seller_avatar_image_size_seller_list'] = 'Seller list';
$_['ms_config_seller_avatar_image_size_product_page'] = 'Product page';
$_['ms_config_seller_avatar_image_size_seller_dashboard'] = 'Seller dashboard';
$_['ms_config_seller_banner_size'] = 'Seller banner size';

$_['ms_config_image_preview_size'] = 'Image preview size';
$_['ms_config_image_preview_size_seller_avatar'] = 'Seller avatar';
$_['ms_config_image_preview_size_product_image'] = 'Product image';

$_['ms_config_product_image_size'] = 'Product image size';
$_['ms_config_product_image_size_seller_profile'] = 'Seller profile';
$_['ms_config_product_image_size_seller_products_list'] = 'Catalog products';
$_['ms_config_product_image_size_seller_products_list_account'] = 'Account products';


$_['ms_config_uploaded_image_size'] = 'Image size limits';
$_['ms_config_uploaded_image_size_note'] = 'Define uploaded image dimension limits (W x H). Set 0 for no limits.';
$_['ms_config_max'] = 'Max.';
$_['ms_config_min'] = 'Min.';

$_['ms_config_seo'] = 'SEO';
$_['ms_config_enable_seo_urls_seller'] = 'Generate SEO URLs for new sellers';
$_['ms_config_enable_seo_urls_seller_note'] = 'This option will generate SEO-friendly URLs for new sellers. SEO URLs need to be enabled in OpenCart to use this.';
$_['ms_config_enable_seo_urls_product'] = 'Generate SEO URLs for new products (experimental)';
$_['ms_config_enable_seo_urls_product_note'] = 'This option will generate SEO-friendly URLs for new product. SEO URLs need to be enabled in OpenCart to use this. Experimental, especially for non-English stores.';
$_['ms_config_enable_non_alphanumeric_seo'] = 'Allow UTF8 in SEO URLs (experimental)';
$_['ms_config_enable_non_alphanumeric_seo_note'] = 'This will not strip UTF8 symbols from SEO URLs. Use at your own risk.';
$_['ms_config_sellers_slug'] = 'Sellers SEO keyword';
$_['ms_config_sellers_slug_note'] = 'Sellers list SEO keyword (will work only if SEO is enabled)';


$_['ms_config_seller'] = 'Sellers';

// Change Seller Group
$_['ms_config_change_group'] = 'Allow select group on signup';
$_['ms_config_change_group_note'] = 'Allow sellers to choose seller group on signup';

// Change Seller Nickname
$_['ms_config_seller_change_nickname'] = 'Allow nickname change';
$_['ms_config_seller_change_nickname_note'] = 'Allow sellers to change nickname/shop name';

// Seller Nickname Rules
$_['ms_config_nickname_rules'] = 'Seller nickname rules';
$_['ms_config_nickname_rules_note'] = 'Character sets allowed in seller nicknames';
$_['ms_config_nickname_rules_alnum'] = 'Alphanumeric';
$_['ms_config_nickname_rules_ext'] = 'Extended latin';
$_['ms_config_nickname_rules_utf'] = 'Full UTF-8';


$_['mxt_google_analytics'] = 'Google Analytics';
$_['mxt_google_analytics_enable'] = 'Enable Google Analytics';

$_['mxt_disqus_comments'] = 'Disqus Comments';
$_['mxt_disqus_comments_enable'] = 'Enable Disqus comments';
$_['mxt_disqus_comments_shortname'] = 'Disqus shortname';

$_['mmes_messaging'] = 'Private Messaging';
$_['mmess_config_enable'] = 'Enable private messaging for MultiMerch';
$_['ms_config_msg_allowed_file_types'] = 'Allowed file extensions';
$_['ms_config_msg_allowed_file_types_note'] = 'Allowed file extensions for uploading in messages';

// Badges
$_['ms_menu_badge'] = 'Badges';
$_['ms_config_badge_size'] = 'Badge size';
$_['ms_catalog_badges_breadcrumbs'] = 'Badges';
$_['ms_catalog_badges_heading'] = 'Badges';
$_['ms_badges_column_id'] = 'ID';
$_['ms_badges_column_name'] = 'Name';
$_['ms_badges_image'] = 'Image';
$_['ms_badges_column_action'] = 'Action';
$_['ms_catalog_insert_badge_heading'] = 'Create badge';
$_['ms_catalog_edit_badge_heading'] = 'Edit badge';
$_['ms_success_badge_created'] = 'Badge created';
$_['ms_success_badge_updated'] = 'Badge updated';
$_['ms_error_badge_name'] = 'Please specify a name for the badge';

// Social Links
$_['ms_menu_social_links'] = 'Social links';
$_['ms_sl_icon_size'] = 'Icon size';
$_['ms_sl'] = 'Social links';
$_['ms_sl_manage'] = 'Manage social channels';
$_['ms_sl_create'] = 'New social channel';
$_['ms_sl_update'] = 'Update social channel';
$_['ms_sl_column_id'] = '#';
$_['ms_sl_column_name'] = 'Name';
$_['ms_sl_image'] = 'Image';
$_['ms_sl_column_action'] = 'Action';
$_['ms_success_channel_created'] = 'Social channel created';
$_['ms_success_channel_updated'] = 'Social channel updated';
$_['ms_error_channel_name'] = 'Please specify a name for the social channel';

// Seller - List
$_['ms_catalog_sellers_heading'] = 'Sellers';
$_['ms_catalog_sellers_breadcrumbs'] = 'Sellers';
$_['ms_catalog_sellers_newseller'] = 'New seller';
$_['ms_catalog_sellers_create'] = 'Create new seller';

$_['ms_catalog_sellers_total_balance'] = 'Total amount on all balances: <b>%s</b> (active sellers: <b>%s</b>)';
$_['ms_catalog_sellers_email'] = 'Email';
$_['ms_catalog_sellers_total_products'] = 'Products';
$_['ms_catalog_sellers_total_sales'] = 'Sales';
$_['ms_catalog_sellers_total_earnings'] = 'Earnings';
$_['ms_catalog_sellers_current_balance'] = 'Balance (Total/Available)';
$_['ms_catalog_sellers_status'] = 'Status';
$_['ms_catalog_sellers_date_created'] = 'Date created';

$_['ms_seller_status_' . MsSeller::STATUS_ACTIVE] = 'Active';
$_['ms_seller_status_' . MsSeller::STATUS_INACTIVE] = 'Inactive';
$_['ms_seller_status_' . MsSeller::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_status_' . MsSeller::STATUS_INCOMPLETE] = 'Incomplete';
$_['ms_seller_status_' . MsSeller::STATUS_DELETED] = 'Deleted';
$_['ms_seller_status_' . MsSeller::STATUS_UNPAID] = 'Unpaid signup fee';

// Customer-seller form
$_['ms_catalog_sellerinfo_heading'] = 'Seller';
$_['ms_catalog_sellerinfo_seller_data'] = 'Seller data';

$_['ms_catalog_sellerinfo_customer'] = 'User';
$_['ms_catalog_sellerinfo_customer_data'] = 'User data';
$_['ms_catalog_sellerinfo_customer_new'] = 'New user';
$_['ms_catalog_sellerinfo_customer_existing'] = 'Existing user';
$_['ms_catalog_sellerinfo_customer_create_new'] = 'Create a new user';
$_['ms_catalog_sellerinfo_customer_firstname'] = 'First Name';
$_['ms_catalog_sellerinfo_customer_lastname'] = 'Last Name';
$_['ms_catalog_sellerinfo_customer_email'] = 'Email';
$_['ms_catalog_sellerinfo_customer_password'] = 'Password';
$_['ms_catalog_sellerinfo_customer_password_confirm'] = 'Confirm password';

$_['ms_catalog_sellerinfo_nickname'] = 'Nickname';
$_['ms_catalog_sellerinfo_keyword'] = 'SEO keyword';
$_['ms_catalog_sellerinfo_description'] = 'Description';
$_['ms_catalog_sellerinfo_zone'] = 'Region / State';
$_['ms_catalog_sellerinfo_zone_select'] = 'Select region/state';
$_['ms_catalog_sellerinfo_zone_not_selected'] = 'No region/state selected';
$_['ms_catalog_sellerinfo_sellergroup'] = 'Seller group';

$_['ms_catalog_sellerinfo_avatar'] = 'Avatar';
$_['ms_catalog_sellerinfo_message'] = 'Message';
$_['ms_catalog_sellerinfo_message_note'] = 'Include this message in the notification email to the seller (optional)';
$_['ms_catalog_sellerinfo_notify'] = 'Notify seller';
$_['ms_catalog_sellerinfo_notify_note'] = 'Check this box to send an email to the seller indicating his account has been modified';
$_['ms_catalog_sellerinfo_product_validation'] = 'Product validation';
$_['ms_catalog_sellerinfo_product_validation_note'] = 'Product validation for this seller';

$_['ms_error_sellerinfo_nickname_empty'] = 'Nickname cannot be empty';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'Nickname can only contain alphanumeric symbols';
$_['ms_error_sellerinfo_nickname_utf8'] = 'Nickname can only contain printable UTF-8 symbols';
$_['ms_error_sellerinfo_nickname_latin'] = 'Nickname can only contain alphanumeric symbols and diacritics';
$_['ms_error_sellerinfo_nickname_length'] = 'Nickname should be between 4 and 50 characters';
$_['ms_error_sellerinfo_nickname_taken'] = 'This nickname is already taken';

// Catalog - Products
$_['ms_catalog_products_heading'] = 'Products';
$_['ms_catalog_products_breadcrumbs'] = 'Products';
$_['ms_catalog_products_notify_sellers'] = 'Notify Sellers';
$_['ms_catalog_products_bulk'] = '--Bulk status change--';
$_['ms_catalog_products_bulk_seller'] = '--Bulk seller change--';
$_['ms_catalog_products_noseller'] = '--No seller--';

$_['ms_product_status_' . MsProduct::STATUS_ACTIVE] = 'Active';
$_['ms_product_status_' . MsProduct::STATUS_INACTIVE] = 'Inactive';
$_['ms_product_status_' . MsProduct::STATUS_DISABLED] = 'Disabled';
$_['ms_product_status_' . MsProduct::STATUS_DELETED] = 'Deleted';
$_['ms_product_status_' . MsProduct::STATUS_UNPAID] = 'Unpaid listing fee';

$_['ms_catalog_products_field_price'] = 'Price';
$_['ms_catalog_products_field_quantity'] = 'Quantity';
$_['ms_catalog_products_field_category'] = 'Category';
$_['ms_catalog_products_field_tags'] = 'Tags';
$_['ms_catalog_products_field_attributes'] = 'Attributes';
$_['ms_catalog_products_field_options'] = 'Options';
$_['ms_catalog_products_field_special_prices'] = 'Special prices';
$_['ms_catalog_products_field_quantity_discounts'] = 'Quantity discounts';
$_['ms_catalog_products_field_images'] = 'Images';
$_['ms_catalog_products_field_files'] = 'Files';
$_['ms_catalog_products_field_meta_keyword'] 	 = 'Meta Keywords';
$_['ms_catalog_products_field_meta_description'] = 'Meta Description';
$_['ms_catalog_products_field_meta_title'] = 'Meta Title';
$_['ms_catalog_products_field_seo_url'] = 'SEO Keyword';
$_['ms_catalog_products_field_model']            = 'Model';
$_['ms_catalog_products_field_sku']              = 'SKU';
$_['ms_catalog_products_field_upc']              = 'UPC';
$_['ms_catalog_products_field_ean']              = 'EAN';
$_['ms_catalog_products_field_jan']              = 'JAN';
$_['ms_catalog_products_field_isbn']             = 'ISBN';
$_['ms_catalog_products_field_mpn']              = 'MPN';
$_['ms_catalog_products_field_manufacturer']     = 'Manufacturer';
$_['ms_catalog_products_field_date_available']   = 'Date Available';
$_['ms_catalog_products_field_stock_status']     = 'Out Of Stock Status';
$_['ms_catalog_products_field_tax_class']        = 'Tax Class';
$_['ms_catalog_products_field_subtract']         = 'Subtract Stock';
$_['ms_catalog_products_filters']         = 'Filters';
$_['ms_catalog_products_min_order_qty']         = 'Minimum Order Quantity';
$_['ms_catalog_products_related_products']         = 'Related Products';
$_['ms_catalog_products_dimensions']            = 'Dimensions';
$_['ms_catalog_products_weight']            = 'Weight';

// Catalog - Seller Groups
$_['ms_catalog_seller_groups_heading'] = 'Seller groups';
$_['ms_catalog_seller_groups_breadcrumbs'] = 'Seller Groups';

$_['ms_seller_groups_column_id'] = 'ID';
$_['ms_seller_groups_column_name'] = 'Name';
$_['ms_seller_groups_column_action'] = 'Actions';

$_['ms_catalog_insert_seller_group_heading'] = 'New Seller Group';
$_['ms_catalog_edit_seller_group_heading'] = 'Edit Seller Group';

$_['ms_product_period'] = 'Product listing period in days (0 for unlimited)';
$_['ms_product_quantity'] = 'Product quantity (0 for no limit)';

$_['ms_error_seller_group_name'] = 'Error: Name must be between 3 and 32 symbols long';
$_['ms_error_seller_group_default'] = 'Error: Default seller group can not be deleted!';
$_['ms_success_seller_group_created'] = 'Seller group created';
$_['ms_success_seller_group_updated'] = 'Seller group updated';

// Payments
$_['ms_payment_heading'] = 'Payments';
$_['ms_payment_breadcrumbs'] = 'Payments';
$_['ms_payment_payout_requests'] = 'Payout requests';
$_['ms_payment_payouts'] = 'Manual payouts';
$_['ms_payment_pending'] = 'Pending';
$_['ms_payment_new'] = 'New payment';
$_['ms_payment_paid'] = 'Paid';

$_['ms_success_payment_created'] = 'Payment successfully created';

// Shipping methods
$_['ms_shipping_method_heading'] = 'Shipping Methods';
$_['ms_shipping_method_breadcrumbs'] = 'Shipping Method';
$_['ms_shipping_method_status_' . MsShippingMethod::STATUS_ENABLED] = 'Enabled';
$_['ms_shipping_method_status_' . MsShippingMethod::STATUS_DISABLED] = 'Disabled';
$_['ms_shipping_method_add_heading'] = 'Add Shipping Method';
$_['ms_shipping_method_add_success'] = 'You have successfully added new shipping method!';
$_['ms_shipping_method_edit_heading'] = 'Edit Shipping Method';
$_['ms_shipping_method_edit_success'] = 'You have successfully modified shipping method!';
$_['ms_shipping_method_delete_success'] = 'You have deleted shipping method!';
$_['ms_shipping_method_name_error'] = 'Please, specify shipping method name';


// Debug
$_['ms_debug_heading'] = 'Debug';
$_['ms_debug_breadcrumbs'] = 'Debug';
$_['ms_debug_info'] = 'MultiMerch debug information';

// Finances - Transactions
$_['ms_transactions_heading'] = 'Transactions';
$_['ms_transactions_breadcrumbs'] = 'Transactions';
$_['ms_transactions_new'] = 'New transaction';

$_['ms_error_transaction_fromto'] = 'Please specify at least the source or the destination seller';
$_['ms_error_transaction_fromto_same'] = 'Source and destination cannot be the same';
$_['ms_error_transaction_amount'] = 'Please specify a valid positive amount';
$_['ms_success_transaction_created'] = 'Transaction successfully created';

$_['button_cancel'] = 'Cancel';
$_['button_save'] = 'Save';
$_['ms_action'] = 'Action';


// Account - Conversations and Messages
$_['ms_account_conversations'] = 'Conversations';
$_['ms_account_messages'] = 'Messages';
$_['ms_sellercontact_success'] = 'Your message has been successfully sent';

$_['ms_account_conversations_heading'] = 'Your Conversations';
$_['ms_account_conversations_breadcrumbs'] = 'Your Conversations';

$_['ms_account_conversations_status'] = 'Status';
$_['ms_account_conversations_from'] = 'Conversation from';
$_['ms_account_conversations_from_admin_prefix'] = ' (administrator)';
$_['ms_account_conversations_to'] = 'Conversation to';
$_['ms_account_conversations_title'] = 'Title';
$_['ms_account_conversations_type'] = 'Conversation type';
$_['ms_account_conversations_date_added'] = 'Date added';

$_['ms_account_conversations_sender_type_seller'] = 'seller';
$_['ms_account_conversations_sender_type_buyer'] = 'buyer';
$_['ms_account_conversations_sender_type_admin'] = 'admin';

$_['ms_conversation_title_product'] = 'Inquiry about product: %s';
$_['ms_conversation_title_order'] = 'Inquiry about order: %s';
$_['ms_conversation_title'] = 'Inquiry from %s';

$_['ms_account_conversations_read'] = 'Read';
$_['ms_account_conversations_unread'] = 'Unread';

$_['ms_account_messages_heading'] = 'Messages';
$_['ms_last_message'] = 'Last message';
$_['ms_message_text'] = 'Your message';
$_['ms_post_message'] = 'Send message';

$_['ms_customer_does_not_exist'] = 'Customer account deleted';
$_['ms_error_empty_message'] = 'Message cannot be left empty';

$_['ms_account_conversations_textarea_placeholder'] = 'Enter your message...';
$_['ms_account_conversations_upload'] = 'Upload file';
$_['ms_account_conversations_file_uploaded'] = 'Your file was successfully uploaded!';
$_['ms_error_file_extension'] = 'Invalid extension';

$_['ms_mail_subject_private_message'] = 'New private message received';
$_['ms_mail_private_message'] = <<<EOT
You have received a new private message from %s!

%s

%s

You can reply in the messaging area in your account.
EOT;

$_['ms_account_message'] = 'Message';
$_['ms_account_message_sender'] = 'Sender';
$_['ms_account_message_attachments'] = 'Attachments';

// Attributes
$_['ms_attribute_heading'] = 'Attributes';
$_['ms_attribute_breadcrumbs'] = 'Attributes';
$_['ms_attribute_create'] = 'New attribute';
$_['ms_attribute_edit'] = 'Edit attribute';
$_['ms_attribute_value'] = 'Attribute value';
$_['ms_error_attribute_name'] = 'Attribute name must be between 1 and 128 characters';
$_['ms_error_attribute_type'] = 'This attribute type requires attribute values';
$_['ms_error_attribute_value_name'] = 'Attribute value name must be between 1 and 128 characters';
$_['ms_success_attribute_created'] = 'Attribute successfully created';
$_['ms_success_attribute_updated'] = 'Attribute successfully updated';

$_['button_cancel'] = 'Cancel';
$_['button_save'] = 'Save';
$_['ms_action'] = 'Action';

// Mails
$_['ms_mail_greeting'] = "Hello %s,\n";
$_['ms_mail_greeting_no_name'] = "Hello,\n";
$_['ms_mail_ending'] = "\nRegards,\n%s";
$_['ms_mail_message'] = "\nMessage:\n%s";

$_['ms_mail_subject_seller_account_modified'] = 'Seller account modified';
$_['ms_mail_seller_account_modified'] = <<<EOT
Your seller account at %s has been modified by the administrator.

Account status: %s
EOT;

$_['ms_mail_subject_product_modified'] = 'Product modified';
$_['ms_mail_product_modified'] = <<<EOT
Your product %s at %s has been modified by the administrator.

Product status: %s
EOT;

$_['ms_mail_subject_product_purchased'] = 'New order';
$_['ms_mail_product_purchased'] = <<<EOT
Your product(s) have been purchased from %s.

Customer: %s (%s)

Products:
%s
Total: %s
EOT;

$_['ms_mail_product_purchased_no_email'] = <<<EOT
Your product(s) have been purchased from %s.

Customer: %s

Products:
%s
Total: %s
EOT;

$_['ms_mail_product_purchased_info'] = <<<EOT
\n
Delivery address:

%s %s
%s
%s
%s
%s %s
%s
%s
EOT;

$_['ms_mail_product_purchased_comment'] = 'Comment: %s';

$_['ms_mail_subject_product_reviewed'] = 'New product review';
$_['ms_mail_product_reviewed'] = <<<EOT
New review has been submitted for %s. 
Visit the following link to view it: <a href="%s">%s</a>
EOT;

// Catalog - Mail
// Attributes
$_['ms_mail_subject_attribute_status_changed'] = 'Your product attribute status updated';
$_['ms_mail_attribute_status_changed'] = <<<EOT
The status of your product attribute <strong>%s</strong> has been updated to: <strong>%s</strong>.
EOT;

$_['ms_mail_subject_attribute_seller_changed'] = 'Attribute owner changed';
$_['ms_mail_attribute_seller_attached'] = <<<EOT
Attribute %s has been assigned to your account.
EOT;
$_['ms_mail_attribute_seller_detached'] = <<<EOT
Attribute %s has been reassigned from your account.
EOT;

$_['ms_mail_subject_attribute_converted_to_global'] = 'Attribute converted';
$_['ms_mail_attribute_converted_to_global'] = <<<EOT
Your attribute "%s" has been converted to global.
EOT;

// Attribute groups
$_['ms_mail_subject_attribute_group_status_changed'] = 'Your attribute group status updated';
$_['ms_mail_attribute_group_status_changed'] = <<<EOT
The status of your attribute group <strong>%s</strong> has been updated to: <strong>%s</strong>.
EOT;

// Options
$_['ms_mail_subject_option_status_changed'] = 'Your option status updated';
$_['ms_mail_option_status_changed'] = <<<EOT
The status of your option <strong>%s</strong> has been updated to: <strong>%s</strong>.
EOT;

$_['ms_mail_subject_option_seller_changed'] = 'Option owner changed';
$_['ms_mail_option_seller_attached'] = <<<EOT
Option %s has been assigned to your account.
EOT;
$_['ms_mail_option_seller_detached'] = <<<EOT
Option %s has been reassigned from your account.
EOT;

$_['ms_mail_subject_option_converted_to_global'] = 'Option converted';
$_['ms_mail_option_converted_to_global'] = <<<EOT
Your option "%s" has been converted to global.
EOT;

// Categories
$_['ms_mail_subject_category_status_changed'] = 'Your category status updated';
$_['ms_mail_category_status_changed'] = <<<EOT
The status of your category <strong>%s</strong> has been updated to: <strong>%s</strong>.
EOT;

// Sales - Mail
$_['ms_transaction_order_created'] = 'Order created';
$_['ms_transaction_order'] = 'Sale: Order Id #%s';
$_['ms_transaction_sale'] = 'Sale: %s (-%s commission)';
$_['ms_transaction_refund'] = 'Refund: %s';
$_['ms_payment_method'] = 'Payment method';
$_['ms_payment_method_balance'] = 'Seller balance';
$_['ms_payment_royalty_payout'] = 'Royalty payout to %s at %s';
$_['ms_payment_completed'] = 'Payment completed';

// Payment gateways
$_['ms_pg_manage'] = 'Manage payment gateways';
$_['ms_pg_heading'] = 'Payment gateways';
$_['ms_pg_install'] = 'Success: You have installed %s gateway!';
$_['ms_pg_uninstall'] = 'Success: You have uninstalled %s gateway!';
$_['ms_pg_modify'] = 'Success: You have modified %s gateway!';
$_['ms_pg_modify_error'] = 'Warning: You do not have permission to modify Payment Gateway extensions!';
$_['ms_pg_for_fee'] = 'Enable for fee:';
$_['ms_pg_for_payout'] = 'Enable for payout:';
$_['ms_pg_uninstall_warning'] = 'Warning!\nAll payment gateway settings of all sellers will be deleted.\n\nAre you sure you want to continue?';
$_['ms_pg_fee_payment_method_name'] = '[MM] Payment Gateways';

// Payment requests
$_['ms_pg_request'] = 'Payment requests';
$_['ms_pg_request_create'] = 'Create payment request';
$_['ms_pg_request_desc_payout'] = "Payout: %s";
$_['ms_pg_request_error_create'] = 'You must select at least one seller!';
$_['ms_pg_request_error_select_payment_request'] = 'You must select at least one payment request!';
$_['ms_pg_request_error_empty'] = 'Error: Request is empty!';

$_['ms_pg_request_type_' . MsPgRequest::TYPE_SIGNUP] = 'Signup fee';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_LISTING] = 'Listing fee';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_PAYOUT] = 'Payout';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_PAYOUT_REQUEST] = 'Payout request';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_RECURRING] = 'Recurring';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_SALE] = 'Sale';

$_['ms_pg_request_status_' . MsPgRequest::STATUS_UNPAID] = 'Unpaid';
$_['ms_pg_request_status_' . MsPgRequest::STATUS_PAID] = 'Paid';
$_['ms_pg_request_status_' . MsPgRequest::STATUS_REFUND_REQUESTED] = 'Refund requested';
$_['ms_pg_request_status_' . MsPgRequest::STATUS_REFUNDED] = 'Refunded';

// Payments
$_['ms_pg_payment_number'] = 'Payment #';
$_['ms_pg_payment_type_' . MsPgPayment::TYPE_PAID_REQUESTS] = 'Paid payment requests';
$_['ms_pg_payment_type_' . MsPgPayment::TYPE_SALE] = 'Sales';

$_['ms_pg_payment_status_' . MsPgPayment::STATUS_INCOMPLETE] = '<p style="color: red">Incomplete</p>';
$_['ms_pg_payment_status_' . MsPgPayment::STATUS_COMPLETE] = '<p style="color: green">Complete</p>';
$_['ms_pg_payment_status_' . MsPgPayment::STATUS_WAITING_CONFIRMATION] = '<p style="color: blue">Waiting for confirmation</p>';

$_['ms_pg_payment_error_no_method'] = 'Error: You must select payment method!';
$_['ms_pg_payment_error_no_methods'] = 'You must select at least one payment!';
$_['ms_pg_payment_error_no_requests'] = 'Error: You must select payment requests!';
$_['ms_pg_payment_error_payment'] = 'Error: Can\'t create payment!';
$_['ms_pg_payment_error_sender_data'] = 'Error: Admin has not specified needed information!';
$_['ms_pg_payment_error_receiver_data'] = 'Error: One or many sellers have not specified needed information!';

// Validation messages
$_['ms_validate_default'] = 'The \'%s\' field is invalid';
$_['ms_validate_required'] = 'The \'%s\' field is required';
$_['ms_validate_alpha_numeric'] = 'The \'%s\' field may only contain alpha-numeric characters';
$_['ms_validate_max_len'] = 'The \'%s\' field needs to be \'%s\' or shorter in length';
$_['ms_validate_min_len'] = 'The \'%s\' field needs to be \'%s\' or longer in length';
$_['ms_validate_phone_number'] = 'The \'%s\' field is not a phone number';
$_['ms_validate_numeric'] = 'The \'%s\' field may only contain numeric characters';

// Seller group settings
$_['ms_seller_group_product_number_limit'] = 'Max product number';

// Category-based and product-based fees
$_['ms_fees_heading'] = 'MultiMerch fees';
$_['ms_config_fee_priority'] = 'Fee priority';
$_['ms_config_fee_priority_catalog'] = 'Catalog';
$_['ms_config_fee_priority_vendor'] = 'Vendor';
$_['ms_config_fee_priority_note'] = 'With \'Catalog\' option selected, category/product listing and sale fees will have priority over the vendor / vendor group fee settings (vice-versa with \'Vendor\' option selected)';

// Seller attributes
$_['ms_global_attribute'] = '--Global--';
$_['ms_catalog_attribute_attach_to_seller'] = 'Attach to seller';
$_['ms_catalog_attribute_all_sellers'] = '--All sellers--';
$_['ms_seller_attribute'] = 'Attribute';
$_['ms_seller_attribute_group'] = 'Attribute group';
$_['ms_seller_attribute_manage'] = 'Manage attributes';

$_['ms_seller_attribute_updated'] = 'Success: Attribute(s) updated!';
$_['ms_seller_attribute_deleted'] = 'Success: Attribute(s) deleted!';
$_['ms_seller_attribute_group_updated'] = 'Success: Attribute group(s) updated!';
$_['ms_seller_attribute_group_deleted'] = 'Success: Attribute group(s) deleted!';

$_['ms_seller_attribute_error_creating'] = 'Error creating attribute!';
$_['ms_seller_attribute_error_updating'] = 'Error updating attribute!';
$_['ms_seller_attribute_error_deleting'] = 'Error deleting attribute!';
$_['ms_seller_attribute_error_assigned'] = 'Warning: Attribute `%s` cannot be deleted as it is currently assigned to %s products!';
$_['ms_seller_attribute_error_not_selected'] = 'You must select at least one attribute!';

$_['ms_seller_attribute_group_error_creating'] = 'Error creating attribute group!';
$_['ms_seller_attribute_group_error_updating'] = 'Error updating attribute group!';
$_['ms_seller_attribute_group_error_deleting'] = 'Error deleting attribute group!';
$_['ms_seller_attribute_group_error_assigned'] = 'Warning: Attribute group `%s` cannot be deleted as it is currently assigned to %s attributes!';
$_['ms_seller_attribute_group_error_not_selected'] = 'You must select at least one attribute group!';

$_['ms_seller_attribute_status_' . MsAttribute::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_APPROVED] = 'Approved';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_ACTIVE] = 'Active';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_INACTIVE] = 'Inactive';

// Seller options
$_['ms_global_option'] = '--Global--';
$_['ms_catalog_option_attach_to_seller'] = 'Attach to seller';
$_['ms_catalog_option_all_sellers'] = '--All sellers--';
$_['ms_seller_option_heading'] = 'Options';
$_['ms_seller_option_breadcrumbs'] = 'Options';
$_['ms_seller_option'] = 'Option';
$_['ms_seller_option_values'] = 'Option values';
$_['ms_seller_option_manage'] = 'Manage options';

$_['ms_seller_option_updated'] = 'Success: Option(s) updated!';
$_['ms_seller_option_deleted'] = 'Success: Option(s) deleted!';

$_['ms_seller_option_error_creating'] = 'Error creating option!';
$_['ms_seller_option_error_updating'] = 'Error updating option!';
$_['ms_seller_option_error_deleting'] = 'Error deleting option!';
$_['ms_seller_option_error_assigned'] = 'Warning: Option `%s` cannot be deleted as it is currently assigned to %s products!';
$_['ms_seller_option_error_not_selected'] = 'You must select at least one option!';

$_['ms_seller_option_status_' . MsOption::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_option_status_' . MsOption::STATUS_APPROVED] = 'Approved';
$_['ms_seller_option_status_' . MsOption::STATUS_ACTIVE] = 'Active';
$_['ms_seller_option_status_' . MsOption::STATUS_INACTIVE] = 'Inactive';

// Seller categories
$_['ms_global_category'] = '--Global--';
$_['ms_catalog_category_attach_to_seller'] = 'Attach to seller';
$_['ms_catalog_category_all_sellers'] = '--All sellers--';
$_['ms_seller_category_heading'] = 'Categories';
$_['ms_seller_category_breadcrumbs'] = 'Categories';
$_['ms_seller_category'] = 'Category';
$_['ms_seller_category_manage'] = 'Manage categories';

$_['ms_seller_newcategory_heading'] = 'Add new seller category';
$_['ms_seller_editcategory_heading'] = 'Edit seller category';
$_['ms_seller_category_general'] = 'General';
$_['ms_seller_category_name'] = 'Name';
$_['ms_seller_category_description'] = 'Description';
$_['ms_seller_category_meta_title'] = 'Meta title';
$_['ms_seller_category_meta_description'] = 'Meta description';
$_['ms_seller_category_meta_keyword'] = 'Meta keywords';
$_['ms_seller_category_data'] = 'Data';
$_['ms_seller_category_seller'] = 'Seller';
$_['ms_seller_category_parent'] = 'Parent';
$_['ms_seller_category_filter'] = 'Filters';
$_['ms_seller_category_store'] = 'Stores';
$_['ms_seller_category_keyword'] = 'SEO URL';
$_['ms_seller_category_image'] = 'Image';
$_['ms_seller_category_sort_order'] = 'Sort order';
$_['ms_seller_category_status'] = 'Status';

$_['ms_seller_category_created'] = 'Success: Category created!';
$_['ms_seller_category_updated'] = 'Success: Category updated!';
$_['ms_seller_category_deleted'] = 'Success: Category deleted!';

$_['ms_seller_category_error_creating'] = 'Error creating category!';
$_['ms_seller_category_error_updating'] = 'Error updating category!';
$_['ms_seller_category_error_deleting'] = 'Error deleting category!';
$_['ms_seller_category_error_assigned'] = 'Warning: This category cannot be deleted as it is currently assigned to %s products!';
$_['ms_seller_category_error_no_sellers'] = 'No sellers available';
$_['ms_seller_category_error_not_selected'] = 'You must select at least one category!';

$_['ms_seller_category_status_' . MsCategory::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_category_status_' . MsCategory::STATUS_APPROVED] = 'Approved';
$_['ms_seller_category_status_' . MsCategory::STATUS_ACTIVE] = 'Active';
$_['ms_seller_category_status_' . MsCategory::STATUS_INACTIVE] = 'Inactive';

// Sale > Order > Info
$_['ms_order_details_by_seller'] = 'Order details by seller';
$_['ms_order_products_by'] = 'Seller:';
$_['ms_order_id'] = "Seller's unique order number:";
$_['ms_order_current_status'] = "Seller's current order status:";

$_['ms_order_transactions'] = "Seller's balance transactions for this order";
$_['ms_order_transactions_amount'] = 'Amount';
$_['ms_order_transactions_description'] = 'Description';
$_['ms_order_date_created'] = 'Date created';
$_['ms_order_notransactions'] = 'Seller has not yet received any balance transactions for this order';

$_['ms_order_status_initial'] = 'Order created';
$_['ms_order_history'] = "Seller's order status history";
?>
