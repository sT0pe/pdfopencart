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

				<form id="ms-new-attribute" class="tab-content ms-attribute">
					<input type="hidden" name="attribute_id" value="<?php echo $attribute['attribute_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_account_general; ?></legend>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_attribute_name; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
									</div>
									<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="attribute_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo $attribute['languages'][$language['language_id']]['name']; ?>" />
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_attribute_name_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_attribute_group; ?></label>
							<div class="col-sm-10">
								<select name="attribute_group_id" id="input-attribute-group" class="form-control">
									<?php foreach ($attribute_groups as $attribute_group) { ?>
										<option value="<?php echo $attribute_group['attribute_group_id']; ?>" <?php if(isset($attribute['attribute_group_id']) && $attribute_group['attribute_group_id'] == $attribute['attribute_group_id']) { ?>selected="selected"<?php } ?>><?php echo $attribute_group['name']; ?></option>
									<?php } ?>
								</select>
								<p class="ms-note"><?php echo sprintf($ms_account_attribute_attr_group_note, $this->url->link('seller/account-attribute/createAttributeGroup')); ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_sort_order; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="sort_order" value="<?php echo $attribute['sort_order']; ?>" />
								<p class="ms-note"><?php echo $ms_account_attribute_sort_order_note; ?></p>
							</div>
						</div>

						<?php if($attribute['attribute_status'] && $attribute['attribute_status'] != MsAttribute::STATUS_DISABLED) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_activate; ?></label>
								<div class="col-sm-10">
									<div class="radio">
										<label class="radio-inline">
											<input type="radio" name="attribute_status" value="<?php echo MsAttribute::STATUS_ACTIVE; ?>" <?php echo $attribute['attribute_status'] == MsAttribute::STATUS_ACTIVE || $attribute['attribute_status'] == MsAttribute::STATUS_APPROVED ? 'checked="checked"' : ''; ?> />
											<?php echo $ms_yes; ?>
										</label>
										<label class="radio-inline">
											<input type="radio" name="attribute_status" value="<?php echo MsAttribute::STATUS_INACTIVE; ?>" <?php echo $attribute['attribute_status'] == MsAttribute::STATUS_INACTIVE ? 'checked="checked"' : ''; ?> />
											<?php echo $ms_no; ?>
										</label>
									</div>
								</div>
							</div>
						<?php } ?>
					</fieldset>
				</form>

				<div class="buttons">
					<div class="pull-left"><a href="<?php echo $back; ?>"><span><?php echo $ms_button_cancel; ?></span></a></div>
					<div class="pull-right"><a class="btn btn-primary ms-spinner" data-type="attribute" id="ms-submit-button"><span><?php echo $ms_button_submit; ?></span></a></div>
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
