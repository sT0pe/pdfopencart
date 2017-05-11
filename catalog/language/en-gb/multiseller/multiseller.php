<?php

$_['ms_language_version'] = '1.0.7.11';

// **********
// * Global *
// **********
$_['ms_viewinstore'] = 'View in store';
$_['ms_view'] = 'View';
$_['ms_view_modify'] = 'View / Modify';
$_['ms_view_invoice'] = 'View invoice';
$_['ms_publish'] = 'Publish';
$_['ms_unpublish'] = 'Unpublish';
$_['ms_activate'] = 'Activate';
$_['ms_deactivate'] = 'Deactivates';
$_['ms_edit'] = 'Edit';
$_['ms_relist'] = 'Relist';
$_['ms_delete'] = 'Delete';
$_['ms_type'] = 'Type';
$_['ms_amount'] = 'Amount';
$_['ms_status'] = 'Status';
$_['ms_date_paid'] = 'Date paid';
$_['ms_last_message'] = 'Last message';
$_['ms_description'] = 'Description';
$_['ms_id'] = '#';
$_['ms_name'] = 'Name';
$_['ms_by'] = 'by';
$_['ms_action'] = 'Action';
$_['ms_sender'] = 'Sender';
$_['ms_message'] = 'Message';
$_['ms_none'] = 'None';
$_['ms_drag_drop_here'] = 'Drop files here to upload';
$_['ms_drag_drop_click_here'] = 'Drop image here or click to upload';
$_['ms_or'] = 'or';
$_['ms_select_files'] = 'Select files';
$_['ms_allowed_extensions'] = 'Allowed extensions: %s';
$_['ms_all_products'] = 'All products';
$_['ms_from'] = 'From';
$_['ms_to'] = 'To';
$_['ms_store_owner'] = 'Store owner';
$_['ms_sort_order'] = 'Sort order';
$_['ms_yes'] = 'Yes';
$_['ms_no'] = 'No';
$_['ms_add'] = 'Add';

$_['ms_date_created'] = 'Date';
$_['ms_date_added'] = 'Date added';
$_['ms_date_modified'] = 'Date modified';
$_['ms_date'] = 'Date';

$_['ms_button_submit'] = 'Submit';
$_['ms_button_add_special'] = '+ Add special';
$_['ms_button_add_discount'] = '+ Add discount';
$_['ms_button_save'] = 'Save';
$_['ms_button_cancel'] = 'Cancel';
$_['ms_button_pay'] = 'Pay';

$_['ms_button_select_image'] = 'Select image';

$_['ms_transaction_order_created'] = 'Order created';
$_['ms_transaction_order'] = 'Sale: Order Id #%s';
$_['ms_transaction_sale'] = 'Sale: %s (-%s commission)';
$_['ms_transaction_refund'] = 'Refund: %s';
$_['ms_transaction_shipping'] = 'Shipping: %s';
$_['ms_transaction_shipping_refund'] = 'Shipping refund: %s';


// Mails

// Seller
$_['ms_mail_greeting'] = "Hello %s,\n";
$_['ms_mail_greeting_no_name'] = "Hello,\n";
$_['ms_mail_ending'] = "\nRegards,\n%s";
$_['ms_mail_message'] = "\nMessage:\n%s";

$_['ms_mail_subject_seller_account_created'] = 'Seller account created';
$_['ms_mail_seller_account_created'] = <<<EOT
Your seller account at %s has been created!

You can now start adding your products.
EOT;

$_['ms_mail_subject_seller_account_awaiting_moderation'] = 'Seller account awaiting moderation';
$_['ms_mail_seller_account_awaiting_moderation'] = <<<EOT
Your seller account at %s has been created and is now awaiting moderation.

You will receive an email as soon as it is approved.
EOT;

$_['ms_mail_subject_product_awaiting_moderation'] = 'Product awaiting moderation';
$_['ms_mail_product_awaiting_moderation'] = <<<EOT
Your product %s at %s is awaiting moderation.

You will receive an email as soon as it is processed.
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

$_['ms_mail_subject_seller_contact'] = 'New customer message';
$_['ms_mail_seller_contact'] = <<<EOT
You have received a new customer message!

Name: %s

Email: %s

Product: %s

Message:
%s
EOT;

$_['ms_mail_seller_contact_no_mail'] = <<<EOT
You have received a new customer message!

Name: %s

Product: %s

Message:
%s
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

$_['ms_mail_subject_product_returned'] = 'Product return requested';
$_['ms_mail_product_returned'] = <<<EOT
Customer has requested to return your product %s. The administrator of %s will get in touch with you shortly.
EOT;

$_['ms_mail_subject_withdraw_request_submitted'] = 'Payout request submitted';
$_['ms_mail_withdraw_request_submitted'] = <<<EOT
We have received your payout request. You will receive your earnings as soon as it is processed.
EOT;

$_['ms_mail_subject_withdraw_request_completed'] = 'Payout completed';
$_['ms_mail_withdraw_request_completed'] = <<<EOT
Your payout request has been processed. You should now receive your earnings.
EOT;

$_['ms_mail_subject_withdraw_request_declined'] = 'Payout request declined';
$_['ms_mail_withdraw_request_declined'] = <<<EOT
Your payout request has been declined. Your funds have been returned to your balance at %s.
EOT;

$_['ms_mail_subject_transaction_performed'] = 'New transaction';
$_['ms_mail_transaction_performed'] = <<<EOT
New transaction has been added to your account at %s.
EOT;

$_['ms_mail_subject_remind_listing'] = 'Product listing has finished';
$_['ms_mail_seller_remind_listing'] = <<<EOT
Your product's %s listing has finished. Go to your account seller area if you would like to re-list the product.
EOT;

// *********
// * Admin *
// *********
$_['ms_mail_admin_subject_seller_account_created'] = 'New seller account created';
$_['ms_mail_admin_seller_account_created'] = <<<EOT
New seller account at %s has been created!
Seller name: %s (%s)
E-mail: %s
EOT;

$_['ms_mail_admin_subject_seller_account_awaiting_moderation'] = 'New seller account awaiting moderation';
$_['ms_mail_admin_seller_account_awaiting_moderation'] = <<<EOT
New seller account at %s has been created and is now awaiting moderation.
Seller name: %s (%s)
E-mail: %s

You can process it in the Multiseller - Sellers section in back office.
EOT;

$_['ms_mail_admin_subject_product_created'] = 'New product added';
$_['ms_mail_admin_product_created'] = <<<EOT
New product %s has been added to %s.

You can view or edit it in back office.
EOT;

$_['ms_mail_admin_subject_new_product_awaiting_moderation'] = 'New product awaiting moderation';
$_['ms_mail_admin_new_product_awaiting_moderation'] = <<<EOT
New product %s has been added to %s and is awaiting moderation.

You can process it in the Multiseller - Products section in back office.
EOT;

$_['ms_mail_admin_subject_edit_product_awaiting_moderation'] = 'Product edited and awaiting moderation';
$_['ms_mail_admin_edit_product_awaiting_moderation'] = <<<EOT
Product %s at %s has been edited and is awaiting moderation.

