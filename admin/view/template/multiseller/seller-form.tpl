<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
	<div class="container-fluid">
	  <div class="pull-right">
		<button id="ms-submit-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
		<a href="<?php echo $this->url->link('multimerch/seller', 'token=' . $token); ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
	  <h1><?php echo $ms_catalog_sellers_heading; ?></h1>
	  <ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	  </ul>
	</div>
  </div>
  <div class="container-fluid">
	<div style="display: none" class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>
	  <div class="panel panel-default">
	  <div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo isset($seller['seller_id']) ? $ms_catalog_sellerinfo_heading : $ms_catalog_sellers_newseller; ?></h3>
	  </div>
	  <div class="panel-body">
		<form id="ms-sellerinfo" class="form-horizontal">
			<input type="hidden" id="seller_id" name="seller[seller_id]" value="<?php echo $seller['seller_id']; ?>" />
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
				<li><a href="#tab-commission" data-toggle="tab"><?php echo $ms_commissions_fees; ?></a></li>
				<?php if (isset($is_edit) AND $is_edit) { ?>
				<li><a href="#tab-product" data-toggle="tab"><?php echo $ms_menu_products; ?></a></li>
				<li><a href="#tab-transaction" data-toggle="tab"><?php echo $ms_menu_transactions; ?></a></li>
				<li><a href="#tab-payment-request" data-toggle="tab"><?php echo $ms_menu_payment_request; ?></a></li>
				<li><a href="#tab-payment" data-toggle="tab"><?php echo $ms_menu_payment; ?></a></li>
				<?php } ?>
				<li><a href="#tab-badge" data-toggle="tab"><?php echo $ms_catalog_badges_breadcrumbs; ?></a></li>
				<li><a href="#tab-user-settings" data-toggle="tab"><?php echo $ms_user_settings; ?></a></li>
				<?php if(isset($payment_gateways) && is_array($payment_gateways) && !empty($payment_gateways)) { ?>
				<li><a href="#tab-payments" data-toggle="tab"><?php echo $ms_menu_payment_gateway_settings; ?></a></li>
				<?php } ?>
			</ul>
			<div class="tab-content">
			<div class="tab-pane active" id="tab-general">

			<fieldset>
			<legend><?php echo $ms_catalog_sellerinfo_customer_data; ?></legend>
			 <div class="form-group required">
				<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_customer; ?></label>

				<div class="col-sm-10">
				<?php if (!$seller['seller_id']) { ?>
				<select class="form-control" name="customer[customer_id]">
					<optgroup label="<?php echo $ms_catalog_sellerinfo_customer_new; ?>">
					<option value="0"><?php echo $ms_catalog_sellerinfo_customer_create_new; ?></option>
					</optgroup>
					<?php if (isset($customers)) { ?>
					<optgroup label="<?php echo $ms_catalog_sellerinfo_customer_existing; ?>">
					<?php foreach ($customers as $c) { ?>
					<option value="<?php echo $c['c.customer_id']; ?>"><?php echo $c['c.name']; ?></option>
					<?php } ?>
					</optgroup>
					<?php } ?>
				</select>
				<?php } else { ?>
					<a href="<?php echo $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $seller['seller_id'], 'SSL'); ?>"><?php echo $seller['name']; ?></a>
				<?php } ?>
				</div>
			</div>

			<div class="form-group required">
				<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_customer_firstname; ?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="customer[firstname]" value="" />
				</div>
			</div>

			<div class="form-group required">
				<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_customer_lastname; ?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="customer[lastname]" value="" />
				</div>
			</div>

			<div class="form-group required">
				<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_customer_email; ?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="customer[email]" value="" />
				</div>
			</div>

			<div class="form-group required">
				<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_customer_password; ?></label>
				<div class="col-sm-10">
					<input type="password" class="form-control" name="customer[password]" value="" />
				</div>
			</div>

			<div class="form-group required">
				<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_customer_password_confirm; ?></label>
				<div class="col-sm-10">
					<input type="password" class="form-control" name="customer[password_confirm]" value="" />
				</div>
			</div>
			</fieldset>

			<fieldset>
			<legend><?php echo $ms_catalog_sellerinfo_seller_data; ?></legend>
			<div class="form-group required">
				<label class="col-sm-2 control-label required"><?php echo $ms_catalog_sellerinfo_nickname; ?></label>
				<?php if (!empty($seller['ms.nickname'])) { ?>
					<div class="col-sm-10" style="padding-top: 5px">
						<b><?php echo $seller['ms.nickname']; ?></b>
					</div>
				<?php } else { ?>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="seller[nickname]" value="<?php echo $seller['ms.nickname']; ?>" />
					</div>
				<?php } ?>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_keyword; ?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="seller[keyword]" value="<?php echo $seller['keyword']; ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_sellergroup; ?></label>
				<div class="col-sm-10"><select class="form-control" name="seller[seller_group]">
					<?php foreach ($seller_groups as $group) { ?>
					<option value="<?php echo $group['seller_group_id']; ?>" <?php if ($seller['ms.seller_group'] == $group['seller_group_id']) { ?>selected="selected"<?php } ?>><?php echo $group['name']; ?></option>
					<?php } ?>
				</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label">
					<span data-toggle="tooltip" title="<?php echo $ms_catalog_sellerinfo_product_validation_note; ?>"><?php echo $ms_catalog_sellerinfo_product_validation; ?></span>
				</label>
				<div class="col-sm-10">
						<select class="form-control" name="seller[product_validation]">
							<option value="1" <?php if($seller['ms.product_validation'] == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_none; ?></option>
						<option value="2" <?php if($seller['ms.product_validation'] == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_approval; ?></option>
					</select>
				</div>
			</div>

				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_description; ?></label>
					<div class="col-sm-10">
						<ul class="nav nav-tabs" id="language">
							<?php foreach ($languages as $language) { ?>
							<li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab">
									<img class="lang_image" src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?>
								</a>
							</li>
							<?php } ?>
						</ul>
						<div class="tab-content">
							<?php foreach ($languages as $language) { ?>
							<div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
								<textarea name="seller[description][<?php echo $language['language_id']; ?>][description]" id="seller_textarea<?php echo $language['language_id']; ?>" class="form-control summernote"> <?php echo $this->config->get('msconf_enable_rte') ? htmlspecialchars_decode($seller['descriptions'][$language['language_id']]['description']) : strip_tags(htmlspecialchars_decode($seller['descriptions'][$language['language_id']]['description'])); ?> </textarea>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>


				<!--
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $ms_catalog_sellerinfo_avatar; ?></label>
					<div class="col-sm-10">
						<div id="sellerinfo_avatar_files">
							<?php if (!empty($seller['avatar'])) { ?>
							<input type="hidden" name="seller[avatar_name]" value="<?php echo $seller['avatar']['name']; ?>" />
							<img src="<?php echo $seller['avatar']['thumb']; ?>" />
							<?php } ?>
						</div>
					</div>
				</div>
				-->

			<?php $msSeller = new ReflectionClass('MsSeller'); ?>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $ms_status; ?></label>
				<div class="col-sm-10">
					<select class="form-control" name="seller[status]">
					<?php foreach ($msSeller->getConstants() as $cname => $cval) { ?>
						<?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
							<option value="<?php echo $cval; ?>" <?php if ($seller['ms.seller_status'] == $cval) { ?>selected="selected"<?php } ?>><?php echo $this->language->get('ms_seller_status_' . $cval); ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label">
					<span data-toggle="tooltip" title="<?php echo $ms_catalog_sellerinfo_notify_note; ?>"><?php echo $ms_catalog_sellerinfo_notify; ?></span>
				</label>
				<div class="col-sm-10">
					<input type="checkbox" style="margin-top: 10px" name="seller[notify]" value="1" checked="checked" /><br>
					<textarea class="form-control" name="seller[message]" placeholder="<?php echo $ms_catalog_sellerinfo_message_note; ?>"></textarea>
				</div>
			</div>
			</fieldset>

			</div>

			<div class="tab-pane" id="tab-commission">
				<table class="form">
					<input type="hidden" name="seller[commission_id]" value="<?php echo $seller['commission_id']; ?>" />
					<?php if (isset($seller['actual_fees'])) { ?>
						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_commission_actual; ?></label>
							<div class="col-sm-10"><?php echo $seller['actual_fees']; ?></div>
						</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $this->language->get('ms_commission_' . MsCommission::RATE_SALE); ?></label>
						<div class="col-sm-10 control-inline">
							<input type="hidden" name="seller[commission][<?php echo MsCommission::RATE_SALE; ?>][rate_id]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_SALE]['rate_id']) ? $seller['commission_rates'][MsCommission::RATE_SALE]['rate_id'] : ''; ?>" />
							<input type="hidden" name="seller[commission][<?php echo MsCommission::RATE_SALE; ?>][rate_type]" value="<?php echo MsCommission::RATE_SALE; ?>" />
							<?php echo $this->currency->getSymbolLeft(); ?>
							<input type="text" class="form-control" name="seller[commission][<?php echo MsCommission::RATE_SALE; ?>][flat]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_SALE]['flat']) ? $this->currency->format($seller['commission_rates'][MsCommission::RATE_SALE]['flat'], $this->config->get('config_currency'), '', FALSE) : '' ?>" size="3"/>
							<?php echo $this->currency->getSymbolRight(); ?>
							+<input type="text" class="form-control" name="seller[commission][<?php echo MsCommission::RATE_SALE; ?>][percent]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_SALE]['percent']) ? $seller['commission_rates'][MsCommission::RATE_SALE]['percent'] : ''; ?>" size="3"/>%
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $this->language->get('ms_commission_' . MsCommission::RATE_LISTING); ?></label>
						<div class="col-sm-10 control-inline">
							<input type="hidden" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][rate_id]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_LISTING]['rate_id']) ? $seller['commission_rates'][MsCommission::RATE_LISTING]['rate_id'] : ''; ?>" />
							<input type="hidden" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][rate_type]" value="<?php echo MsCommission::RATE_LISTING; ?>" />
							<?php echo $this->currency->getSymbolLeft(); ?>
							<input type="text" class="form-control" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][flat]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_LISTING]['flat']) ? $this->currency->format($seller['commission_rates'][MsCommission::RATE_LISTING]['flat'], $this->config->get('config_currency'), '', FALSE) : '' ?>" size="3"/>
							<?php echo $this->currency->getSymbolRight(); ?>
							+<input type="text" class="form-control" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][percent]" value="<?php echo isset($seller['commission_rates'][MsCommission::RATE_LISTING]['percent']) ? $seller['commission_rates'][MsCommission::RATE_LISTING]['percent'] : ''; ?>" size="3"/>%
							<select class="form-control" name="seller[commission][<?php echo MsCommission::RATE_LISTING; ?>][payment_method]">
								<optgroup label="<?php echo $ms_payment_method; ?>">
									<option value="<?php echo MsPgPayment::METHOD_BALANCE; ?>" <?php if(isset($seller['commission_rates'][MsCommission::RATE_LISTING]) && $seller['commission_rates'][MsCommission::RATE_LISTING]['payment_method'] == MsPgPayment::METHOD_BALANCE) { ?> selected="selected" <?php } ?>><?php echo $ms_payment_method_balance; ?></option>
									<option value="<?php echo MsPgPayment::METHOD_PG; ?>" <?php if(isset($seller['commission_rates'][MsCommission::RATE_LISTING]) && $seller['commission_rates'][MsCommission::RATE_LISTING]['payment_method'] == MsPgPayment::METHOD_PG) { ?> selected="selected" <?php } ?>><?php echo $ms_pg_fee_payment_method_name; ?></option>
								</optgroup>
							</select>
						</div>
					</div>
				</table>
			</div>
			<!--  end commission tab -->
			<?php if (isset($is_edit) AND $is_edit) { ?>
			<!-- begin product tab -->
			<div class="tab-pane" id="tab-product">
				<div class="pull-right clearfix">
					<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" id="delete-seller-product" style="margin-bottom: 20px;"><i class="fa fa-trash-o"></i></button>
				</div>
				<div class="table-responsive">
						<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-products">
							<thead>
							<tr>
								<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected_products\']').attr('checked', this.checked);" /></td>
								<td><?php echo $ms_product; ?></td>
								<td class="small"><?php echo $ms_price ;?></td>
								<td class="small"><?php echo $ms_quantity ;?></td>
								<td class="small"><?php echo $ms_sales ;?></td>
								<td class="medium" id="status_column"><?php echo $ms_status; ?></td>
								<td class="medium"><?php echo $ms_date_created; ?></td>
								<td class="medium"><?php echo $ms_date_modified; ?></td>
								<td class="large"><?php echo $ms_action; ?></td>
							</tr>
							<tr class="filter">
								<td></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td>
									<select id="status_select">
										<option></option>
										<?php $msProduct = new ReflectionClass('MsProduct'); ?>
										<?php foreach ($msProduct->getConstants() as $cname => $cval) { ?>
										<?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
										<option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_product_status_' . $cval); ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</td>
								<td><input type="text" class="input-date-datepicker"/></td>
								<td><input type="text" class="input-date-datepicker"/></td>
								<td></td>
							</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
				</div>
			</div>
			<!--  end product tab -->
			<!-- begin transaction tab -->
			<div class="tab-pane" id="tab-transaction">
				<div class="table-responsive">
					<table class="list table table-bordered table-hover" style="text-align: center" id="list-transactions">
						<thead>
						<tr>
							<td class="tiny"><?php echo $ms_id; ?></td>
							<td class="small"><?php echo $ms_net_amount; ?></a></td>
							<td><?php echo $ms_description; ?></a></td>
							<td class="medium"><?php echo $ms_date; ?></a></td>
						</tr>
						<tr class="filter">
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text" class="input-date-datepicker"/></td>
						</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<!--  end transaction tab -->
			<!-- begin payment-request tab -->
			<div class="tab-pane" id="tab-payment-request">
				<p><?php echo $ms_payment_payout_requests; ?>: <b><?php echo $payout_requests['amount_pending'];?></b> <?php echo strtolower($ms_payment_pending); ?> / <b><?php echo $payout_requests['amount_paid'];?></b> <?php echo strtolower($ms_payment_paid); ?></p>
				<p><?php echo $ms_payment_payouts; ?>: <b><?php echo $payouts['amount_pending'];?></b> <?php echo strtolower($ms_payment_pending); ?> / <b><?php echo $payouts['amount_paid'];?></b> <?php echo strtolower($ms_payment_paid); ?></p>

				<div class="table-responsive">
					<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-payment-requests">
						<thead>
						<tr>
							<td class="tiny"><input type="checkbox" onclick="$('#tab-payment-request input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="small"><?php echo $ms_id; ?></td>
							<td class="medium"><?php echo $ms_type; ?></td>
							<td class="small"><?php echo $ms_amount; ?></td>
							<td><?php echo $ms_description; ?></td>
							<td class="medium"><?php echo $ms_date_created; ?></td>
							<td class="medium"><?php echo $ms_status; ?></td>
							<td class="medium"><?php echo $ms_pg_payment_number; ?></td>
							<td class="medium"><?php echo $ms_date_paid; ?></td>
						</tr>
						<tr class="filter">
							<td></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td></td>
							<td><input type="text" class="input-date-datepicker"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text" class="input-date-datepicker"/></td>
						</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<!--  end payment-request tab -->
			<!-- begin payment tab -->
			<div class="tab-pane" id="tab-payment">
				<div class="table-responsive">
					<table class="table table-bordered table-hover" style="text-align: center" id="list-payments">
						<thead>
						<tr>
							<td class="tiny"><?php echo $ms_id; ?></td>
							<td class="medium"><?php echo $ms_type; ?></td>
							<td class="medium"><?php echo $ms_method; ?></td>
							<td><?php echo $ms_description; ?></td>
							<td class="small"><?php echo $ms_amount; ?></td>
							<td class="small"><?php echo $ms_status; ?></td>
							<td class="medium"><?php echo $ms_date_created; ?></td>
						</tr>
						<tr class="filter">
							<td></td>
							<td></td>
							<td><input type="text"/></td>
							<td></td>
							<td><input type="text"/></td>
							<td></td>
							<td><input type="text" class="input-date-datepicker"/></td>
						</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<!--  end payment tab -->
			<?php } ?>
			<!-- begin badge tab -->
			<div class="tab-pane" id="tab-badge">
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $ms_catalog_badges_heading; ?></label>
					<div class="col-sm-10">
						<div class="well well-sm">
							<?php foreach ($badges as $badge) { ?>
							<div>
								<input type="checkbox" name="seller[badges][]" value="<?php echo $badge['badge_id']; ?>" <?php if (isset($seller['badges']) && in_array($badge['badge_id'], $seller['badges'])) { ?>checked="checked"<?php } ?> />
								<?php echo $badge['name']; ?> <img src="<?php echo $badge['image']; ?>"/>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<!-- end badge tab -->

			<!-- begin settings tab -->
			<div class="tab-pane" id="tab-user-settings">
				<fieldset>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_full_name; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_full_name]" value="<?php echo (isset($settings['slr_full_name'])) ? $settings['slr_full_name'] : '' ; ?>" placeholder="<?php echo $ms_seller_full_name; ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_address1; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_address_line1]" value="<?php echo (isset($settings['slr_address_line1'])) ? $settings['slr_address_line1'] : '' ; ?>" placeholder="<?php echo $ms_seller_address1_placeholder ;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_address2; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_address_line2]" value="<?php echo (isset($settings['slr_address_line2'])) ? $settings['slr_address_line2'] : '' ; ?>" placeholder="<?php echo $ms_seller_address2_placeholder ;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_city; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_city]" value="<?php echo (isset($settings['slr_city'])) ? $settings['slr_city'] : '' ; ?>" placeholder="<?php echo $ms_seller_city; ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_state; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_state]" value="<?php echo (isset($settings['slr_state'])) ? $settings['slr_state'] : '' ; ?>" placeholder="<?php echo $ms_seller_state ;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_zip; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_zip]" value="<?php echo (isset($settings['slr_zip'])) ? $settings['slr_zip'] : '' ; ?>" placeholder="<?php echo $ms_seller_zip ;?>">
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_country; ?></label>
						<div class="col-sm-10">
							<select class="form-control" name="seller_setting[slr_country]">
								<?php foreach($countries as $country) :?>
								<?php if($settings['slr_country'] == $country['country_id']) :?>
								<option value="<?php echo $country['country_id'] ;?>" selected><?php echo $country['name'] ;?></option>
								<?php else :?>
								<option value="<?php echo $country['country_id'] ;?>"><?php echo $country['name'] ;?></option>
								<?php endif ;?>
								<?php endforeach ;?>
							</select>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Information</legend>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_website; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_website]" value="<?php echo (isset($settings['slr_website'])) ? $settings['slr_website'] : '' ; ?>" placeholder="<?php echo $ms_seller_website ;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_company; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_company]" value="<?php echo (isset($settings['slr_company'])) ? $settings['slr_company'] : '' ; ?>" placeholder="<?php echo $ms_seller_company ;?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_seller_phone; ?></label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="seller_setting[slr_phone]" value="<?php echo (isset($settings['slr_phone'])) ? $settings['slr_phone'] : '' ; ?>" placeholder="<?php echo $ms_seller_phone ;?>">
						</div>
					</div>
				</fieldset>
			</div>
			<!-- end settings tab -->

			<?php if(isset($payment_gateways) && is_array($payment_gateways) && !empty($payment_gateways)) { ?>
				<!-- begin payments tab -->
				<div class="tab-pane" id="tab-payments">
					<ul class="nav nav-tabs pg-topbar">
						<?php foreach($payment_gateways as $payment_gateway) { ?>
						<li <?php echo reset($payment_gateways) == $payment_gateway ? 'class="active"' : ''; ?>><a href="#tab-<?php echo $payment_gateway['code']; ?>" data-toggle="tab"><?php echo $payment_gateway['text_title']; ?></a></li>
						<?php } ?>
					</ul>
					<div class="tab-content ms-pg-content">
						<?php foreach($payment_gateways as $payment_gateway) { ?>
							<div id="tab-<?php echo $payment_gateway['code']; ?>" class="tab-pane <?php echo reset($payment_gateways) == $payment_gateway ? 'active' : ''; ?>">
								<input type="hidden" class="ms_pg_code" value="<?php echo $payment_gateway['code']; ?>">
								<?php echo $payment_gateway['view']; ?>
							</div>
						<?php } ?>
					</div>
				</div>
				<!-- end payments tab -->
			<?php } ?>

			</div>
		</div>
		</form>
	  </div>
	</div>
  </div>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
	<script type="text/javascript">
	$(function() {
		$('input[name^="customer"]').parents('div.form-group').hide();
		$('select[name="customer[customer_id]"]').bind('change', function() {
			if (this.value == '0') {
				$('input[name^="customer"]').parents('div.form-group').show();
				$('[name="seller[notify]"], [name="seller[message]"]').parents('div.form-group').hide();
			} else {
				$('input[name^="customer"]').parents('div.form-group').hide();
				$('[name="seller[notify]"], [name="seller[message]"]').parents('div.form-group').show();
			}
		}).change();
	
		$("#ms-submit-button").click(function() {
			var button = $(this);
			var id = $(this).attr('id');
			$.ajax({
				type: "POST",
				dataType: "json",
				url: 'index.php?route=multimerch/seller/jxsavesellerinfo&token=<?php echo $token; ?>',
				data: $('#ms-sellerinfo').serialize(),
				beforeSend: function() {
					$('div.text-danger').remove();
					$('.alert-danger').hide().find('i').text('');
				},
				complete: function(jqXHR, textStatus) {
					button.show().prev('span.wait').remove();
					$('.alert-danger').hide().find('i').text('');
				},
				error: function(jqXHR, textStatus, errorThrown) {
				   $('.alert-danger').show().find('i').text(textStatus);
				},
				success: function(jsonData) {
					if (!jQuery.isEmptyObject(jsonData.errors)) {
						for (error in jsonData.errors) {
							$('[name="'+error+'"]').after('<div class="text-danger">' + jsonData.errors[error] + '</div>');
						}
						window.scrollTo(0,0);
					} else {
						window.location = 'index.php?route=multimerch/seller&token=<?php echo $token; ?>';
					}
					}
			});
		});

		$("select[name='seller[country]']").bind('change', function() {
			$.ajax({
				url: 'index.php?route=customer/customer/country&token=<?php echo $token; ?>&country_id=' + this.value,
				dataType: 'json',
				beforeSend: function() {
					$("select[name='seller[country]']").after('<i class="fa fa-circle-o-notch fa-spin"></i>');
				},
				complete: function() {
					$('.fa-spin').remove();
				},
				success: function(json) {
					html = '<option value=""><?php echo $ms_catalog_sellerinfo_zone_select; ?></option>';

					if (json['zone']) {
						for (i = 0; i < json['zone'].length; i++) {
							html += '<option value="' + json['zone'][i]['zone_id'] + '"';
							
							if (json['zone'][i]['zone_id'] == '<?php echo $settings['slr_country']; ?>') {
								html += ' selected="selected"';
							}
			
							html += '>' + json['zone'][i]['name'] + '</option>';
						}
					} else {
						html += '<option value="0" selected="selected"><?php echo $ms_catalog_sellerinfo_zone_not_selected; ?></option>';
					}
					
					$("select[name='seller[zone]']").html(html);
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}).trigger('change');
	});
	</script>

<script type="text/javascript"><!--
		$('#language a:first').tab('show');
//--></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#list-products').dataTable( {
			"sAjaxSource": "index.php?route=multimerch/seller/getProductTableData&token=<?php echo $token; ?>&seller_id=<?php echo $seller['seller_id']; ?>",
			"aoColumns": [
				{ "mData": "checkbox", "bSortable": false },
				{ "mData": "name" },
				{ "mData": "price"},
				{ "mData": "quantity"},
				{ "mData": "sales"},
				{ "mData": "status" },
				{ "mData": "date_added" },
				{ "mData": "date_modified" },
				{ "mData": "actions", "bSortable": false}
			],
			"initComplete": function(settings, json) {
				var api = this.api();
				var statusColumn = api.column('#status_column');

				$('#status_select').change( function() {
					statusColumn.search( $(this).val() ).draw();
				});
				//$('#tab-product #list-products a.ms-button-delete').removeAttr('href');
			}
		});

		$('#list-transactions').dataTable( {
			"sAjaxSource": "index.php?route=multimerch/seller/getTransactionTableData&token=<?php echo $token; ?>&seller_id=<?php echo $seller['seller_id']; ?>",
			"aoColumns": [
				{ "mData": "id" },
				{ "mData": "amount" },
				{ "mData": "description" },
				{ "mData": "date_created" }
			],
			"aaSorting":  [[3,'desc']]
		});

		$('#list-payment-requests').dataTable( {
			"sAjaxSource": "index.php?route=multimerch/seller/getPaymentRequestTableData&token=<?php echo $token; ?>&seller_id=<?php echo $seller['seller_id']; ?>",
			"aoColumns": [
				{ "mData": "checkbox", "bSortable": false },
				{ "mData": "request_id" },
				{ "mData": "request_type" },
				{ "mData": "amount" },
				{ "mData": "description", "bSortable": false },
				{ "mData": "date_created" },
				{ "mData": "request_status" },
				{ "mData": "payment_id" },
				{ "mData": "date_modified" }
			]
		});

		$('#list-payments').dataTable( {
			"sAjaxSource": "index.php?route=multimerch/seller/getPaymentTableData&token=<?php echo $token; ?>&seller_id=<?php echo $seller['seller_id']; ?>",
			"aoColumns": [
				{ "mData": "payment_id" },
				{ "mData": "payment_type" },
				{ "mData": "payment_code" },
				{ "mData": "description" },
				{ "mData": "amount" },
				{ "mData": "payment_status" },
				{ "mData": "date_created" },
			],
			"aaSorting":  [[6,'desc']]
		});

		$(document).delegate('#tab-product #list-products span.ms-button-delete', 'click', function() {
			var product_id = $(this).data('product_id');
			if(confirm('Are you sure?')) {
				$.ajax({
					dataType: "json",
					data: 'product_id='+product_id,
					url: 'index.php?route=multimerch/seller/jxDeleteProduct&token=<?php echo $token; ?>',
					complete: function (jsonData) {
						$('#list-products').DataTable().ajax.reload();
					}
				});
			}
		});

		$(document).on('click', '#delete-seller-product', function(e) {
			e.preventDefault();
			var form = $('#ms-sellerinfo').serialize();
			if(form) {
				if(confirm('Are you sure?')) {
					$.ajax({
						url: 'index.php?route=multimerch/seller/jxDeleteProduct&token=<?php echo $token; ?>',
						data: form,
						type: 'post',
						dataType: 'json',
						complete: function(response) {
							console.log(response);
							$('#list-products').DataTable().ajax.reload();
						}
					});
				}
			}
		});

		setTimeout(function() {
			var dataTables = $('table.dataTable');
			$.map(dataTables, function(item) {
				if($(item).find('tbody tr:first td').length == 1) {
					$(item).find('tbody tr:first td').attr('colspan', '100%');
				}
			});
		}, 500);

		$(document).on('click', '.ms-confirm-bank-transfer', function(e) {
			e.preventDefault();
			var button = $(this);
			var payment_id = button.closest('tr').find('input[name="payment_id"]').val();

			if(payment_id.length) {
				$.ajax({
					url: 'index.php?route=multimerch/payment/bank_transfer/jxConfirmPayment&token=<?php echo $token; ?>',
					type: 'post',
					data: {payment_id: payment_id},
					dataType: 'json',
					beforeSend: function () {
						button.button('loading');
					},
					success: function (json) {
						if(json.success) {
							button.button('reset');
							button.parent('td').html(json.success);
						}
					}
				});
			}
		});

	});

</script>



<?php echo $footer; ?>