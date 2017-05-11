<?php echo $header; ?>
<div class="container">
	<?php if (isset($success) && ($success)) { ?>
		<div class="alert alert-success"><?php echo $success; ?></div>
  	<?php } ?>

	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?> ms-account-dashboard"><?php echo $content_top; ?>
			<div class="mm_dashboard ms-seller-settings">
				<h1><i class="fa fa-tachometer"></i><?php echo $ms_account_settings ;?></h1>

				<ul class="nav nav-tabs topbar">
					<li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $ms_account_general; ?></a></li>

					<?php if(!empty($payment_gateways)) { ?>
						<li><a href="#tab-payment-gateways" data-toggle="tab"><?php echo $ms_account_setting_payments_tab; ?></a></li>
					<?php } ?>

					<?php if($this->config->get('msconf_shipping_type') == 2 && ($this->config->get('msconf_vendor_shipping_type') == 1 || $this->config->get('msconf_vendor_shipping_type') == 3)) { ?>
						<li><a href="#tab-seller-shipping" data-toggle="tab"><?php echo $ms_account_product_tab_shipping; ?></a></li>
					<?php } ?>
				</ul>

				<div class="tab-content">
					<div id="tab-general" class="tab-pane active">
						<form id="ms-sellersettings" class="ms-form form-horizontal">
							<fieldset>
								<legend><?php echo $ms_seller_address; ?></legend>
								<input type="hidden" name="seller_id" value="<?php echo $seller_id ;?>">

								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_full_name; ?></label>

									<div class="mm_form col-sm-10">
										<input type="text" class="form-control" name="settings[slr_full_name]"
											   value="<?php echo $settings['slr_full_name']; ?>"
											   placeholder="<?php echo $ms_seller_full_name; ?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_address1; ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_address_line1]"
											   value="<?php echo $settings['slr_address_line1']; ?>"
											   placeholder="<?php echo $ms_seller_address1_placeholder ;?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_address2; ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_address_line2]"
											   value="<?php echo $settings['slr_address_line2']; ?>"
											   placeholder="<?php echo $ms_seller_address2_placeholder ;?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_city; ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_city]"
											   value="<?php echo $settings['slr_city']; ?>"
											   placeholder="<?php echo $ms_seller_city; ?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_state; ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_state]"
											   value="<?php echo $settings['slr_state']; ?>"
											   placeholder="<?php echo $ms_seller_state ;?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_zip; ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_zip]"
											   value="<?php echo $settings['slr_zip']; ?>"
											   placeholder="<?php echo $ms_seller_zip ;?>">
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_country; ?></label>

									<div class="col-sm-10">
										<select class="form-control" name="settings[slr_country]">
											<?php foreach($countries as $country) :?>
											<?php if($settings['slr_country'] == $country['country_id']) :?>
											<option value="<?php echo $country['country_id'] ;?>"
													selected><?php echo $country['name'];?></option>
											<?php else :?>
											<option value="<?php echo $country['country_id'] ;?>"><?php echo $country['name'] ;?></option>
											<?php endif ;?>
											<?php endforeach ;?>
										</select>
									</div>
								</div>
							</fieldset>

							<fieldset class="control-inline">
								<legend><?php echo $ms_seller_information; ?></legend>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_website; ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_website]"
											   value="<?php echo $settings['slr_website']; ?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_company; ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_company]"
											   value="<?php echo $settings['slr_company']; ?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_phone; ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_phone]"
											   value="<?php echo $settings['slr_phone']; ?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $ms_seller_logo; ?></label>

									<div class="col-sm-10">
										<div id="seller_settings_logo">
											<div class="ms-image <?php if (empty($settings['slr_logo'])) { ?>hidden<?php } ?>">
												<input type="hidden" name="settings[slr_logo]" value="<?php echo $settings['slr_logo']; ?>" />
												<img src="<?php echo $settings['slr_thumb']; ?>" />
												<span class="ms-remove"><i class="fa fa-times"></i></span>
											</div>

											<div class="dragndropmini <?php if ((!empty($settings['slr_logo']))) { ?>hidden<?php } ?>" id="ms-logo"><p class="mm_drophere"><?php echo $ms_drag_drop_click_here; ?></p></div>
											<p class="ms-note"><?php echo $ms_account_sellerinfo_logo_note; ?></p>
											<div class="alert alert-danger" style="display: none;"></div>
											<div class="ms-progress progress"></div>
										</div>
									</div>
								</div>
							</fieldset>

							<?php if ($this->config->get('mxtconf_ga_seller_enable')) { ?>
							<fieldset class="control-inline">
								<legend><?php echo $mxt_google_analytics ?></legend>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $mxt_google_analytics_code ?></label>

									<div class="col-sm-10">
										<input type="text" class="form-control" name="settings[slr_ga_tracking_id]" value="<?php echo $settings['slr_ga_tracking_id'] ?>" placeholder="UA-XXXXXXXX-X">
										<p class="ms-note"><?php echo $mxt_google_analytics_code_note; ?></p>
									</div>
								</div>
							</fieldset>
							<?php } ?>

							<div class="buttons">
								<div class="pull-right">
									<a class="btn btn-primary" id="ms-submit-button"><span><?php echo $ms_button_save; ?></span></a>
								</div>
							</div>
						</form>
					</div>

					<div id="tab-payment-gateways" class="tab-pane">
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

					<div id="tab-seller-shipping" class="tab-pane">
						<div class="alert alert-danger hidden" id="ssm-error-holder"></div>

						<?php if(isset($ssm_errors) && is_array($ssm_errors) && !empty($ssm_errors)) { ?>
							<div class="alert alert-danger">
								<ul>
									<?php foreach($ssm_errors as $ssm_error) { ?>
										<li><?php echo $ssm_error; ?></li>
									<?php } ?>
								</ul>
							</div>
						<?php } else { ?>
							<form id="ms-seller-shipping" class="ms-form form-horizontal">
								<fieldset class="control-inline">
									<legend><?php echo $ms_account_setting_ssm_title; ?></legend>
									<input type="hidden" name="seller_shipping[seller_shipping_id]" value="<?php echo isset($seller_shipping['seller_shipping_id']) ? $seller_shipping['seller_shipping_id'] : ''; ?>" />

									<div class="form-group">
										<label class="col-sm-3">
											<p class="ms-note"><?php echo $ms_account_product_shipping_from; ?></p>
											<input type="hidden" class="location-id" name="seller_shipping[from_country_id]" value="<?php echo isset($seller_shipping['from_country_id']) ? $seller_shipping['from_country_id'] : '0' ;?>" />
											<input type="text" id="mm_shipping_from_country" class="form-control ac-shipping-from-country" name="seller_shipping[from_country_name]" value="<?php echo isset($seller_shipping['from_country_name']) ? $seller_shipping['from_country_name'] : '' ;?>" />
										</label>
										<div class="col-sm-9"></div>
									</div>

									<div class="clearfix"></div>

									<div class="form-group">
										<label class="col-sm-3">
											<p class="ms-note"><?php echo $ms_account_product_shipping_processing_time; ?></p>
											<div class = "input-group">
												<span class="input-group-addon">Up to</span>
												<input type="text" class="form-control text-center" name="seller_shipping[processing_time]" value="<?php echo isset($seller_shipping['processing_time']) ? $seller_shipping['processing_time'] : 0 ;?>"/>
												<span class="input-group-addon">days</span>
											</div>
										</label>
										<div class="col-sm-9"></div>
									</div>

									<div class="clearfix"></div>

									<div class="panel panel-default table-responsive">
										<div class="panel-heading"><?php echo $ms_account_product_shipping_locations_to; ?></div>
										<table class="table table-borderless table-hover">
											<thead>
												<tr>
													<td><?php echo $ms_account_product_shipping_locations_destination; ?></td>
													<td><?php echo $ms_account_product_shipping_locations_company; ?></td>
													<td><?php echo $ms_account_product_shipping_locations_delivery_time; ?></td>
													<td><?php echo sprintf($ms_account_settings_shipping_weight, $this->weight->getUnit($this->config->get('config_weight_class_id'))); ?></td>
													<td><?php echo $ms_account_product_shipping_locations_cost_fixed_pwu; ?></td>
													<td></td>
												</tr>
											</thead>

											<tbody>
												<!-- sample row -->
												<tr class="ffSample">
													<td>
														<input type="hidden" class="location-id" name="seller_shipping[methods][0][to_geo_zone_id]" value="0" />
														<input type="text" class="form-control inline medium ac-shipping-locations" placeholder="<?php echo $ms_autocomplete; ?>" name="seller_shipping[methods][0][to_geo_zone_name]" value="" />
													</td>
													<td>
														<input type="hidden" class="method-id" name="seller_shipping[methods][0][shipping_method_id]" value="0" />
														<input type="text" class="form-control inline medium ac-shipping-methods" placeholder="<?php echo $ms_autocomplete; ?>" name="seller_shipping[methods][0][shipping_method_name]" value="" />
													</td>
													<td>
														<select class="form-control inline" name="seller_shipping[methods][0][delivery_time_id]" style="width: 90px;">
															<?php foreach($delivery_times as $delivery_time_id => $delivery_time_desc) { ?>
																<option value="<?php echo $delivery_time_id; ?>"><?php echo $delivery_time_desc[$this->config->get('config_language_id')]; ?></option>
															<?php } ?>
														</select>
													</td>
													<td>
														<div class="form-inline">
															<div class="input-group">
																<input type="text" class="form-control inline small" name="seller_shipping[methods][0][weight_from]" value="" size="4" />
																<span class="input-group-addon"><?php echo $ms_to; ?></span>
																<input type="text" class="form-control inline small" name="seller_shipping[methods][0][weight_to]" value="" size="4" />
															</div>
															<input type="hidden" name="seller_shipping[methods][0][weight_class_id]" value="<?php echo $this->config->get('config_weight_class_id'); ?>" />
														</div>
													</td>
													<td>
														<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
														<input type="text" class="form-control inline small mm_price" name="seller_shipping[methods][0][cost_fixed]" value="" size="4" />
														<span class="mm_currency"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
														+
														<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
														<input type="text" class="form-control inline small mm_price" name="seller_shipping[methods][0][cost_pwu]" value="" size="4" />
														<span class="mm_currency"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
													</td>
													<td><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
												</tr>

												<?php if (!empty($seller_shipping['methods'])) { ?>
													<?php $ssm_row = 1; ?>
													<?php foreach ($seller_shipping['methods'] as $ssm) { ?>
														<tr>
															<input type="hidden" name="seller_shipping[methods][<?php echo $ssm_row; ?>][seller_shipping_location_id]" value="<?php echo $ssm['seller_shipping_location_id']; ?>" />

															<td>
																<input type="hidden" class="location-id" name="seller_shipping[methods][<?php echo $ssm_row; ?>][to_geo_zone_id]" value="<?php echo $ssm['to_geo_zone_id']; ?>" />
																<input type="text" class="form-control inline medium ac-shipping-locations" placeholder="<?php echo $ms_autocomplete; ?>" name="seller_shipping[methods][<?php echo $ssm_row; ?>][to_geo_zone_name]" value="<?php echo $ssm['to_geo_zone_name']; ?>" />
															</td>
															<td>
																<input type="hidden" class="method-id" name="seller_shipping[methods][<?php echo $ssm_row; ?>][shipping_method_id]" value="<?php echo $ssm['shipping_method_id']; ?>" />
																<input type="text" class="form-control inline medium ac-shipping-methods" placeholder="<?php echo $ms_autocomplete; ?>" name="seller_shipping[methods][<?php echo $ssm_row; ?>][shipping_method_name]" value="<?php echo $ssm['shipping_method_name']; ?>" />
															</td>
															<td>
																<select class="form-control inline" name="seller_shipping[methods][<?php echo $ssm_row; ?>][delivery_time_id]" style="width: 90px;">
																	<?php foreach($delivery_times as $delivery_time_id => $delivery_time_desc) { ?>
																		<option value="<?php echo $delivery_time_id; ?>" <?php echo ($delivery_time_id == $ssm['delivery_time_id']) ? 'selected="selected"' : ''; ?>><?php echo $delivery_time_desc[$this->config->get('config_language_id')]; ?></option>
																	<?php } ?>
																</select>
															</td>
															<td>
																<div class="form-inline">
																	<div class="input-group">
																		<input type="text" class="form-control inline small" name="seller_shipping[methods][<?php echo $ssm_row; ?>][weight_from]" value="<?php echo $ssm['weight_from']; ?>" size="4" />
																		<span class="input-group-addon" style="padding: 0px 5px;"><?php echo $ms_to; ?></span>
																		<input type="text" class="form-control inline small" name="seller_shipping[methods][<?php echo $ssm_row; ?>][weight_to]" value="<?php echo $ssm['weight_to']; ?>" size="4" />
																	</div>
																	<input type="hidden" name="seller_shipping[methods][<?php echo $ssm_row; ?>][weight_class_id]" value="<?php echo $this->config->get('config_weight_class_id'); ?>" />
																</div>
															</td>
															<td>
																<div class="form-inline">
																	<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
																	<input type="text" class="form-control inline small mm_price" name="seller_shipping[methods][<?php echo $ssm_row; ?>][cost_fixed]" value="<?php echo $ssm['cost_fixed']; ?>" size="4" />
																	<span class="mm_currency"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
																	+
																	<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
																	<input type="text" class="form-control inline small mm_price" name="seller_shipping[methods][<?php echo $ssm_row; ?>][cost_pwu]" value="<?php echo $ssm['cost_pwu']; ?>" size="4" />
																	<span class="mm_currency"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
																</div>
															</td>
															<td><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
														</tr>
														<?php $ssm_row++; ?>
													<?php } ?>
												<?php } ?>
											</tbody>
										</table>

										<div class="col-sm-12">
											<div class="pull-left">
												<a class="btn btn-default ffClone"><?php echo $ms_account_settings_shipping_add; ?></a>
											</div>
											<div class="pull-right">
												<a class="btn btn-primary" id="ms-ssm-submit"><span><?php echo $ms_button_save; ?></span></a>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						<?php } ?>
					</div>

				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<?php $timestamp = time(); ?>
<script>
	var msGlobals = {
		timestamp: '<?php echo $timestamp; ?>',
		session_id: '<?php echo session_id(); ?>',
		uploadError: '<?php echo htmlspecialchars($ms_error_file_upload_error, ENT_QUOTES, "UTF-8"); ?>',
		formError: '<?php echo htmlspecialchars($ms_error_form_submit_error, ENT_QUOTES, "UTF-8"); ?>',
	};
</script>
<?php echo $footer; ?>