You can process it in the Multiseller - Products section in back office.
EOT;

$_['ms_mail_admin_subject_withdraw_request_submitted'] = 'Payout request awaiting moderation';
$_['ms_mail_admin_withdraw_request_submitted'] = <<<EOT
New payout request has been submitted.

You can process it in the Multiseller - Finances section in back office.
EOT;

// Catalog - Mail
// Attributes
$_['ms_mail_subject_attribute_created'] = 'New attribute created by vendor %s: %s';
$_['ms_mail_attribute_created'] = <<<EOT
New attribute has been created by vendor <strong>%s</strong>: <strong>%s</strong>.

Please click <a href="%s">this link</a> to approve, decline and manage attributes.
EOT;

// Attribute groups
$_['ms_mail_subject_attribute_group_created'] = 'New attribute group created by vendor %s: %s';
$_['ms_mail_attribute_group_created'] = <<<EOT
New attribute group has been created by vendor <strong>%s</strong>: <strong>%s</strong>.

Please click <a href="%s">this link</a> to approve, decline and manage attribute groups.
EOT;

// Options
$_['ms_mail_subject_option_created'] = 'New option created by vendor %s: %s';
$_['ms_mail_option_created'] = <<<EOT
New option has been created by vendor <strong>%s</strong>: <strong>%s</strong>.

Please click <a href="%s">this link</a> to approve, decline and manage options.
EOT;

// Categories
$_['ms_mail_subject_category_created'] = 'New category created by vendor %s: %s';
$_['ms_mail_category_created'] = <<<EOT
New category has been created by vendor <strong>%s</strong>: <strong>%s</strong>.

Please click <a href="%s">this link</a> to approve, decline and manage categories.
EOT;


// Success
$_['ms_success_product_published'] = 'Product published';
$_['ms_success_product_unpublished'] = 'Product unpublished';
$_['ms_success_product_created'] = 'Product created';
$_['ms_success_product_updated'] = 'Product updated';
$_['ms_success_product_deleted'] = 'Product deleted';

// Errors
$_['ms_error_sellerinfo_nickname_empty'] = 'Nickname cannot be empty';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'Nickname can only contain alphanumeric symbols';
$_['ms_error_sellerinfo_nickname_utf8'] = 'Nickname can only contain printable UTF-8 symbols';
$_['ms_error_sellerinfo_nickname_latin'] = 'Nickname can only contain alphanumeric symbols and diacritics';
$_['ms_error_sellerinfo_nickname_length'] = 'Nickname should be between 4 and 50 characters';
$_['ms_error_sellerinfo_nickname_taken'] = 'This nickname is already taken';
$_['ms_error_sellerinfo_description_length'] = 'Description cannot be longer than 5000 characters';
$_['ms_error_sellerinfo_terms'] = 'Warning: You must agree to the %s!';
$_['ms_error_file_extension'] = 'Invalid extension';
$_['ms_error_file_type'] = 'Invalid file type';
$_['ms_error_file_size'] = 'File too big';
$_['ms_error_image_too_small'] = 'Image file dimensions are too small. Minimum allowed size is: %s x %s (Width x Height)';
$_['ms_error_image_too_big'] = 'Image file dimensions are too big. Maximum allowed size is: %s x %s (Width x Height)';
$_['ms_error_file_upload_error'] = 'File upload error';
$_['ms_error_form_submit_error'] = 'Error occurred when submitting the form. Please contact the store owner for more information.';
$_['ms_error_form_notice'] = 'Please check all form tabs for errors.';
$_['ms_error_product_price_empty'] = 'Please specify a price for your product';
$_['ms_error_product_price_invalid'] = 'Invalid price';
$_['ms_error_product_price_low'] = 'Price too low';
$_['ms_error_product_price_high'] = 'Price too high';
$_['ms_error_product_category_empty'] = 'Please select a category';
$_['ms_error_product_model_empty'] = 'Product model cannot be empty';
$_['ms_error_product_model_length'] = 'Product model should be between %s and %s characters';
$_['ms_error_product_image_count'] = 'Please upload at least %s image(s) for your product';
$_['ms_error_product_download_count'] = 'Please submit at least %s download(s) for your product';
$_['ms_error_product_image_maximum'] = 'No more than %s image(s) allowed';
$_['ms_error_product_download_maximum'] = 'No more than %s download(s) allowed';
$_['ms_error_contact_text'] = 'Message cannot be longer than 2000 characters';
$_['ms_error_contact_allfields'] = 'Please fill in all fields';
$_['ms_error_invalid_quantity_discount_priority'] = 'Error in priority field - please enter correct value';
$_['ms_error_invalid_quantity_discount_quantity'] = 'Quantity should be 2 or greater';
$_['ms_error_invalid_quantity_discount_price'] = 'Invalid quantity discount price entered';
$_['ms_error_invalid_quantity_discount_dates'] = 'Date fields for quantity discounts must be filled in';
$_['ms_error_invalid_special_price_priority'] = 'Error in priority field - please enter correct value';
$_['ms_error_invalid_special_price_price'] = 'Invalid special price entered';
$_['ms_error_invalid_special_price_dates'] = 'Date fields for special prices must be filled in';
$_['ms_error_slr_gr_product_number_limit_exceeded'] = 'You have created the maximum number of products allowed for your vendor account. Please contact us for more information on lifting product limits.';
$_['ms_error_fixed_coupon_warning'] = "Warning: This coupon code can not be used at this time!";
$_['ms_error_voucher_warning'] = "Warning: This gift certificate can not be used at this time!";
$_['ms_error_product_forbid_to_buy_own_product'] = "You can not purchase your own products!";

// Account - General
$_['ms_account_register_new'] = 'New Seller';
$_['ms_account_register_seller'] = 'Register seller account';
$_['ms_account_register_seller_note'] = 'Create a seller account and start selling your products in our store!';
$_['ms_account_register_details'] = 'Step 1: Your Details';


$_['ms_account_profile_general'] = "General information";
$_['ms_account_profile_group'] = "Choose your plan";
$_['ms_account_group_signup fee'] = '%s signup fee';
$_['ms_account_group_listing_fee'] = '%s listing fee';
$_['ms_account_group_sale_fee'] = '%s + %s %% sale fee';
$_['ms_account_group_select_plan'] = "Select plan";
$_['ms_seller'] = 'Seller';
$_['ms_catalog'] = 'Catalog';
$_['ms_account_dashboard'] = 'Dashboard';
$_['ms_account_customer'] = 'Customer';
$_['ms_account_my_account'] = 'My account';
$_['ms_account_overview'] = 'Overview';
$_['ms_account_sellerinfo'] = 'Seller profile';
$_['ms_account_sellerinfo_new'] = 'Create a new seller account';
$_['ms_account_sellerinfo_new_short'] = 'New account';
$_['ms_account_newproduct'] = 'Add new product';
$_['ms_account_products'] = 'Products';
$_['ms_account_transactions'] = 'Transactions';
$_['ms_account_payments'] = 'Payments';
$_['ms_account_payment_requests'] = 'Payment requests';
$_['ms_account_orders'] = 'Orders';
$_['ms_account_revenue'] = 'Revenue';
$_['ms_account_views'] = 'Views';
$_['ms_account_withdraw'] = 'Request payout';
$_['ms_account_stats'] = 'Statistics';
$_['ms_account_settings'] = 'Settings';
$_['ms_account_profile'] = 'Profile';
$_['ms_account_member_since'] = 'Member since:';
$_['ms_account_dashboard_in_month'] = 'this month';
$_['ms_account_no_orders'] = 'You have no orders.';
$_['ms_account_general'] = 'General';


