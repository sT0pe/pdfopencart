<?php echo $header; ?><?php echo $column_left; ?>
<!-- MultiMerch settings page -->
<div id="content" class="ms-settings">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button id="saveSettings" type="submit" form="form-store" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $ms_settings_heading; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <?php if (isset($error_warning)) { ?>
	  <div class="alert alert-danger ms_alert"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
	  </div>
  <?php } ?>
  <?php if (isset($success)) { ?>
	  <div class="alert alert-success ms_alert"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
	  </div>
  <?php } ?>

  <div class="mm_container">
	  <div class="sidebar">
		  <ul>
			  <!-- mm admin multiseller settings sidebar start -->
			  <li class="active"><a href="#tab-seller" data-toggle="tab"><?php echo $ms_menu_sellers; ?></a></li>
			  <li><a href="#tab-productform" data-toggle="tab"><?php echo $ms_menu_products; ?></a></li>
			  <li><a href="#tab-finances" data-toggle="tab"><?php echo $ms_config_finances; ?></a></li>
			  <li><a href="#tab-miscellaneous" data-toggle="tab"><?php echo $ms_config_miscellaneous; ?></a></li>
			  <li><a href="#tab-shipping" data-toggle="tab"><?php echo $ms_config_shipping; ?></a></li>
			  <li><a href="#tab-payment-gateways" data-toggle="tab"><?php echo $ms_menu_payment_gateway; ?></a></li>
			  <!-- mm admin multiseller settings sidebar end -->
		  </ul>
	  </div>

	  <div class="content">


        <form id="settings" method="post" enctype="multipart/form-data">
			<div class="tab-content">
				<!-- BEGIN SELLER TAB -->
				<div id="tab-seller" class="tab-pane active">
					<h4><?php echo $ms_config_general; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_validation; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<span class="arrow"></span>
								<select class="form-control" name="msconf_seller_validation">
								  <option value="1" <?php if($msconf_seller_validation == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_none; ?></option>
								  <!--<option value="2" <?php if($msconf_seller_validation == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_activation; ?></option>-->
								  <option value="3" <?php if($msconf_seller_validation == 3) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_approval; ?></option>
								</select>
							</div>

							<div class="comment"><?php echo $ms_config_seller_validation_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_terms_page; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<span class="arrow"></span>
								<select class="form-control" name="msconf_seller_terms_page">
									<option value="0"><?php echo $text_none; ?></option>
									<?php foreach ($informations as $information) { ?>
									<?php if ($information['information_id'] == $msconf_seller_terms_page) { ?>
									<option value="<?php echo $information['information_id']; ?>" selected="selected"><?php echo $information['title']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $information['information_id']; ?>"><?php echo $information['title']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
								</div>
							<div class="comment"><?php echo $ms_config_seller_terms_page_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_menu_badge; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $text_enabled; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_badge_enabled" value="1" <?php if($msconf_badge_enabled == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_badge_enabled" value="0" <?php if($msconf_badge_enabled == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
						</div>
					</div>

					<div class="form-group required">
						<label class="col-sm-3 control-label"><?php echo $ms_config_badge_size; ?></label>
						<div class="col-sm-9 control-inline">
							<input class="form-control-mini" type="text" name="msconf_badge_width" value="<?php echo $msconf_badge_width; ?>" size="3" />
							x
							<input class="form-control-mini" type="text" name="msconf_badge_height" value="<?php echo $msconf_badge_height; ?>" size="3" />
						</div>
					</div>

					<h4><?php echo $ms_menu_social_links; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $text_enabled; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_sl_status" value="1" <?php if($msconf_sl_status == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_sl_status" value="0" <?php if($msconf_sl_status == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_sl_icon_size; ?></label>
						<div class="col-sm-9 control-inline">
							<input class="form-control-mini" type="text" name="msconf_sl_icon_width" value="<?php echo $msconf_sl_icon_width; ?>" size="3" />
							x
							<input class="form-control-mini" type="text" name="msconf_sl_icon_height" value="<?php echo $msconf_sl_icon_height; ?>" size="3" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_sl_manage; ?></label>
						<div class="col-sm-9 control-inline">
							<a target="_blank" href="<?php echo $this->url->link('multimerch/social_link', 'token=' . $this->session->data['token'], 'SSL'); ?>"><button type="button" class="btn btn-primary pull-left"><i class="fa fa-gears"></i> <?php echo $ms_sl_manage; ?></button></a>
						</div>
					</div>

					<h4><?php echo $mxt_google_analytics; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $mxt_google_analytics_enable; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="mxtconf_ga_seller_enable" value="1" <?php if($mxtconf_ga_seller_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="mxtconf_ga_seller_enable" value="0" <?php if($mxtconf_ga_seller_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
						</div>
					</div>

					<h4><?php echo $ms_config_miscellaneous; ?></h4>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_change_group; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_change_group" value="1" <?php if ($msconf_change_group == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_change_group" value="0" <?php if ($msconf_change_group == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_change_group_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_change_nickname; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_change_seller_nickname" value="1" <?php if ($msconf_change_seller_nickname == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_change_seller_nickname" value="0" <?php if ($msconf_change_seller_nickname == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_seller_change_nickname_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_nickname_rules; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_nickname_rules" value="0" <?php if ($msconf_nickname_rules == 0) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_nickname_rules_alnum; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_nickname_rules" value="1" <?php if ($msconf_nickname_rules == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_nickname_rules_ext; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_nickname_rules" value="2" <?php if ($msconf_nickname_rules == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_nickname_rules_utf; ?></label>
							<div class="comment"><?php echo $ms_config_nickname_rules_note; ?></div>
						</div>
					</div>
				</div>
				<!-- END SELLER TAB -->

			 	<!-- BEGIN PRODUCT FORM TAB -->
			 	<div id="tab-productform" class="tab-pane">
					<h4><?php echo $ms_config_general; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_product_validation; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<span class="arrow"></span>
								<select class="form-control" name="msconf_product_validation">
								<option value="1" <?php if($msconf_product_validation == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_none; ?></option>
								<option value="2" <?php if($msconf_product_validation == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_approval; ?></option>
								</select>
								</div>
							<div class="comment"><?php echo $ms_config_product_validation_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_free_products; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<label class="radio-inline"><input type="radio" name="msconf_allow_free_products" value="1" <?php if($msconf_allow_free_products == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="msconf_allow_free_products" value="0" <?php if($msconf_allow_free_products == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							</div>
						</div>
					</div>

					<div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_minmax_product_price; ?></label>
						<div class="col-sm-9 control-inline">
							<span><?php echo $ms_config_min; ?></span> <input class="form-control-mini" type="text" name="msconf_minimum_product_price" value="<?php echo $msconf_minimum_product_price; ?>" size="4"/>
							<span><?php echo $ms_config_max; ?></span> <input class="form-control-mini" type="text" name="msconf_maximum_product_price" value="<?php echo $msconf_maximum_product_price; ?>" size="4"/>
							<div class="comment"><?php echo $ms_config_minmax_product_price_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_digital_products; ?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<label class="radio-inline"><input type="radio" name="msconf_allow_digital_products" value="1" <?php if($msconf_allow_digital_products == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="msconf_allow_digital_products" value="0" <?php if($msconf_allow_digital_products == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
								<div class="comment"><?php echo $ms_config_allow_digital_products_note; ?></div>
							</div>
						</div>
					</div>

					<h4><?php echo $ms_config_product_categories; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_seller_categories; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_categories" value="1" <?php if($msconf_allow_seller_categories == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_categories" value="0" <?php if($msconf_allow_seller_categories == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_allow_seller_categories_note; ?></div>
						</div>
					</div>

					<div class="form-group product_categories_type <?php echo (isset($msconf_allow_seller_categories) && $msconf_allow_seller_categories == 1) ? '' : 'hidden'; ?>">
						<label class="col-sm-3 control-label"><?php echo $ms_config_product_categories_type; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_product_category_type" value="1" <?php if($msconf_product_category_type == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_product_category_store; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_product_category_type" value="2" <?php if($msconf_product_category_type == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_product_category_seller; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_product_category_type" value="3" <?php if($msconf_product_category_type == 3) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_product_category_both; ?></label>
							<div class="comment"><?php echo $ms_config_product_categories_type_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_multiple_categories; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_allow_multiple_categories" value="1" <?php if($msconf_allow_multiple_categories == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_allow_multiple_categories" value="0" <?php if($msconf_allow_multiple_categories == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_allow_multiple_categories_note; ?></div>
					  	</div>
					</div>

		   			<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_restrict_categories; ?></label>
						<div class="col-sm-9">
                          <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
                            <?php foreach ($categories as $category) { ?>
                              <input type="checkbox" name="msconf_restrict_categories[]" value="<?php echo $category['category_id']; ?>" <?php if (isset($msconf_restrict_categories) && in_array($category['category_id'], $msconf_restrict_categories)) { ?>checked="checked"<?php } ?> /> <?php echo $category['name']; ?><br>
                            <?php } ?>
                          </div>
							<div class="comment"><?php echo $ms_config_restrict_categories_note; ?></div>
					  	</div>
					</div>

					<h4><?php echo $ms_config_product_attributes_options; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_attributes; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_attributes" value="1" <?php if($msconf_allow_seller_attributes == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_attributes" value="0" <?php if($msconf_allow_seller_attributes == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_allow_attributes_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_options; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_options" value="1" <?php if($msconf_allow_seller_options == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_allow_seller_options" value="0" <?php if($msconf_allow_seller_options == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_allow_options_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_product_fields; ?></h4>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $ms_config_product_included_fields; ?></label>
                        <div class="col-sm-9">
                          <div class="well well-sm" style="height: 150px; overflow: auto;">
                            <?php foreach ($product_included_fieds as $field_code=>$field_name) { ?>
                              <input type="checkbox" name="msconf_product_included_fields[]" value="<?php echo $field_code; ?>" <?php if (isset($msconf_product_included_fields) && in_array($field_code, $msconf_product_included_fields)) { ?>checked="checked"<?php } ?> /> <?php echo $field_name; ?><br>
                            <?php } ?>
                          </div>
							<div class="comment"><?php echo $ms_config_product_included_fields_note; ?></div>
                        </div>
                    </div>

					<h4><?php echo $ms_config_file_types; ?></h4>
					<div class="form-group">
						  <label class="col-sm-3 control-label"><?php echo $ms_config_allowed_image_types; ?></label>
						  <div class="col-sm-9">
						  	<input class="form-control" type="text" name="msconf_allowed_image_types" value="<?php echo $msconf_allowed_image_types; ?>" />
							  <div class="comment"><?php echo $ms_config_allowed_image_types_note; ?></div>
						  </div>
					</div>

					<div class="form-group">
						  <label class="col-sm-3 control-label"><?php echo $ms_config_allowed_download_types; ?></label>
						  <div class="col-sm-9">
						  	<input class="form-control" type="text" name="msconf_allowed_download_types" value="<?php echo $msconf_allowed_download_types; ?>" />
							  <div class="comment"><?php echo $ms_config_allowed_download_types_note; ?></div>
						  </div>
					</div>

					<h4><?php echo $ms_config_limits; ?></h4>
					<div class="form-group">
						  <label class="col-sm-3 control-label"><?php echo $ms_config_images_limits; ?></label>
						  <div class="col-sm-9 control-inline">
						    <span><?php echo $ms_config_min; ?></span> <input class="form-control-mini" type="text" name="msconf_images_limits[]" value="<?php echo $msconf_images_limits[0]; ?>" size="3" />
						    <span><?php echo $ms_config_max; ?></span> <input class="form-control-mini" type="text" name="msconf_images_limits[]" value="<?php echo $msconf_images_limits[1]; ?>" size="3" />
							  <div class="comment"><?php echo $ms_config_images_limits_note; ?></div>
						  </div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_downloads_limits; ?></label>
						<div class="col-sm-9 control-inline">
						    <span><?php echo $ms_config_min; ?></span> <input class="form-control-mini" type="text" name="msconf_downloads_limits[]" value="<?php echo $msconf_downloads_limits[0]; ?>" size="3" />
                            <span><?php echo $ms_config_max; ?></span> <input class="form-control-mini" type="text" name="msconf_downloads_limits[]" value="<?php echo $msconf_downloads_limits[1]; ?>" size="3" />
							<div class="comment"><?php echo $ms_config_downloads_limits_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_miscellaneous; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_rte_whitelist; ?></label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="msconf_rte_whitelist" value="<?php echo $msconf_rte_whitelist; ?>" />
							<div class="comment"><?php echo $ms_config_rte_whitelist_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_reviews; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_reviews_enable; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_reviews_enable" value="1" <?php if($msconf_reviews_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_reviews_enable" value="0" <?php if($msconf_reviews_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_reviews_enable_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_product_questions; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_allow_question ;?></label>
						<div class="col-sm-9">
							<div class="selectcontainer">
								<label class="radio-inline"><input type="radio" name="msconf_allow_questions" value="1" <?php if($msconf_allow_questions == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="msconf_allow_questions" value="0" <?php if($msconf_allow_questions == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							</div>
						</div>
					</div>

					<!-- deprecated -->
					<div style="display:none" class="form-group">
						<label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $ms_config_enable_rte_note; ?>"><?php echo $ms_config_enable_rte; ?></span></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_enable_rte" value="1" <?php if($msconf_enable_rte == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?>
							</label>
							<label class="radio-inline"><input type="radio" name="msconf_enable_rte" value="0" <?php if($msconf_enable_rte == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?>
							</label>
						</div>
					</div>
				</div>
				<!-- END PRODUCT FORM TAB -->

			 	<!-- BEGIN FINANCES TAB -->
			 	<div id="tab-finances" class="tab-pane">
					<h4><?php echo $ms_config_general; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_fee_priority; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_fee_priority" value="1" <?php if($msconf_fee_priority == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_fee_priority_catalog; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_fee_priority" value="2" <?php if($msconf_fee_priority == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_fee_priority_vendor; ?></label>
							<div class="comment"><?php echo $ms_config_fee_priority_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_order_statuses; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_credit_order_statuses; ?></label>
						<div class="col-sm-9">
							<div class="well well-sm" style="height: 150px; overflow: auto;">
								<?php foreach ($order_statuses as $status) { ?>
									<input type="checkbox" name="msconf_credit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" <?php if (in_array($status['order_status_id'], $msconf_credit_order_statuses)) { ?>checked="checked"<?php } ?> /> <?php echo $status['name']; ?><br>
								<?php } ?>
							</div>
							<div class="comment"><?php echo $ms_config_credit_order_statuses_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_debit_order_statuses; ?></label>
						<div class="col-sm-9">
							<div class="well well-sm" style="height: 150px; overflow: auto;">
								<?php foreach ($order_statuses as $status) { ?>
									<input type="checkbox" name="msconf_debit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" <?php if (in_array($status['order_status_id'], $msconf_debit_order_statuses)) { ?>checked="checked"<?php } ?> /> <?php echo $status['name']; ?><br>
								<?php } ?>
							</div>
							<div class="comment"><?php echo $ms_config_debit_order_statuses_note; ?></div>
						</div>
					</div>
				</div>

				<!-- BEGIN MISCELLANEOUS TAB -->
			 	<div id="tab-miscellaneous" class="tab-pane">
					<h4><?php echo $mmes_messaging; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $mmess_config_enable; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="mmess_conf_enable" value="1" <?php if($mmess_conf_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="mmess_conf_enable" value="0" <?php if($mmess_conf_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_msg_allowed_file_types; ?></label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="msconf_msg_allowed_file_types" value="<?php echo $msconf_msg_allowed_file_types; ?>" />
							<div class="comment"><?php echo $ms_config_msg_allowed_file_types_note; ?></div>
						</div>
					</div>

					<h4><?php echo $mxt_disqus_comments; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $mxt_disqus_comments_enable; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="mxtconf_disqus_enable" value="1" <?php if($mxtconf_disqus_enable == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="mxtconf_disqus_enable" value="0" <?php if($mxtconf_disqus_enable == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
						</div>
					</div>

					<div class="form-group required">
						<label class="col-sm-3 control-label"><?php echo $mxt_disqus_comments_shortname; ?></label>
						<div class="col-sm-9 control-inline">
							<input class="form-control" type="text" name="mxtconf_disqus_shortname" value="<?php echo $mxtconf_disqus_shortname; ?>" size="10" />
						</div>
					</div>

                    <h4><?php echo $ms_config_image_sizes; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_avatar_image_size; ?></label>
						<div class="col-sm-9 control-inline">
                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_seller_avatar_image_size_seller_profile; ?></span>
                            <span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_seller_avatar_seller_profile_image_width" value="<?php echo $msconf_seller_avatar_seller_profile_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_seller_avatar_seller_profile_image_height" value="<?php echo $msconf_seller_avatar_seller_profile_image_height; ?>" size="3" /></span>
                            </div>

                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_seller_avatar_image_size_seller_list; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_seller_avatar_seller_list_image_width" value="<?php echo $msconf_seller_avatar_seller_list_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_seller_avatar_seller_list_image_height" value="<?php echo $msconf_seller_avatar_seller_list_image_height; ?>" size="3" />							</span>
                            </div>

                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_seller_avatar_image_size_product_page; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_seller_avatar_product_page_image_width" value="<?php echo $msconf_seller_avatar_product_page_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_seller_avatar_product_page_image_height" value="<?php echo $msconf_seller_avatar_product_page_image_height; ?>" size="3" /></span>
                            </div>

                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_seller_avatar_image_size_seller_dashboard; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_seller_avatar_dashboard_image_width" value="<?php echo $msconf_seller_avatar_dashboard_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_seller_avatar_dashboard_image_height" value="<?php echo $msconf_seller_avatar_dashboard_image_height; ?>" size="3" /></span>
                            </div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_seller_banner_size; ?></label>
						<div class="col-sm-9 control-inline">
                            <div class="row">
							<span class="col-sm-3"></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_product_seller_banner_width" value="<?php echo $msconf_product_seller_banner_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_product_seller_banner_height" value="<?php echo $msconf_product_seller_banner_height; ?>" size="3" /></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_image_preview_size; ?></label>
						<div class="col-sm-9 control-inline">
                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_image_preview_size_seller_avatar; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_preview_seller_avatar_image_width" value="<?php echo $msconf_preview_seller_avatar_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_preview_seller_avatar_image_height" value="<?php echo $msconf_preview_seller_avatar_image_height; ?>" size="3" /></span>
							</div>

                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_image_preview_size_product_image; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_preview_product_image_width" value="<?php echo $msconf_preview_product_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_preview_product_image_height" value="<?php echo $msconf_preview_product_image_height; ?>" size="3" /></span>
                            </div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_product_image_size; ?></label>
						<div class="col-sm-9 control-inline">
                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_product_image_size_seller_profile; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_product_seller_profile_image_width" value="<?php echo $msconf_product_seller_profile_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_product_seller_profile_image_height" value="<?php echo $msconf_product_seller_profile_image_height; ?>" size="3" /></span>
							</div>

                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_product_image_size_seller_products_list; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_product_seller_products_image_width" value="<?php echo $msconf_product_seller_products_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_product_seller_products_image_height" value="<?php echo $msconf_product_seller_products_image_height; ?>" size="3" /></span>
							</div>

                            <div class="row">
							<span class="col-sm-3"><?php echo $ms_config_product_image_size_seller_products_list_account; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_product_seller_product_list_seller_area_image_width" value="<?php echo $msconf_product_seller_product_list_seller_area_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_product_seller_product_list_seller_area_image_height" value="<?php echo $msconf_product_seller_product_list_seller_area_image_height; ?>" size="3" /></span>
                            </div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_uploaded_image_size; ?></label>
						<div class="col-sm-9 control-inline">
                            <div class="row">
                            <span class="col-sm-3"><?php echo $ms_config_min; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_min_uploaded_image_width" value="<?php echo $msconf_min_uploaded_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_min_uploaded_image_height" value="<?php echo $msconf_min_uploaded_image_height; ?>" size="3" /></span>
                            </div>

                            <div class="row">
                            <span class="col-sm-3"><?php echo $ms_config_max; ?></span>
							<span class="col-sm-6"><input class="form-control-mini" type="text" name="msconf_max_uploaded_image_width" value="<?php echo $msconf_max_uploaded_image_width; ?>" size="3" /> x <input class="form-control-mini" type="text" name="msconf_max_uploaded_image_height" value="<?php echo $msconf_max_uploaded_image_height; ?>" size="3" /></span>
                            </div>
							<div class="comment"><?php echo $ms_config_uploaded_image_size_note; ?></div>
						</div>
					</div>

                    <h4><?php echo $ms_config_seo; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_enable_seo_urls_seller; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_enable_seo_urls_seller" value="1" <?php if($msconf_enable_seo_urls_seller == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_enable_seo_urls_seller" value="0" <?php if($msconf_enable_seo_urls_seller == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_enable_seo_urls_seller_note; ?></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_enable_seo_urls_product; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_enable_seo_urls_product" value="1" <?php if($msconf_enable_seo_urls_product == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_enable_seo_urls_product" value="0" <?php if($msconf_enable_seo_urls_product == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_enable_seo_urls_product_note; ?></div>
					  	</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_enable_non_alphanumeric_seo; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_enable_non_alphanumeric_seo" value="1" <?php if($msconf_enable_non_alphanumeric_seo == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_enable_non_alphanumeric_seo" value="0" <?php if($msconf_enable_non_alphanumeric_seo == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							<div class="comment"><?php echo $ms_config_enable_non_alphanumeric_seo_note; ?></div>
					  	</div>
					</div>

					<!-- hidden -->
					<div style="display: none" class="form-group">
						<label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $ms_config_sellers_slug_note; ?>"><?php echo $ms_config_sellers_slug; ?></span></label>
						<div class="col-sm-9">
							<input class="form-control" type="text" name="msconf_sellers_slug" value="<?php echo isset($msconf_sellers_slug) ? $msconf_sellers_slug : 'sellers' ; ?>" />
						</div>
					</div>
                    </fieldset>
				</div>
				<!-- END MISCELLANEOUS TAB -->

				<!-- BEGIN SHIPPING TAB -->
				<div id="tab-shipping" class="tab-pane">
					<h4><?php echo $ms_config_general; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_shipping_type; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_shipping_type" value="1" <?php if($msconf_shipping_type == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_enable_store_shipping; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_shipping_type" value="2" <?php if($msconf_shipping_type == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_enable_vendor_shipping; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_shipping_type" value="0" <?php if($msconf_shipping_type == 0) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_disable_shipping; ?></label>
							<div class="comment"><?php echo $ms_config_shipping_type_note; ?></div>
						</div>
					</div>

					<div class="form-group vendor_shipping_type <?php echo (isset($msconf_vendor_shipping_type) && $msconf_shipping_type == 2) ? '' : 'hidden'; ?>">
						<label class="col-sm-3 control-label"><?php echo $ms_config_vendor_shipping_type; ?></label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="msconf_vendor_shipping_type" value="1" <?php if($msconf_vendor_shipping_type == 1) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_vendor_shipping_combined; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_vendor_shipping_type" value="2" <?php if($msconf_vendor_shipping_type == 2) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_vendor_shipping_per_product; ?></label>
							<label class="radio-inline"><input type="radio" name="msconf_vendor_shipping_type" value="3" <?php if($msconf_vendor_shipping_type == 3) { ?> checked="checked" <?php } ?>  /><?php echo $ms_config_vendor_shipping_both; ?></label>
							<div class="comment"><?php echo $ms_config_vendor_shipping_type_note; ?></div>
						</div>
					</div>

					<h4><?php echo $ms_config_shipping_delivery; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_config_shipping_delivery_times; ?></label>
						<div class="col-sm-9">
							<div class="row">
								<div class="col-sm-12">
									<table class="table table-sm table-hover <?php echo empty($shipping_delivery_times) ? 'hidden' : '' ;?>" id="delivery-times">
										<thead>
										<tr>
											<?php foreach ($languages as $language) { ?>
											<td class="text-center col-sm-4"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang" data-lang="<?php echo $language['code']; ?>"></td>
											<?php } ?>
											<td class="col-sm-1"></td>
										</tr>
										</thead>

										<tbody>
										<?php foreach ($shipping_delivery_times as $delivery_time_id => $delivery_time_desc) { ?>
										<tr>
											<input type="hidden" class="delivery_time_id" value="<?php echo $delivery_time_id; ?>" />
											<?php foreach ($languages as $language) { ?>
											<td class="text-center editable-time" data-lang-id="<?php echo $language['language_id'] ;?>">
												<?php echo isset($delivery_time_desc[$language['language_id']]) ? $delivery_time_desc[$language['language_id']] : '' ;?>
											</td>
											<?php } ?>

											<td class="text-center"><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
										</tr>
										<?php } ?>
										</tbody>

										<div class="comment <?php echo empty($shipping_delivery_times) ? 'hidden' : '' ;?>"><?php echo $ms_config_shipping_delivery_time_comment; ?></div>
									</table>
								</div>
							</div>

							<div>
								<a class="btn btn-default addDeliveryTime"><?php echo $ms_config_shipping_delivery_time_add_btn; ?></a>
							</div>

							<div class="row addDeliveryTimeForm hidden">
								<div class="col-sm-12">
									<?php foreach($languages as $language) { ?>
										<div style="display: block; margin-bottom: 5px;">
											<input type="text" class="form-control" name="delivery_time_<?php echo $language['language_id'] ;?>" value="" placeholder=""/>
											<img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang" data-lang="<?php echo $language['code']; ?>">
										</div>
									<?php } ?>
									<input type="button" id="add-delivery-time" class="btn btn-primary pull-left" style="margin: 10px 0 5px 0;" value="<?php echo $button_save; ?>"/>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- END SHIPPING TAB -->

				<!-- BEGIN PAYMENT TAB -->
				<div id="tab-payment-gateways" class="tab-pane">
					<h4><?php echo $ms_config_general; ?></h4>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $ms_pg_manage; ?></label>
						<div class="col-sm-9 control-inline">
							<a target="_blank" href="<?php echo $this->url->link('multimerch/payment-gateway', 'token=' . $this->session->data['token'], 'SSL'); ?>"><button type="button" class="btn btn-primary pull-left"><i class="fa fa-gears"></i> <?php echo $ms_pg_manage; ?></button></a>
						</div>
					</div>
				</div>
				<!-- END PAYMENT TAB -->
			</div>
			</form>
	  </div>
  </div>
</div>
<script>

$(function() {
	$("#saveSettings").click(function() {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=module/multimerch/savesettings&token=<?php echo $token; ?>',
			data: $('#settings').serialize(),
			success: function(jsonData) {
				if (jsonData.errors) {
					$("#error").html('');
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						$("#error").append('<p>'+jsonData.errors[error]+'</p>');
					}				
				} else {
					window.location.reload();
				}
		   	}
		});
	});

	$(document).on('click', '#add-delivery-time', function() {
		var delivery_times = [];
		$(document).find('input[name^=delivery_time]').map(function(index, item) {
			var language_id = parseInt($(item).attr('name').split('_').slice(-1).pop());
			delivery_times[language_id] = $(item).val();
		});

		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multimerch/shipping-method/jxSaveDeliveryTime&token=<?php echo $token; ?>',
			data: {names: delivery_times},
			success: function(jsonData) {
				var html = '';
				html += '<tr>';
				html += '<input type="hidden" class="delivery_time_id" value="' + jsonData['delivery_time_id'] + '" />';
				$(jsonData['delivery_time_names']).map(function(index, item) {
					if(item) {
						html += '<td class="text-center editable-time" data-lang-id="' + index + '">' + item + '</td>';
					}
				});
				html += '<td class="text-center"><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>';
				html += '</tr>';

				$('#delivery-times > tbody').append(html);
				$('#delivery-times').removeClass('hidden');
				$('#delivery-times').siblings('.comment').removeClass('hidden');

				$(document).find('input[name^=delivery_time]').map(function() {
					$(this).val("");
				});
			}
		});
	});

	$('#delivery-times').delegate('.mm_remove', 'click', function() {
		$(this).closest('tr').remove();

		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multimerch/shipping-method/jxDeleteDeliveryTime&token=<?php echo $token; ?>',
			data: {id : $(this).closest('tr').find('.delivery_time_id').val()},
			success: function(jsonData) {
				// console.log(jsonData);
				if ($('#delivery-times tbody tr').length < 1) {
					$('#delivery-times').addClass('hidden');
					$('#delivery-times').siblings('.comment').addClass('hidden');
				}
			}
		});
	});

	// edit delivery times
	var original_value;

	$(document).on('dblclick', '.editable-time', function () {
		original_value = $(this).text();
		$(this).text("");
		$('<input type="text" class="form-control" value="' + $.trim(original_value) + '" style="width: 75%; float: left;"/>').appendTo(this).select();
		$('<button class="btn btn-primary pull-left"><i class="fa fa-check" aria-hidden="true"></i></button>').appendTo(this);
	});

	$(document).on('click', '.editable-time > button', function (e) {
		e.preventDefault();
		var data = {
			name: $(this).siblings('input[type="text"]').val(),
			delivery_time_id: $(this).closest('tr').find('.delivery_time_id').val(),
			language_id: $(this).closest('td').data('lang-id')
		};

		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=multimerch/shipping-method/jxEditDeliveryTime&token=<?php echo $token; ?>',
			data: data,
			success: function(jsonData) {
				//console.log(jsonData);
			}
		});

		$(this).parent().text($(this).siblings('input[type="text"]').val() || original_value);
		$(this).remove();
	});

	$(document).on('click', '.addDeliveryTime', function() {
		$('.addDeliveryTimeForm').removeClass('hidden');
		$(this).addClass('hidden');
	});

	$(document).on('change', 'input[name="msconf_shipping_type"]', function() {
		if($(this).val() == 2) {
			$('.vendor_shipping_type').removeClass('hidden');
		} else {
			$('.vendor_shipping_type').addClass('hidden');
		}
	});

    $(document).on('change', 'input[name="msconf_allow_seller_categories"]', function() {
        if($(this).val() == 1) {
            $('.product_categories_type').removeClass('hidden');
        } else {
            $('.product_categories_type').addClass('hidden');
        }
    });
});
</script>  

<?php echo $footer; ?>	
</div>