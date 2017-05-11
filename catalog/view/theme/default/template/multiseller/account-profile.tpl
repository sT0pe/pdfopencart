<?php echo $header; ?>

<div class="container account-seller-profile">

	<?php if (isset($success) && $success) { ?>
	<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
	<?php } ?>
	<div class="alert alert-danger warning main error"></div>
	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?> ms-account-dashboard"><?php echo $content_top; ?>
			<div class="mm_dashboard">
				<?php if (isset($statustext) && ($statustext)) { ?>
					<div class="alert alert-<?php echo $statusclass; ?>"><?php echo $statustext; ?></div>
				<?php } ?>

				<h1><?php echo $ms_account_sellerinfo_heading; ?></h1>
				<div class="tab-content">
					<form id="ms-sellerinfo">
						<?php if ($this->config->get('msconf_change_group') AND (!isset($seller['seller_id']) || $seller['ms.seller_status'] == MsSeller::STATUS_INCOMPLETE)) { ?>
							<fieldset>
								<legend><?php echo $ms_account_profile_group ;?></legend>
								<input type="hidden" name="seller[seller_group]" value="<?php echo $seller_group_id; ?>" id="ms_group" />
								<div class="form-group">
									<?php foreach($seller_groups as $seller_group) { ?>
										<div class="col-md-4">
											<div id="group<?php echo $seller_group['seller_group_id']; ?>" class="change_group_block">
												<div class="mm_dashboard_block">
													<?php if ($seller_group['seller_group_id'] == $seller_group_id) { ?>
														<div class="head active_group"><?php echo $seller_group['name']; ?></div>
													<?php } else { ?>
														<div class="head"><?php echo $seller_group['name']; ?></div>
													<?php } ?>
													<ul class="group_fee">
														<li><?php echo sprintf($this->language->get('ms_account_group_signup fee'),$this->currency->format($seller_group['commissions'][3]['flat'], $this->config->get('config_currency'))); ?></li>
														<li><?php echo sprintf($this->language->get('ms_account_group_listing_fee'),$this->currency->format($seller_group['commissions'][2]['flat'], $this->config->get('config_currency'))); ?></li>
														<li><?php echo sprintf($this->language->get('ms_account_group_sale_fee'),$this->currency->format($seller_group['commissions'][1]['flat'], $this->config->get('config_currency')), $seller_group['commissions'][1]['percent']); ?></li>
													</ul>
													<hr align="center" width="80%" />
													<div class="text-center">
														<span data-group_id="<?php echo $seller_group['seller_group_id']; ?>" class="btn btn-primary select_plan"><?php echo $ms_account_group_select_plan ;?></span>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
							</fieldset>
						<?php } ?>

						<fieldset>
							<legend><?php echo $ms_account_profile_general ;?></legend>
							<input type="hidden" name="action" id="ms_action" />
							<div class="lang-chooser">
								<?php $i = 0; foreach ($languages as $language) { ?>
								<img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang <?php echo $i == 0 ? 'active' : ''; ?>" data-lang="<?php echo $language['code'] ?>">
								<?php $i++; } ?>
							</div>
							<!-- todo status check update -->
							<?php if ($seller['ms.seller_status'] == MsSeller::STATUS_DISABLED || $seller['ms.seller_status'] == MsSeller::STATUS_DELETED) { ?>
							<div class="ms-overlay"></div>
							<?php } ?>
	
							<div class="form-group required">
								<?php if (!$this->config->get('msconf_change_seller_nickname') && !empty($seller['ms.nickname'])) { ?>
								<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_sellerinfo_nickname; ?></label>
								<div class="mm_form col-sm-10">
									<b><?php echo $seller['ms.nickname']; ?></b>
								</div>
								<?php } else { ?>
								<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_sellerinfo_nickname; ?></label>
								<div class="mm_form col-sm-10">
									<input type="text" class="form-control"  name="seller[nickname]" value="<?php echo $seller['ms.nickname']; ?>" />
									<p class="ms-note"><?php echo $ms_account_sellerinfo_nickname_note; ?></p>
								</div>
								<?php } ?>
							</div>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_account_sellerinfo_description; ?></label>
								<div class="col-sm-10">
									<?php
								$i = 0;
								foreach ($languages as $language) {
									$img = "catalog/language/{$language['code']}/{$language['code']}.png";
									?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo $i == 0 ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<textarea name="seller[description][<?php echo $language['language_id']; ?>][description]" class="form-control mm_input_language mm_flag_<?php echo $language['code']; ?> <?php echo $this->config->get('msconf_enable_rte') ? 'ckeditor' : ''; ?>"><?php echo $this->config->get('msconf_enable_rte') ? htmlspecialchars_decode($seller['descriptions'][$language['language_id']]['description']) : strip_tags(htmlspecialchars_decode($seller['descriptions'][$language['language_id']]['description'])); ?></textarea>
									</div>
									<?php $i++; } ?>
									<p class="ms-note"><?php echo $ms_account_sellerinfo_description_note; ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_account_sellerinfo_avatar; ?></label>
								<div class="mm_form col-sm-10">
									<div id="sellerinfo_avatar_files">
										<div class="ms-image <?php if (!$seller['avatar']['name']) { ?>hidden<?php } ?>">
											<input type="hidden" name="seller[avatar_name]" value="<?php echo $seller['avatar']['name']; ?>" />
											<img src="<?php echo $seller['avatar']['thumb']; ?>" />
											<span class="ms-remove"><i class="fa fa-times"></i></span>
										</div>

										<div class="dragndropmini <?php if ($seller['avatar']['name']) { ?>hidden<?php } ?>" id="ms-avatar"><p class="mm_drophere"><?php echo $ms_drag_drop_click_here; ?></p></div>
										<p class="ms-note"><?php echo $ms_account_sellerinfo_avatar_note; ?></p>
										<div class="alert alert-danger" style="display: none;"></div>
										<div class="ms-progress progress"></div>
									</div>
								</div>
							</div>

							<?php if ($this->config->get('msconf_enable_seller_banner')) { ?>
								<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_account_sellerinfo_banner; ?></label>
								<div class="mm_form col-sm-10">
									<div id="sellerinfo_banner_files">
										<div class="ms-image <?php if (!$seller['banner']['name']) { ?>hidden<?php } ?>">
											<input type="hidden" name="seller[banner_name]" value="<?php echo $seller['banner']['name']; ?>" />
											<img src="<?php echo $seller['banner']['thumb']; ?>" />
											<span class="ms-remove"><i class="fa fa-times"></i></span>
										</div>

										<div class="dragndropmini <?php if ($seller['banner']['name']) { ?>hidden<?php } ?>" id="ms-banner"><p class="mm_drophere"><?php echo $ms_drag_drop_click_here; ?></p></div>
										<p class="ms-note"><?php echo $ms_account_sellerinfo_banner_note; ?></p>
										<div class="alert alert-danger" style="display: none;"></div>
										<div class="ms-progress progress"></div>
									</div>
								</div>
							</div>
							<?php } ?>
	
							<?php if ($ms_account_sellerinfo_terms_note) { ?>
								<div class="form-group required">
								<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_sellerinfo_terms; ?></label>
								<div class="col-sm-10">
									<p style="margin-bottom: 0">
										<input type="checkbox" name="seller[terms]" value="1" />
										<?php echo $ms_account_sellerinfo_terms_note; ?>
									</p>
								</div>
							</div>
							<?php } ?>

							<?php if ((!isset($seller['seller_id']) || $seller['ms.seller_status'] == MsSeller::STATUS_INCOMPLETE) && $seller_validation != MsSeller::MS_SELLER_VALIDATION_NONE) { ?>
								<div class="form-group">
								<label class="mm_label mm_req"><?php echo $ms_account_sellerinfo_reviewer_message; ?></label>
								<div class="col-sm-10">
									<textarea name="seller[reviewer_message]" id="message_textarea" class="form-control"></textarea>
									<p class="ms-note"><?php echo $ms_account_sellerinfo_reviewer_message_note; ?></p>
								</div>
							</div>
							<?php } ?>
						</fieldset>

						<?php if ($this->config->get('msconf_sl_status')) { ?>
							<fieldset>
								<legend><?php echo $ms_sl_social_media; ?></legend>
								<?php foreach($social_channels as $channel) { ?>
									<div class="form-group social_links">
									<label class="col-sm-2 control-label">
									<img src="<?php echo $channel['image']; ?>" title="<?php echo $channel['name']; ?>" />
									</label>
									<div class="col-sm-10">
									<input type="text" class="form-control"  name="seller[social_links][<?php echo $channel['channel_id'] ?>]" value="<?php echo isset($seller['social_links'][$channel['channel_id']]) ? $seller['social_links'][$channel['channel_id']]['channel_value'] : ''; ?>" />
									<p class="ms-note"><?php echo $channel['description']; ?></p>
									</div>
									</div>
								<?php } ?>
							</fieldset>
						<?php } ?>
					</form>
					<?php if (!$this->config->get('msconf_change_group')) { ?>
						<?php if (isset($group_commissions) && $group_commissions[MsCommission::RATE_SIGNUP]['flat'] > 0) { ?>
							<div class="alert alert-warning ms-commission">
								<p><?php echo sprintf($this->language->get('ms_account_sellerinfo_fee_flat'),$this->currency->format($group_commissions[MsCommission::RATE_SIGNUP]['flat'], $this->config->get('config_currency')), $this->config->get('config_name')); ?></p>
								<p><?php echo $ms_commission_payment_type; ?></p>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="buttons">
					<?php if ($seller['ms.seller_status'] != MsSeller::STATUS_DISABLED && $seller['ms.seller_status'] != MsSeller::STATUS_DELETED) { ?>
						<div class="pull-right">
							<a class="btn btn-primary ms-spinner" id="ms-submit-button">
								<span><?php echo $ms_button_submit; ?></span>
							</a>
						</div>
					<?php } ?>
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
		token : '<?php echo md5($salt . $timestamp); ?>',
		session_id: '<?php echo session_id(); ?>',
		uploadError: '<?php echo htmlspecialchars($ms_error_file_upload_error, ENT_QUOTES, "UTF-8"); ?>',
		formError: '<?php echo htmlspecialchars($ms_error_form_submit_error, ENT_QUOTES, "UTF-8"); ?>',
		config_enable_rte: '<?php echo $this->config->get('msconf_enable_rte'); ?>',
		zoneSelectError: '<?php echo htmlspecialchars($ms_account_sellerinfo_zone_select, ENT_QUOTES, "UTF-8"); ?>',
		zoneNotSelectedError: '<?php echo htmlspecialchars($ms_account_sellerinfo_zone_not_selected, ENT_QUOTES, "UTF-8"); ?>'
	};
</script>
<script>
	$(document).ready(function(){
		var lang_inputs = $('.lang-select-field');
		var current_language = "<?php echo $this->config->get('config_language') ;?>";
		for(var i = 0; i < lang_inputs.length; i++) {
			if($(lang_inputs[i]).data('lang') != current_language) {
				$(lang_inputs[i]).hide();
			} else {
				$(lang_inputs[i]).show();
			}
		}
	});
</script>

<?php echo $footer; ?>