// customer account

$_['ms_account_edit_info'] = 'Edit information';
$_['ms_account_password'] = 'Change password';
$_['ms_account_wishlist'] = 'Wishlist';
$_['ms_account_newsletter'] = 'Newsletter';
$_['ms_account_address_book'] = 'Address book';
$_['ms_account_order_history'] = 'Order history';
$_['ms_account_reward_points'] = 'Reward points';
$_['ms_account_transactions'] = 'Transactions';
$_['ms_account_returns'] = 'Returns';
$_['ms_account_logout'] = 'Logout';

// Disqus
$_['mxt_disqus_comments'] = 'Disqus Comments';

// Analytics
$_['mxt_google_analytics'] = 'Google Analytics';
$_['mxt_google_analytics_code'] = 'Tracking ID';
$_['mxt_google_analytics_code_note'] = 'Specify your Google Analytics tracking ID to track your product and profile performance';

// Badges

// Soclal links
$_['ms_sl_social_media'] = 'Social Media';

// Account - New product
$_['ms_account_newproduct_heading'] = 'New Product';
//General Tab
$_['ms_account_product_tab_specials'] = 'Special prices';
$_['ms_account_product_tab_discounts'] = 'Quantity discounts';
$_['ms_account_product_name_description'] = 'General information';
$_['ms_account_product_additional_data'] = 'Additional data';
$_['ms_account_product_search_optimization'] = 'Search optimization';

$_['ms_account_product_name'] = 'Name';
$_['ms_account_product_name_note'] = 'Specify the name of your product';

$_['ms_account_product_description'] = 'Description';
$_['ms_account_product_description_note'] = 'Describe your product. Make sure your description is complete and accurate to attract more potential buyers';

$_['ms_account_product_meta_description'] = 'Meta Description';
$_['ms_account_product_meta_description_note'] = 'Meta Description is used by search engines to describe your product in search results. No formatting';

$_['ms_account_product_seo_keyword'] = 'SEO Keyword';
$_['ms_account_product_seo_keyword_note'] = 'This will appear in the URL of your product page. Don\'t use spaces or special characters';

$_['ms_account_product_meta_title'] = 'Meta Title';
$_['ms_account_product_meta_title_note'] = 'Meta Title is what will appear in the title of your product listing page';

$_['ms_account_product_meta_keyword'] = 'Meta Tag Keywords';
$_['ms_account_product_meta_keyword_note'] = 'Meta Keywords may be used by search engines to determine what your product listing is about';

$_['ms_account_product_tags'] = 'Tags';
$_['ms_account_product_tags_note'] = 'Specify a list of comma-separated tags that describe your product the best';

$_['ms_account_product_price'] = 'Price';
$_['ms_account_product_price_note'] = 'Specify the price of your product (e.g. %s1%s000%s00%s)';

$_['ms_account_product_digital'] = 'Digital product';

$_['ms_account_product_attribute'] = 'Attribute';
$_['ms_account_product_attributes'] = 'Attributes';
$_['ms_account_product_value'] = 'Value';
$_['ms_account_product_new_attribute'] = '+ Add attribute';

$_['ms_account_product_listing_balance'] = 'This amount will be deducted from your seller balance.';
$_['ms_account_product_listing_pg'] = 'You can find your fee requests in Payment requests section.';

$_['ms_account_product_listing_flat'] = 'Listing fee for this product is <span>%s</span>';
$_['ms_account_product_listing_percent'] = 'Listing fee for this product is based on the product price. Current listing fee: <span>%s</span>.';

$_['ms_account_product_listing_category_note'] = 'Please, select category to get fee rates.';
$_['ms_account_product_listing_category'] = 'Listing fee for this product is based on the category. Current listing fee: %s.';

$_['ms_account_product_categories'] = 'Marketplace categories';
$_['ms_account_product_vendor_categories'] = 'My categories';
$_['ms_account_product_category_select'] = '-- Select category --';
$_['ms_account_product_category_note'] = 'Choose a category for your product. This will allow the buyers find your item';
$_['ms_account_product_vendor_category_note'] = 'Choose a category for your product from your own categories list';
$_['ms_account_product_quantity'] = 'Quantity';
$_['ms_account_product_quantity_note']    = 'Specify the quantity of your product';
$_['ms_account_product_minorderqty'] = 'Minimum quantity';
$_['ms_account_product_minorderqty_note']    = 'Specify the minimum order quantity of your product';
$_['ms_account_product_files'] = 'Files';
$_['ms_account_product_download'] = 'Downloads';
$_['ms_account_product_download_note'] = 'Upload files for your product. Allowed extensions: %s';
$_['ms_account_product_image'] = 'Images';
$_['ms_account_product_image_note'] = 'Select images for your product. First image will be used as a thumbnail. You can change the order of the images by dragging them. Allowed extensions: %s';
//Data Tab
$_['ms_account_product_model'] = 'Model';
$_['ms_account_product_sku'] = 'SKU';
$_['ms_account_product_sku_note'] = 'Stock Keeping Unit';
$_['ms_account_product_upc']  = 'UPC';
$_['ms_account_product_upc_note'] = 'Universal Product Code';
$_['ms_account_product_ean'] = 'EAN';
$_['ms_account_product_ean_note'] = 'European Article Number';
$_['ms_account_product_jan'] = 'JAN';
$_['ms_account_product_jan_note'] = 'Japanese Article Number';
$_['ms_account_product_isbn'] = 'ISBN';
$_['ms_account_product_isbn_note'] = 'International Standard Book Number';
$_['ms_account_product_mpn'] = 'MPN';
$_['ms_account_product_mpn_note'] = 'Manufacturer Part Number';
$_['ms_account_product_manufacturer'] = 'Manufacturer';
$_['ms_account_product_manufacturer_note'] = '(Autocomplete)';
$_['ms_account_product_tax_class'] = 'Tax Class';
$_['ms_account_product_date_available'] = 'Date Available';
$_['ms_account_product_stock_status'] = 'Out Of Stock Status';
$_['ms_account_product_subtract'] = 'Subtract Stock';
$_['ms_account_product_customer_group'] = 'Customer Group';


// Options
$_['ms_account_product_tab_options'] = 'Options';
$_['ms_options_add'] = '+ Add option';
$_['ms_options_add_value'] = '+ Add value';
$_['ms_options_price'] = 'Price';
$_['ms_options_subtract'] = 'Subtract';
$_['ms_options_quantity'] = 'Quantity';


