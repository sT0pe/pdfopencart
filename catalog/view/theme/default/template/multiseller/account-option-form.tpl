<?php echo $header; ?>
<div class="container">

	<?php if (isset($success) && $success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
	<?php } ?>

	<div class="alert alert-danger" id="error-holder" style="display: none;"></div>

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
				<h1><?php echo $heading; ?></h1>

				<div class="lang-chooser">
					<?php foreach ($languages as $language) { ?>
						<img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang <?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'active' : ''; ?>" data-lang="<?php echo $language['code']; ?>">
					<?php } ?>
				</div>

				<form id="ms-new-option" class="tab-content ms-option">
					<input type="hidden" name="option_id" value="<?php echo $option['option_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_account_general; ?></legend>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_option_name; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
									</div>
									<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="option_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo $option['languages'][$language['language_id']]['name']; ?>" />
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_option_name_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_type; ?></label>
							<div class="col-sm-10">
								<select class="form-control" name="type">
									<?php foreach($option_types as $opt_group => $types) { ?>
										<optgroup label="<?php echo $this->language->get('ms_account_option_type_' . $opt_group); ?>">
											<?php foreach($types as $type) { ?>
												<option value="<?php echo $type; ?>" <?php echo isset($option['type']) && $option['type'] == $type ? 'selected="selected"' : ''; ?>><?php echo $this->language->get('ms_account_option_type_' . $type); ?></option>
											<?php } ?>
										</optgroup>
									<?php } ?>
								</select>
								<p class="ms-note"><?php echo $ms_account_option_type_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_sort_order; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="sort_order" value="<?php echo $option['sort_order']; ?>" />
								<p class="ms-note"><?php echo $ms_account_option_sort_order_note; ?></p>
							</div>
						</div>

						<?php if($option['option_status'] && $option['option_status'] != MsOption::STATUS_DISABLED) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_activate; ?></label>
								<div class="col-sm-10">
									<div class="radio">
										<label class="radio-inline">
											<input type="radio" name="option_status" value="<?php echo MsOption::STATUS_ACTIVE; ?>" <?php echo $option['option_status'] == MsOption::STATUS_ACTIVE || $option['option_status'] == MsOption::STATUS_APPROVED ? 'checked="checked"' : ''; ?> />
											<?php echo $ms_yes; ?>
										</label>
										<label class="radio-inline">
											<input type="radio" name="option_status" value="<?php echo MsOption::STATUS_INACTIVE; ?>" <?php echo $option['option_status'] == MsOption::STATUS_INACTIVE ? 'checked="checked"' : ''; ?> />
											<?php echo $ms_no; ?>
										</label>
									</div>
								</div>
							</div>
						<?php } ?>


						<table id="option-values" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<td class="text-left required"><?php echo $ms_account_option_value; ?></td>
									<td class="text-right"><?php echo $ms_sort_order; ?></td>
									<td></td>
								</tr>
							</thead>
							<tbody>
								<tr class="ffSample option_value">
									<td class="text-left">
										<?php foreach ($languages as $language) { ?>
											<div>
												<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="option_value[0][option_value_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($option_value['option_value_description'][$language['language_id']]) ? $option_value['option_value_description'][$language['language_id']]['name'] : ''; ?>" />
											</div>
										<?php } ?>
									</td>
									<td class="text-left">
										<div>
											<input type="text" name="option_value[0][sort_order]" value="" class="form-control" />
										</div>
									</td>
									<td class="text-center"><a class="icon-remove ms_remove_option_value" title="<?php echo $ms_delete; ?>"><i class="fa fa-times"></i></a></td>
								</tr>

								<?php if($option_values) { ?>
									<?php $i = 1; ?>
									<?php foreach ($option_values as $option_value) { ?>
										<tr class="option_value">
											<td class="text-left">
												<?php foreach ($languages as $language) { ?>
													<div>
														<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="option_value[<?php echo $i; ?>][option_value_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($option_value['option_value_description'][$language['language_id']]) ? $option_value['option_value_description'][$language['language_id']]['name'] : ''; ?>" />
													</div>
												<?php } ?>
											</td>
											<td class="text-left">
												<div>
													<input type="text" name="option_value[<?php echo $i; ?>][sort_order]" value="<?php echo $option_value['sort_order']; ?>" class="form-control" />
												</div>
											</td>
											<td class="text-center"><a class="icon-remove ms_remove_option_value" title="<?php echo $ms_delete; ?>"><i class="fa fa-times"></i></a></td>
										</tr>
										<?php $i++; ?>
									<?php } ?>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="2"></td>
									<td class="text-center"><a class="icon-add ms_add_option_value" title="<?php echo $ms_add; ?>"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
								</tr>
							</tfoot>
						</table>
					</fieldset>
				</form>

				<div class="buttons">
					<div class="pull-left"><a href="<?php echo $back; ?>"><span><?php echo $ms_button_cancel; ?></span></a></div>
					<div class="pull-right"><a class="btn btn-primary ms-spinner" id="ms-submit-button"><span><?php echo $ms_button_submit; ?></span></a></div>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<script>
	var msGlobals = {
		config_language: "<?php echo $this->config->get('config_language') ;?>"
	};
</script>
<?php echo $footer; ?>
