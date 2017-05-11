<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-category-form">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button id="ms-submit-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $this->url->link('multimerch/category', 'token=' . $this->session->data['token']); ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<div style="display: none" class="alert alert-danger" id="error-holder"><i class="fa fa-exclamation-circle"></i>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading; ?></h3>

				<div class="lang-chooser pull-right">
					<?php foreach ($languages as $language) { ?>
						<img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang <?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_admin_language')) ? 'active' : ''; ?>" data-lang="<?php echo $language['code']; ?>">
					<?php } ?>
				</div>
			</div>
			<div class="panel-body">
				<form id="ms-category-form" class="form-horizontal">
					<input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_seller_category_general; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_seller_category_name; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div>
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<input type="text" name="category_description[<?php echo $language['language_id']; ?>][name]" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" value="<?php echo $category['cat_name'][$language['language_id']]; ?>" />
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_seller_category_description; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field <?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_admin_language')) ? 'active' : ''; ?>" data-lang="<?php echo $language['code'] ?>">
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<textarea name="category_description[<?php echo $language['language_id']; ?>][description]" class="form-control summernote"><?php echo $category['cat_description'][$language['language_id']]; ?></textarea>
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group" style="display: none;">
							<label class="col-sm-2 control-label"><?php echo $ms_seller_category_meta_title; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div>
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<input type="text" class="lang-select-field form-control" data-lang="<?php echo $language['code']; ?>" name="category_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo $category['cat_meta_title'][$language['language_id']]; ?>" />
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group" style="display: none;">
							<label class="col-sm-2 control-label"><?php echo $ms_seller_category_meta_description; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div>
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<textarea class="lang-select-field form-control" data-lang="<?php echo $language['code']; ?>" name="category_description[<?php echo $language['language_id']; ?>][meta_description]"><?php echo $category['cat_meta_description'][$language['language_id']]; ?></textarea>
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="form-group" style="display: none;">
							<label class="col-sm-2 control-label"><?php echo $ms_seller_category_meta_keyword; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "language/{$language['code']}/{$language['code']}.png"; ?>
									<div>
										<div class="lang-img-icon-input"><img src="<?php echo $img?>"></div>
										<textarea class="lang-select-field form-control" data-lang="<?php echo $language['code']; ?>" name="category_description[<?php echo $language['language_id']; ?>][meta_keyword]"><?php echo $category['cat_meta_keyword'][$language['language_id']]; ?></textarea>
									</div>
								<?php } ?>
							</div>
						</div>
					</fieldset>

					<fieldset id="mm_data">
						<legend><?php echo $ms_seller_category_data; ?></legend>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_seller_category_seller; ?></label>
							<div class="col-sm-10">
								<?php if(isset($category['seller_id'])) { ?>
									<input type="hidden" name="seller_id" value="<?php echo $category['seller_id']; ?>" />
									<strong><?php echo $this->MsLoader->MsSeller->getSellerNickname($category['seller_id']) ?></strong>
								<?php } else { ?>
									<?php if (!empty($sellers)) { ?>
										<select name="seller_id" class="form-control">
											<?php foreach ($sellers as $s) { ?>
												<option value="<?php echo $s['seller_id']; ?>"><?php echo $s['ms.nickname']; ?></option>
											<?php } ?>
										</select>
									<?php } else { ?>
										<input type="hidden" name="seller_id" value="0" />
										<strong><?php echo $ms_seller_category_error_no_sellers; ?></strong>
									<?php } ?>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-parent"><?php echo $ms_seller_category_parent; ?></label>
							<div class="col-sm-10">
								<input type="text" name="path" value="<?php echo $category['path']; ?>" placeholder="<?php echo $ms_seller_category_parent; ?>" id="input-parent" class="form-control" />
								<input type="hidden" name="parent_id" value="<?php echo $category['parent_id']; ?>" />
							</div>
						</div>

						<div class="form-group" style="display: none;">
							<label class="col-sm-2 control-label" for="input-filter"><?php echo $ms_seller_category_filter; ?></span></label>
							<div class="col-sm-10">
								<input type="text" name="filter" value="" placeholder="<?php echo $ms_seller_category_filter; ?>" id="input-filter" class="form-control" />
								<div id="category-filter" class="well well-sm" style="height: 150px; overflow: auto;">
									<?php if(isset($category['filters'])) { ?>
										<?php foreach ($category['filters'] as $category_filter) { ?>
											<div id="category-filter<?php echo $category_filter['filter_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $category_filter['name']; ?>
												<input type="hidden" name="category_filter[]" value="<?php echo $category_filter['filter_id']; ?>" />
											</div>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $ms_seller_category_store; ?></label>
							<div class="col-sm-10">
								<div class="well well-sm" style="height: 150px; overflow: auto;">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="category_store[]" value="0" checked="checked" />
											<?php echo $ms_store_default; ?>
										</label>
									</div>
									<?php foreach($stores as $store) { ?>
										<div class="checkbox">
											<label>
												<input type="checkbox" name="category_store[]" value="<?php echo $store['store_id']; ?>" <?php echo in_array($store['store_id'], $category['stores']) ? 'checked="checked"' : ''; ?> />
												<?php echo $store['name']; ?>
											</label>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>

						<div class="form-group" style="display: none;">
							<label class="col-sm-2 control-label" for="input-keyword"><?php echo $ms_seller_category_keyword; ?></span></label>
							<div class="col-sm-10">
								<input type="text" name="keyword" value="<?php echo $category['keyword']; ?>" placeholder="<?php echo $ms_seller_category_keyword; ?>" id="input-keyword" class="form-control" />
							</div>
						</div>

						<div class="form-group" style="display: none;">
							<label class="col-sm-2 control-label"><?php echo $ms_seller_category_image; ?></label>
							<div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
								<input type="hidden" name="image" value="<?php echo $category['image']; ?>" id="input-image" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-sort-order"><?php echo $ms_seller_category_sort_order; ?></label>
							<div class="col-sm-10">
								<input type="text" name="sort_order" value="<?php echo $category['sort_order']; ?>" placeholder="<?php echo $ms_seller_category_sort_order; ?>" id="input-sort-order" class="form-control" />
							</div>
						</div>

						<?php if($category) { ?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-status"><?php echo $ms_seller_category_status; ?></label>
								<div class="col-sm-10">
									<select name="status" id="input-status" class="form-control">
										<?php foreach ($statuses as $cname => $cval) { ?>
											<option value="<?php echo $cval; ?>" <?php echo (int)$category['category_status'] == (int)$cval ? 'selected="selected"' : ''; ?>><?php echo $this->language->get('ms_seller_category_status_' . $cval); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } else { ?>
							<input type="hidden" name="status" value="<?php echo MsCategory::STATUS_ACTIVE; ?>" />
						<?php } ?>
					</fieldset>
				</form>
			</div>
		</div>
	</div>

	<script>
		var msGlobals = {
			token : "<?php echo $this->session->data['token']; ?>",
			config_enable_rte: "<?php echo $this->config->get('msconf_enable_rte'); ?>",
			current_language: "<?php echo $this->config->get('config_admin_language') ;?>",
			text_none: "<?php echo $text_none; ?>"
		};
	</script>
</div>
<?php echo $footer; ?>