$_['ms_account_product_manufacturer'] = 'Manufacturer';
$_['ms_account_product_manufacturer_note'] = '(Autocomplete)';
$_['ms_account_product_tax_class'] = 'Tax Class';
$_['ms_account_product_date_available'] = 'Date Available';
$_['ms_account_product_stock_status'] = 'Out Of Stock Status';
$_['ms_account_product_subtract'] = 'Subtract Stock';

$_['ms_account_product_priority'] = 'Priority';
$_['ms_account_product_date_start'] = 'Start date';
$_['ms_account_product_date_end'] = 'End date';



// Account - Edit product
$_['ms_account_editproduct_heading'] = 'Edit Product';

// Account - Seller
$_['ms_account_sellerinfo_heading'] = 'Seller Profile';
$_['ms_account_sellerinfo_breadcrumbs'] = 'Seller Profile';
$_['ms_account_sellerinfo_nickname'] = 'Nickname';
$_['ms_account_sellerinfo_nickname_note'] = 'Specify your seller nickname.';
$_['ms_account_sellerinfo_description'] = 'Description';
$_['ms_account_sellerinfo_description_note'] = 'Describe yourself';
$_['ms_account_sellerinfo_zone'] = 'Region / State';
$_['ms_account_sellerinfo_zone_select'] = 'Select region/state';
$_['ms_account_sellerinfo_zone_not_selected'] = 'No region/state selected';
$_['ms_account_sellerinfo_avatar'] = 'Avatar';
$_['ms_account_sellerinfo_avatar_note'] = 'Select your avatar';
$_['ms_account_sellerinfo_banner'] = 'Banner';
$_['ms_account_sellerinfo_banner_note'] = 'Upload a banner that will be displayed on your profile page';
$_['ms_account_sellerinfo_reviewer_message'] = 'Message to the reviewer';
$_['ms_account_sellerinfo_reviewer_message_note'] = 'Your message to the reviewer';
$_['ms_account_sellerinfo_terms'] = 'Accept terms';
$_['ms_account_sellerinfo_terms_note'] = 'I have read and agree to the <a class="agree" href="%s" alt="%s"><b>%s</b></a>';
$_['ms_account_sellerinfo_fee_flat'] = 'There is a signup fee of <span>%s</span> to become a seller at %s.';
$_['ms_account_sellerinfo_fee_balance'] = 'This amount will be deducted from your initial balance.';
$_['ms_account_sellerinfo_fee_pg'] = 'You can find your fee requests in Payment requests section.';
$_['ms_account_sellerinfo_saved'] = 'Seller account data saved.';

$_['ms_account_status'] = 'Your seller account status is: ';
$_['ms_account_status_tobeapproved'] = 'You will be able to use your account as soon as it is approved by the store owner.';
$_['ms_account_status_please_fill_in'] = 'Please complete the following form to create a seller account.';

