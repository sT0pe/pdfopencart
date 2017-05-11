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

				<form id="ms-new-category" class="tab-content ms-category">
					<input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_account_general; ?></legend>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_category_name; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
									</div>
									<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="category_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo $category['cat_name'][$language['language_id']]; ?>" />
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_category_name_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_account_category_description; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input"><img src="<?php echo $img; ?>"></div>
										<textarea name="category_description[<?php echo $language['language_id']; ?>][description]" class="form-control mm_input_language mm_flag_<?php echo $language['code']; ?> <?php echo $this->config->get('msconf_enable_rte') ? 'ckeditor' : ''; ?>"><?php echo $category['cat_description'][$language['language_id']]; ?></textarea>
									</div>
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_category_description_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_sort_order; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="sort_order" value="<?php echo $category['sort_order']; ?>" />
								<p class="ms-note"><?php echo $ms_account_category_sort_order_note; ?></p>
							</div>
						</div>

						<?php if($category['category_status'] && $category['category_status'] != Mscategory::STATUS_DISABLED) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_activate; ?></label>
								<div class="col-sm-10">
									<div class="radio">
										<label class="radio-inline">
											<input type="radio" name="status" value="<?php echo MsCategory::STATUS_ACTIVE; ?>" <?php echo $category['category_status'] == MsCategory::STATUS_ACTIVE || $category['category_status'] == MsCategory::STATUS_APPROVED ? 'checked="checked"' : ''; ?> />
											<?php echo $ms_yes; ?>
										</label>
										<label class="radio-inline">
											<input type="radio" name="status" value="<?php echo MsCategory::STATUS_INACTIVE; ?>" <?php echo $category['category_status'] == MsCategory::STATUS_INACTIVE ? 'checked="checked"' : ''; ?> />
											<?php echo $ms_no; ?>
										</label>
									</div>
								</div>
							</div>
						<?php } ?>
					</fieldset>

					<fieldset>
						<legend><?php echo $ms_account_category_additional_data; ?></legend>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_account_category_parent; ?></label>
							<div class="col-sm-10">
								<input type="text" name="path" value="<?php echo $category['path']; ?>" placeholder="<?php echo $ms_account_category_parent; ?>" id="input-parent" class="form-control" />
								<input type="hidden" name="parent_id" value="<?php echo $category['parent_id']; ?>" />
								<p class="ms-note"><?php echo $ms_account_category_parent_note; ?></p>
							</div>
						</div>

						<div class="form-group" style="display: none;">
							<label class="mm_label col-sm-2" for="input-filter"><span data-toggle="tooltip"><?php echo $this->language->get('ms_entry_filter'); ?></span></label>
							<div class="col-sm-10">
								<input type="text" name="filter" value="" placeholder="<?php echo $this->language->get('ms_autocomplete'); ?>" id="input-filter" class="form-control" />
								<div id="category-filter" class="well well-sm" style="height: 150px; overflow: auto;">
									<?php if (!empty($category['filters'])) { ?>
										<?php foreach ($category['filters'] as $filter) { ?>
											<div id="category-filter<?php echo $filter['filter_id']; ?>"><i class="fa fa-minus-circle"></i>
												<?php echo $filter['name']; ?><input type="hidden" name="category_filter[]" value="<?php echo $filter['filter_id']; ?>" />
											</div>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
						</div>
					</fieldset>

					<fieldset style="display: none !important;">
						<legend><?php echo $ms_account_category_search_optimization; ?></legend>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_account_category_meta_title; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
									</div>
									<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="category_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo $category['cat_meta_title'][$language['language_id']]; ?>" />
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_category_meta_title_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_account_category_meta_description; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
									</div>
									<textarea class="lang-select-field lang-img-icon-textarea-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="category_description[<?php echo $language['language_id']; ?>][meta_description]"><?php echo $category['cat_meta_description'][$language['language_id']]; ?></textarea>
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_category_meta_description_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_account_category_meta_keyword; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
									</div>
									<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="category_description[<?php echo $language['language_id']; ?>][meta_keyword]" value="<?php echo $category['cat_meta_keyword'][$language['language_id']]; ?>" />
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_category_meta_keyword_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2"><?php echo $ms_account_category_seo_keyword; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="keyword" value="<?php echo $category['keyword']; ?>" />
								<p class="ms-note"><?php echo $ms_account_category_seo_keyword_note; ?></p>
							</div>
						</div>
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
		config_language: "<?php echo $this->config->get('config_language') ;?>",
        config_enable_rte: "<?php echo $this->config->get('msconf_enable_rte') ;?>",
		text_none: "<?php echo $ms_account_category_no_parent; ?>"
	};
</script>
<?php echo $footer; ?>