$_['ms_seller_status_' . MsSeller::STATUS_ACTIVE] = 'Active';
$_['ms_seller_status_' . MsSeller::STATUS_INACTIVE] = 'Inactive';
$_['ms_seller_status_' . MsSeller::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_status_' . MsSeller::STATUS_INCOMPLETE] = 'Incomplete';
$_['ms_seller_status_' . MsSeller::STATUS_DELETED] = 'Deleted';
$_['ms_seller_status_' . MsSeller::STATUS_UNPAID] = 'Unpaid signup fee';

// Account - Products
$_['ms_account_products_heading'] = 'Your Products';
$_['ms_account_products_breadcrumbs'] = 'Your Products';
$_['ms_account_products_product'] = 'Product';
$_['ms_account_products_sales'] = 'Sales';
$_['ms_account_products_earnings'] = 'Earnings';
$_['ms_account_products_status'] = 'Status';
$_['ms_account_products_confirmdelete'] = 'Are you sure you want to delete your product?';

$_['ms_not_defined'] = 'Not defined';

$_['ms_product_status_' . MsProduct::STATUS_ACTIVE] = 'Active';
$_['ms_product_status_' . MsProduct::STATUS_INACTIVE] = 'Inactive';
$_['ms_product_status_' . MsProduct::STATUS_DISABLED] = 'Disabled';
$_['ms_product_status_' . MsProduct::STATUS_DELETED] = 'Deleted';
$_['ms_product_status_' . MsProduct::STATUS_UNPAID] = 'Unpaid listing fee';

// Account - Conversations and Messages
$_['ms_account_conversations'] = 'Conversations';
$_['ms_account_messages'] = 'Messages';
$_['ms_sellercontact_success'] = 'Your message has been successfully sent';

$_['ms_account_conversations_heading'] = 'Your Conversations';
$_['ms_account_conversations_breadcrumbs'] = 'Your Conversations';

$_['ms_account_conversations_status'] = 'Status';
$_['ms_account_conversations_with'] = 'Conversation with';
$_['ms_account_conversations_title'] = 'Title';
$_['ms_account_conversations_type'] = 'Conversation type';
$_['ms_account_conversations_textarea_placeholder'] = 'Enter your message...';

$_['ms_account_conversations_sender_type_' . MsConversation::SENDER_TYPE_CUSTOMER] = 'buyer';
$_['ms_account_conversations_sender_type_' . MsConversation::SENDER_TYPE_SELLER] = 'seller';
$_['ms_account_conversations_sender_type_' . MsConversation::SENDER_TYPE_ADMIN] = 'administrator';


$_['ms_conversation_title_product'] = 'Inquiry about product: %s';
$_['ms_conversation_title_order'] = 'Inquiry about order: %s';
$_['ms_conversation_title'] = 'Inquiry from %s';

$_['ms_account_conversations_read'] = 'Read';
$_['ms_account_conversations_unread'] = 'Unread';

$_['ms_account_conversations_start_with_seller'] = 'Start a new conversation with seller %s';
$_['ms_account_conversations_start_with_customer'] = 'Start a new conversation with customer %s';

$_['ms_account_messages_heading'] = 'Messages';

$_['ms_message_text'] = 'Your message';
$_['ms_post_message'] = 'Send message';

$_['ms_customer_does_not_exist'] = 'Customer account deleted';
$_['ms_error_empty_message'] = 'Message cannot be left empty';

$_['ms_mail_subject_private_message'] = 'New private message received';
$_['ms_mail_private_message'] = <<<EOT
You have received a new private message from %s!

%s

%s

You can reply in the messaging area in your account.
EOT;

$_['ms_mail_subject_order_updated'] = 'Your order #%s has been updated by %s';
$_['ms_mail_order_updated'] = <<<EOT
Your order at %s has been updated by %s:

Order#: %s

Products:
%s

Status: %s

Comment:
%s

EOT;

$_['ms_mail_subject_seller_vote'] = 'Vote for the seller';
$_['ms_mail_seller_vote_message'] = 'Vote for the seller';

// Account - Transactions
$_['ms_account_transactions_heading'] = 'Your Finances';
$_['ms_account_transactions_breadcrumbs'] = 'Your Finances';
$_['ms_account_transactions_earnings'] = 'Your earnings to date:';
$_['ms_account_transactions_description'] = 'Description';
$_['ms_account_transactions_amount'] = 'Amount';

// Payments
$_['ms_payment_payments_heading'] = 'Your payments';
$_['ms_payment_payments'] = 'Payments';

// Account - Orders
$_['ms_account_orders_heading'] = 'Your Orders';
$_['ms_account_orders_breadcrumbs'] = 'Your Orders';
$_['ms_account_orders_id'] = '#';
$_['ms_account_orders_customer'] = 'Customer';
$_['ms_account_orders_products'] = 'Products';
$_['ms_account_orders_transactions'] = 'Transactions';
$_['ms_account_orders_history'] = 'Order status history';
$_['ms_account_orders_marketplace_history'] = 'Marketplace status history';
$_['ms_account_orders_addresses'] = 'Addresses';
$_['ms_account_orders_total'] = 'Total';
$_['ms_account_orders_noorders'] = 'You don\'t have any orders yet!';
$_['ms_account_orders_notransactions'] = 'There are no balance transactions for this order yet!';
$_['ms_account_orders_nohistory'] = 'There is no history for this order yet!';
$_['ms_account_orders_comment'] = 'Comment';
$_['ms_account_orders_add_comment'] = 'Let the customer know why you are changing order status...';
$_['ms_account_orders_status_select_default'] = '-- Select new status --';
$_['ms_account_orders_change_status'] = 'Change order status';
$_['ms_account_orders_attachments']    = 'Attachments';


$_['ms_account_order_information'] = 'Order Information';

// Account - Dashboard
$_['ms_account_dashboard_heading'] = 'Seller Dashboard';
$_['ms_account_dashboard_breadcrumbs'] = 'Seller Dashboard';
$_['ms_account_dashboard_orders'] = 'Last orders';
$_['ms_account_dashboard_view_all_orders'] = 'View all orders';
$_['ms_account_sellersetting_breadcrumbs'] = 'Seller Settings';

// Account - Seller return
$_['ms_account_returns_heading'] = 'Product Returns';
$_['ms_account_returns_breadcrumbs'] = 'Seller Dashboard';
$_['ms_account_return_id'] = 'Return ID';
$_['ms_account_return_order_id'] = 'Order ID';
$_['ms_account_return_customer'] = 'Customer';
$_['ms_account_return_status'] = 'Status';
$_['ms_account_return_date_added'] = 'Date added';
$_['ms_account_return_customer_info'] = 'Order information';


//Account - Settings
$_['ms_seller_information'] = "Information";
$_['ms_seller_address'] = "Address (for invoicing purposes)";
$_['ms_seller_full_name'] = "Full name";
$_['ms_seller_address1'] = "Address Line 1";
$_['ms_seller_address1_placeholder'] = 'Street address, P.O. box, company name, c/o';
$_['ms_seller_address2'] = "Address Line 2";
$_['ms_seller_address2_placeholder'] = 'Apartment, suite, unit, building, floor etc.';
$_['ms_seller_city'] = "City";
$_['ms_seller_state'] = "State/Province/Region";
$_['ms_seller_zip'] = "ZIP/Postal Code";
$_['ms_seller_country'] = "Country";
$_['ms_success_settings_saved'] = "Settings successfully saved!";

$_['ms_seller_company'] = 'Company';
$_['ms_seller_website'] = 'Website';
$_['ms_seller_phone'] = 'Phone';
$_['ms_seller_logo'] = 'Logo';

$_['ms_account_sellerinfo_logo_note'] = "Select your logo (displayed in your invoices)";


// Account - Request withdrawal
$_['ms_account_withdraw_balance'] = 'Your current balance:';
$_['ms_account_balance_reserved_formatted'] = '-%s pending withdrawal';
$_['ms_account_balance_waiting_formatted'] = '-%s waiting period';

// Account - Stats
$_['ms_account_stats_heading'] = 'Statistics';
$_['ms_account_stats_breadcrumbs'] = 'Statistics';
$_['ms_account_stats_tab_summary'] = 'Summary';
$_['ms_account_stats_tab_by_product'] = 'By Product';
$_['ms_account_stats_tab_by_year'] = 'By Year';
$_['ms_account_stats_summary_comment'] = 'Below is a summary of your sales';
$_['ms_account_stats_sales_data'] = 'Sales data';
$_['ms_account_stats_number_of_orders'] = 'Number of orders';
$_['ms_account_stats_total_revenue'] = 'Total revenue';
$_['ms_account_stats_average_order'] = 'Average order';
$_['ms_account_stats_statistics'] = 'Statistics';
$_['ms_account_stats_grand_total'] = 'Grand total sales';
$_['ms_account_stats_product'] = 'Product';
$_['ms_account_stats_sold'] = 'Sold';
$_['ms_account_stats_total'] = 'Total';
$_['ms_account_stats_this_year'] = 'This Year';
$_['ms_account_stats_year_comment'] = '<span id="sales_num">%s</span> Sale(s) for specified period';
$_['ms_account_stats_show_orders'] = 'Show Orders From: ';
$_['ms_account_stats_month'] = 'Month';
$_['ms_account_stats_num_of_orders'] = 'Number of orders';
$_['ms_account_stats_total_r'] = 'Total revenue';
$_['ms_account_stats_average_order'] = 'Average order';
$_['ms_account_stats_today'] = 'Today, ';
$_['ms_account_stats_yesterday'] = 'Yesterday, ';
$_['ms_account_stats_daily_average'] = 'Daily average for ';
$_['ms_account_stats_date_month_format'] = 'm/Y';
$_['ms_account_stats_projected_totals'] = 'Projected totals for ';
$_['ms_account_stats_grand_total_sales'] = 'Grand total sales';

// Product page - Seller information
$_['ms_catalog_product_seller_information'] = 'Seller information';
$_['ms_catalog_product_contact'] = 'Contact this seller';

$_['ms_footer'] = '<br>MultiMerch Marketplace by <a href="http://multimerch.com/">multimerch.com</a>';

// Seller modules
$_['ms_newsellers_sellers'] = 'New sellers';

$_['ms_topsellers_sellers'] = 'Top sellers';

// Catalog - Sellers list
$_['ms_catalog_sellers_heading'] = 'Sellers';
$_['ms_sort_nickname_desc'] = 'Name (Z - A)';
$_['ms_sort_nickname_asc'] = 'Name (A - Z)';

// Catalog - Seller profile page
$_['ms_catalog_sellers'] = 'Sellers';
$_['ms_catalog_sellers_empty'] = 'There are no sellers yet.';
$_['ms_catalog_seller_profile'] = 'View profile';
$_['ms_catalog_seller_profile_heading'] = '%s\'s profile';
$_['ms_catalog_seller_profile_breadcrumbs'] = '%s\'s profile';

$_['ms_catalog_seller_profile_total_sales'] = 'Sales';
$_['ms_catalog_seller_profile_total_products'] = 'Products';
$_['ms_catalog_seller_profile_view_products'] = 'View products';
$_['ms_catalog_seller_profile_featured_products'] = 'Newly published products';
$_['ms_catalog_seller_profile_view'] = 'View all %s\'s products';
$_['ms_catalog_seller_profile_search'] = 'Search all products';
$_['ms_catalog_seller_profile_rating'] = 'Rating';
$_['ms_catalog_seller_profile_total_reviews'] = '(%s %s)';


// Catalog - Seller's products list
$_['ms_catalog_seller_products_heading'] = '%s\'s products';
$_['ms_catalog_seller_products_breadcrumbs'] = '%s\'s products';
$_['ms_catalog_seller_products_empty'] = 'This seller doesn\'t have any products yet!';

// Catalog - Seller contact dialog
$_['ms_sellercontact_signin'] = 'Please <a href="%s">sign in</a> to contact %s';
$_['ms_sellercontact_sendto'] = 'Send a message to %s';
$_['ms_sellercontact_text'] = 'Message: ';
$_['ms_sellercontact_close'] = 'Close';
$_['ms_sellercontact_send'] = 'Send';
$_['ms_sellercontact_success'] = 'Your message has been successfully sent';

// Product filters
$_['ms_entry_filter'] = 'Filters';
$_['ms_autocomplete'] = '(Autocomplete)';

// Related products
$_['ms_catalog_products_related_products']  = 'Related Products';

// Dimensions
$_['ms_catalog_products_measurements']    = 'Measurements';
$_['ms_catalog_products_size']          = 'Size (L x W x H)';
$_['ms_catalog_products_size_length']   = 'Length';
$_['ms_catalog_products_size_width']    = 'Width';
$_['ms_catalog_products_size_height']   = 'Height';
$_['ms_catalog_products_weight']        = 'Weight';

// Invoices
$_['heading_invoice_title']         = 'Order Invoice';
$_['column_total_shipping'] = 'Shipping';

// Validation
$_['ms_validate_default'] = 'The \'%s\' field is invalid';
$_['ms_validate_required'] = 'The \'%s\' field is required';
$_['ms_validate_alpha_numeric'] = 'The \'%s\' field may only contain alpha-numeric characters';
$_['ms_validate_max_len'] = 'The \'%s\' %s field needs to be \'%s\' or shorter in length';
$_['ms_validate_min_len'] = 'The \'%s\' %s field needs to be \'%s\' or longer in length';
$_['ms_validate_phone_number'] = 'The \'%s\' field is not a phone number';
$_['ms_validate_valid_url'] = 'The \'%s\' field must be a valid URL';
$_['ms_validate_numeric'] = 'The \'%s\' field may only contain numeric characters';


//Order history

$_['ms_order_placed'] = 'Order date:';
$_['ms_order_total'] = 'Total:';
$_['ms_order_dispatch'] = 'Dispatch to:';
$_['ms_order_details'] = 'Order details';
$_['ms_order_details_by_seller'] = 'Order details by seller';
$_['ms_order_products_by'] = 'Seller:';
$_['ms_order_id'] = "Seller's unique order number:";
$_['ms_order_current_status'] = "Seller's current order status:";
$_['ms_order_status'] = "Seller's order status:";
$_['ms_order_status_initial'] = 'Order created';
$_['ms_marketplace_order_status'] = 'Marketplace order status';
$_['ms_order_status_history'] = "Seller's order status history";
$_['ms_order_sold_by'] = 'Sold by:';
$_['ms_order_buy_again'] = 'Buy it again';
$_['ms_order_feedback'] = 'Leave feedback';
$_['ms_order_return'] = 'Return items';


// Questions
$_['mm_question_title'] = 'Questions';
$_['mm_question'] = 'Question';
$_['mm_question_posted_by'] = 'Posted by:';
$_['mm_question_answers'] = 'Answers';
$_['mm_question_answer_by'] = 'Answer by:';
$_['mm_question_no_answers'] = 'There are no answers for this question yet';
$_['mm_question_no_questions'] = 'There are no questions about this product yet';
$_['mm_question_write_answer'] = 'Answer this question';
$_['mm_question_submit'] = 'Submit';
$_['mm_question_ask'] = 'Ask your question about this product';
$_['mm_question_signin'] = 'Please sign in to ask a question.';

$_['question_title'] = 'Questions';
$_['posted_by'] = 'Posted by:';
$_['answer_by'] = 'Answer by:';
$_['no_answers'] = 'No answers =(';
$_['write_answer'] = 'Write your answer';

// Customer feedback
$_['ms_customer_product_rate_heading'] = 'Rate Your Experience';
$_['ms_customer_product_rate_stars_label'] = 'Please rate this transaction';
$_['ms_customer_product_rate_comments'] = 'Leave comments';
$_['ms_customer_product_rate_comments_placeholder'] = 'Please leave comments about your shopping experience with this seller';
$_['ms_customer_product_rate_quiz'] = 'Was the item description accurate?';
$_['ms_customer_product_rate_btn_submit'] = 'Submit Feedback';

// Reviews
$_['mm_review_comments_title'] = 'Customer Reviews';
$_['mm_review_rating_summary'] = '%s out of 5 (%s %s)';
$_['mm_review_stats_stars'] = '%s stars';
$_['mm_review_no_reviews'] = 'There are no reviews yet!';
$_['mm_review_seller_profile_history'] = 'Recent feedback history';
$_['mm_review_seller_profile_history_positive'] = 'Positive (4-5 stars)';
$_['mm_review_seller_profile_history_neutral'] = 'Neutral (3 stars)';
$_['mm_review_seller_profile_history_negative'] = 'Negative (1-2 stars)';
$_['mm_review_one_month'] = '1 month';
$_['mm_review_three_months'] = '3 months';
$_['mm_review_six_months'] = '6 months';
$_['mm_review_twelve_months'] = '12 months';
$_['mm_review_submit_success'] = 'Thank you for submitting your feedback!';

// Seller > Account-Product > Shipping
$_['ms_account_product_tab_shipping'] = 'Shipping';
$_['ms_account_product_shipping_from'] = 'Shipping From';
$_['ms_account_product_shipping_free'] = 'Free Shipping';
$_['ms_account_product_shipping_free_note'] = 'Shipping prices will not be considered';
$_['ms_account_product_shipping_processing_time'] = 'Processing time';
$_['ms_account_product_shipping_locations_to'] = 'Ships to';
$_['ms_account_product_shipping_locations_destination'] = 'Destination';
$_['ms_account_product_shipping_locations_company'] = 'Shipping company';
$_['ms_account_product_shipping_locations_delivery_time'] = 'Delivery time';
$_['ms_account_product_shipping_locations_cost'] = 'Cost';
$_['ms_account_product_shipping_locations_cost_fixed_pwu'] = 'Cost (fixed + per weight unit)';
$_['ms_account_product_shipping_locations_additional_cost'] = 'Additional item';
$_['ms_account_product_shipping_locations_add_btn'] = '+ Add location';
$_['ms_account_product_shipping_elsewhere'] = 'Worldwide';

$_['ms_account_product_shipping_combined_enabled'] = 'Combined shipping is enabled, the shipping cost of this product will be calculated automatically.';
$_['ms_account_product_shipping_combined_override'] = 'Override combined rules';

// Seller > Account-Settings > Shipping
$_['ms_account_settings_shipping_settings'] = 'Shipping settings';
$_['ms_account_settings_shipping_weight'] = 'Weight (%s)';
$_['ms_account_settings_shipping_comment'] = 'Comment';
$_['ms_account_settings_shipping_add'] = '+ Add method';

// Seller > Account-Settings > Payments
$_['ms_account_setting_payments_tab'] = 'Payments';


// Product page > Shipping
$_['mm_product_shipping_title'] = 'Shipping information';
$_['mm_product_shipping_free'] = 'Free delivery';
$_['mm_product_shipping_from_country'] = 'Shipping country';
$_['mm_product_shipping_processing_time'] = 'Processing time';
$_['mm_product_shipping_processing_days'] = '%s %s';
$_['mm_product_shipping_locations_note'] = 'Estimated delivery times may vary, especially during peak periods.';
$_['mm_product_shipping_not_specified'] = 'Vendor has not specified delivery information for this product!';
$_['mm_product_shipping_digital_product'] = 'Product is digital and doesn\'t require shipping!';

// Checkout > Shipping
$_['mm_not_specified'] = 'Not specified';
$_['mm_checkout_shipping_ew_location_delivery_time_name'] = 'Delivery time depends on location';
$_['mm_checkout_shipping_delivery_details_title'] = 'Delivery address';
$_['mm_checkout_shipping_delivery_details_change'] = 'Change';
$_['mm_checkout_shipping_products_title'] = 'Select the <b>preferred delivery method</b> to use on each of the products';
$_['mm_checkout_shipping_products_price'] = 'Price: ';
$_['mm_checkout_shipping_products_quantity'] = 'Quantity: ';
$_['mm_checkout_shipping_products_seller'] = 'Sold by: ';
$_['mm_checkout_shipping_method_title'] = 'Please choose a delivery method';
$_['mm_checkout_shipping_method_title_shot'] = 'Delivery method';
$_['mm_checkout_shipping_method_free'] = 'Free delivery';
$_['mm_checkout_shipping_not_required'] = 'Shipping is not required.';
$_['mm_checkout_shipping_digital_products'] = 'Digital product(s). Shipping is not required.';
$_['mm_checkout_shipping_not_available'] = 'Delivery to your location is not available. Product will be removed from your cart.';
$_['mm_checkout_shipping_total'] = 'Shipping total: ';
$_['mm_checkout_shipping_product_delete_warning'] = 'Products that can not be delivered to your location will be removed from your cart.';
$_['mm_checkout_shipping_no_selected_methods'] = 'Error: You must select shipping methods for each product!';

$_['mm_checkout_shipping_error_maxweight_exceeded'] = 'The total weight of the products by %s (%s) in your cart exceeds the maximum weight allowed by this vendor (%s). Please remove some of the products by %s <a href="%s">from your cart</a> or the products will be removed automatically if you continue.';

// Account > Order history
$_['mm_account_order_shipping_cost'] = 'Shipping cost';
$_['mm_account_order_shipping_total'] = 'Shipping total';

// Payment requests
$_['ms_pg_request_description_signup'] = 'Signup fee at %s';
$_['ms_pg_request_description_listing'] = 'Product listing: <a href="%s">%s</a>';
$_['ms_pg_request_error_select_payment_request'] = 'You must select at least one payment request!';

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

$_['ms_pg_new_payment'] = 'New payment';
$_['ms_pg_payment_method'] = 'Payment method';
$_['ms_pg_payment_requests'] = 'Payment requests';
$_['ms_pg_payment_form_select_method'] = 'Select payment method';

$_['ms_pg_payment_type_' . MsPgPayment::TYPE_PAID_REQUESTS] = 'Paid payment requests';
$_['ms_pg_payment_type_' . MsPgPayment::TYPE_SALE] = 'Sales';

$_['ms_pg_payment_status_' . MsPgPayment::STATUS_INCOMPLETE] = '<p style="color: red">Incomplete</p>';
$_['ms_pg_payment_status_' . MsPgPayment::STATUS_COMPLETE] = '<p style="color: green">Complete</p>';
$_['ms_pg_payment_status_' . MsPgPayment::STATUS_WAITING_CONFIRMATION] = '<p style="color: blue">Waiting for confirmation</p>';

$_['ms_pg_payment_error_not_available'] = 'Sorry! No payment methods are available at the moment!';
$_['ms_pg_payment_error_no_method'] = 'Error: You should select payment method!';
$_['ms_pg_payment_error_receiver_data'] = 'Error: No receiver data!';
$_['ms_pg_payment_error_sender_data'] = 'Error: No sender data!';

// Combined shipping
$_['ms_account_setting_ssm_title'] = 'Combined shipping settings';
$_['ms_account_setting_ssm_success'] = 'You have successfully modified combined-shipping settings!';
$_['ms_account_setting_ssm_error_data'] = 'Error: No data to process!';
$_['ms_account_setting_ssm_error_methods'] = 'Error: No methods were specified!';
$_['ms_account_setting_ssm_error_location'] = 'You must select location!';
$_['ms_account_setting_ssm_error_method'] = 'You must select method!';
$_['ms_account_setting_ssm_error_delivery_time'] = 'You must select delivery time!';
$_['ms_account_setting_ssm_error_weight'] = 'You must specify weight ranges!';
$_['ms_account_setting_ssm_error_cost'] = 'You must specify cost!';
$_['ms_account_setting_ssm_error_no_sm'] = 'No shipping methods are available at the moment!';
$_['ms_account_setting_ssm_error_no_dt'] = 'No delivery times are available at the moment!';
$_['ms_account_setting_ssm_error_no_gz'] = 'No geo zones are available at the moment!';

$_['ms_product_ssm_weight_range_template'] = '%s - %s %s';

// Seller attibutes
$_['ms_account_attribute_heading'] = 'Your Attributes';
$_['ms_account_attribute_breadcrumbs'] = 'Your Attributes';
$_['ms_account_attribute'] = 'Attribute';
$_['ms_account_attributes'] = 'Attributes';
$_['ms_account_attribute_manage'] = 'Manage attributes';
$_['ms_account_attribute_new'] = 'New attribute';
$_['ms_account_attribute_name'] = 'Name';
$_['ms_account_newattribute_heading'] = 'New Attribute';
$_['ms_account_editattribute_heading'] = 'Edit Attribute';

$_['ms_account_attribute_name_note'] = 'Specify the name of your attribute';
$_['ms_account_attribute_attr_group_note'] = 'Attach your attribute to an existing attribute group or <a href="%s">create your own group</a>';
$_['ms_account_attribute_sort_order_note'] = 'Specify sort order for your attribute';

$_['ms_success_attribute_created'] = 'Attribute successfully created! You will be able to use it once it\'s approved by the marketplace administrator.';
$_['ms_success_attribute_updated'] = 'Attribute updated!';
$_['ms_success_attribute_deleted'] = 'Attribute deleted!';
$_['ms_success_attribute_activated'] = 'Attribute activated!';
$_['ms_success_attribute_deactivated'] = 'Attribute deactivated!';

$_['ms_error_attribute_assigned_to_products'] = 'Warning: This attribute cannot be deleted as it is currently assigned to %s products!';
$_['ms_error_attribute_id'] = 'Attribute id is not set!';

// Seller attibute groups
$_['ms_account_attribute_group_heading'] = 'Your Attribute groups';
$_['ms_account_attribute_group_breadcrumbs'] = 'Your Attribute groups';
$_['ms_account_attribute_group'] = 'Attribute group';
$_['ms_account_attribute_groups'] = 'Attribute groups';
$_['ms_account_attribute_group_new'] = 'New attribute group';
$_['ms_account_attribute_group_name'] = 'Name';
$_['ms_account_newattributegroup_heading'] = 'New Attribute group';
$_['ms_account_editattributegroup_heading'] = 'Edit Attribute group';

$_['ms_account_attribute_group_name_note'] = 'Specify the name of your attribute group';
$_['ms_account_attribute_group_sort_order_note'] = 'Specify sort order for your attribute group';

$_['ms_success_attribute_group_created'] = 'Attribute group successfully created! You will be able to use it once it\'s approved by the marketplace administrator.';
$_['ms_success_attribute_group_updated'] = 'Attribute group updated!';
$_['ms_success_attribute_group_deleted'] = 'Attribute group deleted!';
$_['ms_success_attribute_group_activated'] = 'Attribute group activated!';
$_['ms_success_attribute_group_deactivated'] = 'Attribute group deactivated!';

$_['ms_error_attribute_group_assigned_to_attributes'] = 'Warning: This attribute group cannot be deleted as it is currently assigned to %s attributes!';
$_['ms_error_attribute_group_id'] = 'Attribute group id is not set!';

$_['ms_seller_attribute_status_' . MsAttribute::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_APPROVED] = 'Approved';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_ACTIVE] = 'Active';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_INACTIVE] = 'Inactive';

// Seller options
$_['ms_account_option_heading'] = 'Your Options';
$_['ms_account_option_breadcrumbs'] = 'Your Options';
$_['ms_account_option'] = 'Option';
$_['ms_account_options'] = 'Options';
$_['ms_account_option_value'] = 'Option value';
$_['ms_account_option_values'] = 'Option values';
$_['ms_account_option_new'] = 'New option';
$_['ms_account_option_name'] = 'Name';
$_['ms_account_option_manage'] = 'Manage options';
$_['ms_account_newoption_heading'] = 'New Option';
$_['ms_account_editoption_heading'] = 'Edit Option';

$_['ms_account_option_name_note'] = 'Specify the name of your option';
$_['ms_account_option_sort_order_note'] = 'Specify sort order for your option';
$_['ms_account_option_type_note'] = 'Specify the type of your option';

$_['ms_success_option_created'] = 'Option successfully created! You will be able to use it once it\'s approved by the marketplace administrator.';
$_['ms_success_option_updated'] = 'Option updated!';
$_['ms_success_option_deleted'] = 'Option deleted!';
$_['ms_success_option_activated'] = 'Option activated!';
$_['ms_success_option_deactivated'] = 'Option deactivated!';

$_['ms_error_option_assigned_to_products'] = 'Warning: This option cannot be deleted as it is currently assigned to %s products!';
$_['ms_error_option_id'] = 'Attribute group id is not set!';

$_['ms_seller_option_status_' . MsOption::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_option_status_' . MsOption::STATUS_APPROVED] = 'Approved';
$_['ms_seller_option_status_' . MsOption::STATUS_ACTIVE] = 'Active';
$_['ms_seller_option_status_' . MsOption::STATUS_INACTIVE] = 'Inactive';

$_['ms_account_option_type_choose'] = 'Choose';
$_['ms_account_option_type_input'] = 'Input';
$_['ms_account_option_type_file'] = 'File';
$_['ms_account_option_type_date'] = 'Date';
$_['ms_account_option_type_select'] = 'Select';
$_['ms_account_option_type_radio'] = 'Radio';
$_['ms_account_option_type_checkbox'] = 'Checkbox';
$_['ms_account_option_type_text'] = 'Text';
$_['ms_account_option_type_textarea'] = 'Textarea';
$_['ms_account_option_type_file'] = 'File';
$_['ms_account_option_type_time'] = 'Time';
$_['ms_account_option_type_datetime'] = 'Date & Time';

// Seller categories
$_['ms_account_category_heading'] = 'Your Categories';
$_['ms_account_category_breadcrumbs'] = 'Your Categories';
$_['ms_account_category'] = 'Category';
$_['ms_account_categories'] = 'Categories';
$_['ms_account_category_manage'] = 'Manage categories';
$_['ms_account_category_new'] = 'New category';
$_['ms_account_newcategory_heading'] = 'New Category';
$_['ms_account_editcategory_heading'] = 'Edit Category';

$_['ms_account_category_name'] = 'Name';
$_['ms_account_category_name_note'] = 'Specify the name of your category';
$_['ms_account_category_description'] = 'Description';
$_['ms_account_category_description_note'] = 'Describe your category';

$_['ms_account_category_additional_data'] = 'Additional data';
$_['ms_account_category_parent'] = 'Parent';
$_['ms_account_category_no_parent'] = '-- None --';
$_['ms_account_category_parent_note'] = 'Specify parent category to build your own categories structure';
$_['ms_account_category_filters'] = 'Filters';

$_['ms_account_category_search_optimization'] = 'Search optimization';
$_['ms_account_category_meta_title'] = 'Meta title';
$_['ms_account_category_meta_title_note'] = 'Meta Title is what will appear in the title of your category listing page';
$_['ms_account_category_meta_description'] = 'Meta description';
$_['ms_account_category_meta_description_note'] = 'Meta Description is used by search engines to describe your category in search results. No formatting';
$_['ms_account_category_meta_keyword'] = 'Meta keywords';
$_['ms_account_category_meta_keyword_note'] = 'Meta Keywords may be used by search engines to determine what your category listing is about';
$_['ms_account_category_seo_keyword'] = 'SEO URL';
$_['ms_account_category_seo_keyword_note'] = "This will appear in the URL of your category page. Don't use spaces or special characters";

$_['ms_account_category_image'] = 'Image';
$_['ms_account_category_sort_order'] = 'Sort order';
$_['ms_account_category_sort_order_note'] = 'Specify sort order for your category';

$_['ms_success_category_created'] = 'Category successfully created! You will be able to use it once it\'s approved by the marketplace administrator.';
$_['ms_success_category_updated'] = 'Category updated!';
$_['ms_success_category_deleted'] = 'Category deleted!';
$_['ms_success_category_activated'] = 'Category activated!';
$_['ms_success_category_deactivated'] = 'Category deactivated!';

$_['ms_seller_category_status_' . MsCategory::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_category_status_' . MsCategory::STATUS_APPROVED] = 'Approved';
$_['ms_seller_category_status_' . MsCategory::STATUS_ACTIVE] = 'Active';
$_['ms_seller_category_status_' . MsCategory::STATUS_INACTIVE] = 'Inactive';

?>
