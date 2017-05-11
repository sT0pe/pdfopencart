<?php echo $header; ?>
<div class="container">

	<?php if (isset($success) && $success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
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
			<div class="mm_dashboard">
				<h1><?php echo $heading; ?></h1>
				<div class="alert alert-danger fade in hidden" id="error-holder"></div>
				<div class="lang-chooser">
					<?php foreach ($languages as $language) { ?>
						<img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" width="19" class="select-input-lang <?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'active' : ''; ?>" data-lang="<?php echo $language['code'] ?>">
					<?php } ?>
				</div>

				<form id="ms-new-product" class="tab-content ms-product">
					<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
					<input type="hidden" name="action" id="ms_action" />

					<fieldset id="mm_general">
						<legend><?php echo $ms_account_product_name_description; ?></legend>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_name; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
									</div>
									<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="product_name[<?php echo $language['language_id']; ?>]" value="<?php echo $product['name'][$language['language_id']]; ?>" />
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_product_name_note; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_description; ?></label>
							<div class="col-sm-10">
								<?php foreach ($languages as $language) { ?>
									<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
									<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
										<div class="lang-img-icon-input"><img src="<?php echo $img; ?>"></div>
										<textarea name="product_description[<?php echo $language['language_id']; ?>]" class="form-control mm_input_language mm_flag_<?php echo $language['code']; ?> <?php echo $this->config->get('msconf_enable_rte') ? 'ckeditor' : ''; ?>"><?php echo $product['description'][$language['language_id']]; ?></textarea>
									</div>
								<?php } ?>
								<p class="ms-note"><?php echo $ms_account_product_description_note; ?></p>
							</div>
						</div>

						<?php if (in_array('price', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_price; ?></label>
								<div class="col-sm-10">
									<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
									<input type="text" class="form-control inline mm_price <?php if (isset($seller['commissions']) && ($seller['commissions'][MsCommission::RATE_LISTING]['percent'] > 0 || $seller['commissions'][MsCommission::RATE_LISTING]['flat'] > 0)) { ?>ms-price-dynamic <?php } ?>" name="product_price" value="<?php echo $product['price']; ?>" />
									<span class="mm_currency_right"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
									<p class="ms-note"><?php echo $ms_account_product_price_note; ?></p>

									<?php if ($this->config->get('msconf_fee_priority') == 2) { ?>
										<?php if (isset($seller['commissions']) && ($seller['commissions'][MsCommission::RATE_LISTING]['percent'] > 0 || $seller['commissions'][MsCommission::RATE_LISTING]['flat'] > 0)) { ?>
											<div class="alert alert-warning ms-commission">
												<p>
													<?php if ($seller['commissions'][MsCommission::RATE_LISTING]['percent'] > 0) { ?>
														<?php echo sprintf($this->language->get('ms_account_product_listing_percent'),$this->currency->format($seller['commissions'][MsCommission::RATE_LISTING]['flat'], $this->config->get('config_currency'))); ?>
													<?php } else if ($seller['commissions'][MsCommission::RATE_LISTING]['flat'] > 0) { ?>
														<?php echo sprintf($this->language->get('ms_account_product_listing_flat'),$this->currency->format($seller['commissions'][MsCommission::RATE_LISTING]['flat'], $this->config->get('config_currency'))); ?>
													<?php } ?>
												</p>
												<p><?php echo $ms_commission_payment_type; ?></p>
											</div>
										<?php } ?>
									<?php } else if ($this->config->get('msconf_fee_priority') == 1 && isset($enable_category_fee)) { ?>
										<!-- Get ajax category commissions -->
										<div class="alert alert-warning ms-commission">
											<p class="rate"><?php echo $this->language->get('ms_account_product_listing_category_note'); ?></p>
											<p class="type" style="display:none;"></p>
										</div>
									<?php } ?>
								</div>
							</div>
						<?php } else { ?>
							<input type="hidden" name="product_price" value="0" />
						<?php } ?>

						<?php if (in_array('quantity', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_quantity; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control inline" name="product_quantity" value="<?php echo $product['quantity']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_quantity_note; ?></p>
								</div>
							</div>
						<?php } else { ?>
							<input type="hidden" name="product_quantity" value="999" />
						<?php } ?>

						<?php if (in_array('minOrderQty', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_minorderqty; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control inline" name="minimum" value="<?php echo $product['minimum']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_minorderqty_note; ?></p>
								</div>
							</div>
						<?php } else { ?>
							<input type="hidden" name="minimum" value="1" />
						<?php } ?>

						<?php if($this->config->get('msconf_allow_digital_products') == 1) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_digital; ?></label>
								<div class="col-sm-10">
									<select class="form-control" name="product_is_digital">
										<option value="1" <?php if (isset($product['oc_shipping']) && !$product['oc_shipping']) { ?>selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
										<option value="0" <?php if (isset($product['oc_shipping']) && $product['oc_shipping']) { ?>selected="selected"<?php } ?>><?php echo $text_no; ?></option>
									</select>
								</div>
							</div>
						<?php } ?>
					</fieldset>

					<!-- Category -->
					<?php if (in_array('category', $this->config->get('msconf_product_included_fields'))) { ?>
						<?php if(!$this->config->get('msconf_allow_seller_categories') || ($this->config->get('msconf_product_category_type') == 1 || $this->config->get('msconf_product_category_type') == 3)) { ?>
							<fieldset>
								<legend><?php echo $ms_account_product_categories ;?></legend>

								<div class="form-group required">
									<label class="mm_label col-sm-2"><?php echo $ms_account_category; ?></label>

									<div class="col-sm-10" id="product_oc_category_block">
										<?php if(empty($product['oc_categories'])) { ?>
											<!-- If there are no categories assigned to this product, show default selector -->

											<div class="row category-holder">
												<div class="form-group category">
													<div class="col-sm-12">
														<select class="form-control category-select" name="product_category_1[]">
															<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
															<?php foreach($categories as $category) { ?>
																<!-- Show only top level (not having parent) categories -->
																<?php if($category['parent_id'] == 0) { ?>
																	<option value="<?php echo $category['category_id'] ;?>"><?php echo $category['name'] ;?></option>
																<?php } ?>
															<?php } ?>
														</select>
													</div>
												</div>
											</div>

										<?php } else { ?>
											<!-- If there are categories assigned to this product -->

											<?php foreach($product['oc_categories'] as $row_id => $oc_cat_ids) { ?>
												<div class="row category-holder">
													<?php foreach($oc_cat_ids as $parent_cat_id => $cat_id) { ?>
														<div class="form-group category">
															<div class="col-sm-12">
																<select class="form-control category-select" name="product_category_<?php echo $row_id + 1; ?>[]" data-selected="true">
																	<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
																	<?php foreach($categories as $category) { ?>
																		<?php if($category['parent_id'] == $parent_cat_id) { ?>
																			<option value="<?php echo $category['category_id']; ?>" <?php echo $category['category_id'] == $cat_id ? 'selected="selected"' : ''; ?>><?php echo $category['name'] ;?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
													<?php } ?>

													<?php if ($msconf_allow_multiple_categories) { ?>
														<div class="category-actions pull-right">
															<a href="#" class="remove">
																<i class="fa fa-times" aria-hidden="true"></i>
															</a>
														</div>
													<?php } ?>
												</div>
											<?php } ?>

										<?php } ?>

										<?php if ($msconf_allow_multiple_categories) { ?>
											<a href="#" class="add-category">
												<i class="fa fa-plus" aria-hidden="true"></i>
											</a>
										<?php } ?>
									</div>
								</div>

								<div class="hidden">
									<!-- select field -->
									<div class="form-group category" id="cloneSelect">
										<div class="col-sm-12">
											<select class="form-control category-select" name="product_category_0[]">
												<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
											</select>
										</div>
									</div>

									<!-- Whole row -->
									<div class="row category-holder" id="cloneRow">
										<div class="form-group category">
											<div class="col-sm-12">
												<select class="form-control category-select" name="product_category_0[]">
													<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
													<?php foreach($categories as $category) { ?>
														<?php if($category['parent_id'] == 0) { ?>
															<option value="<?php echo $category['category_id'] ;?>"><?php echo $category['name'] ;?></option>
														<?php } ?>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="category-actions pull-right">
											<a href="#" class="remove">
												<i class="fa fa-times" aria-hidden="true"></i>
											</a>
										</div>
									</div>
								</div>

								<p class="ms-note"><?php echo $ms_account_product_category_note; ?></p>
							</fieldset>
						<?php } ?>

						<?php if($this->config->get('msconf_allow_seller_categories') && ($this->config->get('msconf_product_category_type') == 2 || $this->config->get('msconf_product_category_type') == 3)) { ?>
							<fieldset>
								<legend><?php echo $ms_account_product_vendor_categories ;?></legend>

								<div class="form-group">
									<label class="mm_label col-sm-2"><?php echo $ms_account_category; ?></label>

									<div class="col-sm-10" id="product_ms_category_block">
										<?php if(empty($product['ms_categories'])) { ?>
											<!-- If there are no MsCategories assigned to this product, show default selector -->

											<div class="row category-holder">
												<div class="form-group category">
													<div class="col-sm-12">
														<select class="form-control category-select" name="product_ms_category_1[]">
															<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
															<?php foreach($ms_categories as $category) { ?>
																<!-- Show only top level (not having parent) categories -->
																<?php if($category['parent_id'] == 0) { ?>
																	<option value="<?php echo $category['category_id'] ;?>"><?php echo $category['name'] ;?></option>
																<?php } ?>
															<?php } ?>
														</select>
													</div>
												</div>
											</div>
										<?php } else { ?>
											<!-- If there are categories assigned to this product -->

											<?php foreach($product['ms_categories'] as $row_id => $ms_cat_ids) { ?>
												<div class="row category-holder">
													<?php foreach($ms_cat_ids as $parent_cat_id => $cat_id) { ?>
														<div class="form-group category">
															<div class="col-sm-12">
																<select class="form-control category-select" name="product_ms_category_<?php echo $row_id + 1; ?>[]" data-selected="true">
																	<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
																	<?php foreach($ms_categories as $category) { ?>
																		<?php if($category['parent_id'] == $parent_cat_id) { ?>
																			<option value="<?php echo $category['category_id']; ?>" <?php echo $category['category_id'] == $cat_id ? 'selected="selected"' : ''; ?>><?php echo $category['name'] ;?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
														</div>
													<?php } ?>

													<?php if ($msconf_allow_multiple_categories) { ?>
														<div class="category-actions pull-right">
															<a href="#" class="remove">
																<i class="fa fa-times" aria-hidden="true"></i>
															</a>
														</div>
													<?php } ?>
												</div>
											<?php } ?>
										<?php } ?>

										<?php if ($msconf_allow_multiple_categories) { ?>
											<a href="#" class="add-category">
												<i class="fa fa-plus" aria-hidden="true"></i>
											</a>
										<?php } ?>
									</div>
								</div>

								<div class="hidden">
									<!-- select field -->
									<div class="form-group category" id="cloneMsSelect">
										<div class="col-sm-12">
											<select class="form-control category-select" name="product_ms_category_0[]">
												<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
											</select>
										</div>
									</div>

									<!-- Whole row -->
									<div class="row category-holder" id="cloneMsRow">
										<div class="form-group category">
											<div class="col-sm-12">
												<select class="form-control category-select" name="product_ms_category_0[]">
													<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
													<?php foreach($ms_categories as $category) { ?>
														<?php if($category['parent_id'] == 0) { ?>
															<option value="<?php echo $category['category_id'] ;?>"><?php echo $category['name'] ;?></option>
														<?php } ?>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="category-actions pull-right">
											<a href="#" class="remove">
												<i class="fa fa-times" aria-hidden="true"></i>
											</a>
										</div>
									</div>
								</div>

								<p class="ms-note"><?php echo $ms_account_product_vendor_category_note; ?></p>
							</fieldset>
						<?php } ?>
					<?php } else { ?>
						<input type="hidden" name="product_category[]" value="0">
					<?php } ?>

					<!-- SEO -->
					<fieldset id="mm_search_optimization">
						<legend><?php echo $ms_account_product_search_optimization; ?></legend>

						<?php if (in_array('tags', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_tags; ?></label>
								<div class="col-sm-10">
									<?php foreach ($languages as $language) { ?>
										<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
										<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
											<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
										</div>
										<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="product_tags[<?php echo $language['language_id']; ?>]" value="<?php echo $product['tags'][$language['language_id']]; ?>" />
									<?php } ?>
									<p class="ms-note"><?php echo $ms_account_product_tags_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('metaTitle', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_meta_title; ?></label>
								<div class="col-sm-10">
									<?php foreach ($languages as $language) { ?>
										<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
										<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
											<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
										</div>
										<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="product_meta_title[<?php echo $language['language_id']; ?>]" value="<?php echo $product['product_meta_title'][$language['language_id']]; ?>" />
									<?php } ?>
									<p class="ms-note"><?php echo $ms_account_product_meta_title_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('metaDescription', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_meta_description; ?></label>
								<div class="col-sm-10">
									<?php foreach ($languages as $language) { ?>
										<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
										<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
											<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
										</div>
										<textarea class="lang-select-field lang-img-icon-textarea-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="product_meta_description[<?php echo $language['language_id']; ?>]"><?php echo $product['product_meta_description'][$language['language_id']]; ?></textarea>
									<?php } ?>
									<p class="ms-note"><?php echo $ms_account_product_meta_description_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('metaKeywords', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_meta_keyword; ?></label>
								<div class="col-sm-10">
									<?php foreach ($languages as $language) { ?>
										<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
										<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
											<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
										</div>
										<input type="text" class="lang-select-field lang-img-icon-text-input form-control mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>" name="product_meta_keywords[<?php echo $language['language_id']; ?>]" value="<?php echo $product['product_meta_keywords'][$language['language_id']]; ?>" />
									<?php } ?>
									<p class="ms-note"><?php echo $ms_account_product_meta_keyword_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('seoURL', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_seo_keyword; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="keyword" value="<?php echo $product['keyword']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_seo_keyword_note; ?></p>
								</div>
							</div>
						<?php } ?>
					</fieldset>

					<!-- extra OC fields -->
					<fieldset id="mm_additional_data">
						<legend><?php echo $ms_account_product_additional_data; ?></legend>

						<?php if (in_array('model', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_model; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="product_model" value="<?php echo $product['model']; ?>" />
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('sku', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_sku; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="product_sku" value="<?php echo $product['sku']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_sku_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('upc', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_upc; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="product_upc" value="<?php echo $product['upc']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_upc_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('ean', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_ean; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="product_ean" value="<?php echo $product['ean']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_ean_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('jan', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_jan; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="product_jan" value="<?php echo $product['jan']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_jan_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('isbn', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_isbn; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="product_isbn" value="<?php echo $product['isbn']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_isbn_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('mpn', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_mpn; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="product_mpn" value="<?php echo $product['mpn']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_mpn_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('manufacturer', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_manufacturer; ?></label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="product_manufacturer" value="<?php echo $product['manufacturer'] ?>" />
									<input type="hidden" name="product_manufacturer_id" value="<?php echo $product['manufacturer_id']; ?>" />
									<p class="ms-note"><?php echo $ms_account_product_manufacturer_note; ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('taxClass', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_tax_class; ?></label>
								<div class="col-sm-10">
									<select class="form-control" name="product_tax_class_id">
										<option value="0"><?php echo $text_none; ?></option>
										<?php foreach ($tax_classes as $tax_class) { ?>
											<option value="<?php echo $tax_class['tax_class_id']; ?>" <?php if ($tax_class['tax_class_id'] == $product['tax_class_id']) { ?>selected="selected" <?php } ?>><?php echo $tax_class['title']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('subtract', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_subtract; ?></label>
								<div class="col-sm-10">
									<select class="form-control" name="product_subtract">
										<option value="1" <?php if (isset($product['subtract']) && $product['subtract']) { ?>selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
										<option value="0" <?php if (isset($product['subtract']) && !$product['subtract']) { ?>selected="selected"<?php } ?>><?php echo $text_no; ?></option>
									</select>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('stockStatus', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_stock_status; ?></label>
								<div class="col-sm-10">
									<select class="form-control" name="product_stock_status_id">
										<?php foreach ($stock_statuses as $stock_status) { ?>
											<option value="<?php echo $stock_status['stock_status_id']; ?>" <?php if ($stock_status['stock_status_id'] == $product['stock_status_id']) { ?>selected="selected" <?php } ?>><?php echo $stock_status['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('dateAvailable', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group required">
								<label class="mm_label col-sm-2"><?php echo $ms_account_product_date_available; ?></label>
								<div class="col-sm-10"><input type="text" class="form-control" name="product_date_available" value="<?php echo $date_available; ?>" size="12" class="date" /></div>
							</div>
						<?php } ?>

						<?php if (in_array('filters', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2" for="input-filter"><span data-toggle="tooltip"><?php echo $this->language->get('ms_entry_filter'); ?></span></label>
								<div class="col-sm-10">
									<input type="text" name="filter" value="" placeholder="<?php echo $this->language->get('ms_autocomplete'); ?>" id="input-filter" class="form-control" />
									<div id="product-filter" class="well well-sm" style="height: 150px; overflow: auto;">
										<?php if (isset($product_filters) && $product_filters) foreach ($product_filters as $product_filter) { ?>
											<div id="product-filter<?php echo $product_filter['filter_id']; ?>"><i class="fa fa-minus-circle"></i>
												<?php echo $product_filter['name']; ?><input type="hidden" name="product_filter[]" value="<?php echo $product_filter['filter_id']; ?>" />
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php } ?>

						<?php if (in_array('relatedProducts', $this->config->get('msconf_product_included_fields'))) { ?>
							<div class="form-group">
								<label class="mm_label col-sm-2" for="input-related"><span data-toggle="tooltip"><?php echo $this->language->get('ms_catalog_products_related_products'); ?></span></label>
								<div class="col-sm-10">
									<input type="text" name="related" value="" placeholder="<?php echo $this->language->get('ms_autocomplete'); ?>" id="input-related" class="form-control" />
									<div id="product-related" class="well well-sm" style="height: 150px; overflow: auto;">
										<?php foreach ($product_relateds as $product_related) { ?>
											<div id="product-related<?php echo $product_related['product_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_related['name']; ?>
												<input type="hidden" name="product_related[]" value="<?php echo $product_related['product_id']; ?>" />
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php } ?>

					</fieldset>

					<?php if (in_array('dimensions', $this->config->get('msconf_product_included_fields')) || in_array('weight', $this->config->get('msconf_product_included_fields'))) { ?>
						<fieldset id="mm_product_dimensions">
							<legend><?php echo $ms_catalog_products_measurements; ?></legend>
							<?php $dimensions_format = "%01.2f"; ?>
							<?php if(in_array('dimensions', $this->config->get('msconf_product_included_fields'))) { ?>
								<div class="form-inline">
									<label class="mm_label col-sm-2" for="input-length"><span data-toggle="tooltip"><?php echo $this->language->get('ms_catalog_products_size'); ?></span></label>
									<input type="text" name="length" value="<?php printf($dimensions_format, $length); ?>" placeholder="<?php echo $ms_catalog_products_size_length; ?>" id="input-length" class="form-control" size="8" /> x
									<input type="text" name="width" value="<?php printf($dimensions_format, $width); ?>" placeholder="<?php echo $ms_catalog_products_size_width; ?>" id="input-width" class="form-control" size="8" /> x
									<input type="text" name="height" value="<?php printf($dimensions_format, $height) ?>" placeholder="<?php echo $ms_catalog_products_size_height; ?>" id="input-height" class="form-control" size="8" />
									<select name="length_class_id" id="input-length-class" class="form-control">
										<?php foreach ($length_classes as $length_class) { ?>
											<?php if ($length_class['length_class_id'] == $length_class_id) { ?>
												<option value="<?php echo $length_class['length_class_id']; ?>" selected="selected"><?php echo $length_class['title']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $length_class['length_class_id']; ?>"><?php echo $length_class['title']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</div>
							<?php } ?>

							<?php if(in_array('weight', $this->config->get('msconf_product_included_fields'))) { ?>
								<br />
								<div class="form-inline">
									<label class="mm_label col-sm-2" for="input-weight"><span data-toggle="tooltip"><?php echo $ms_catalog_products_weight; ?></span></label>
									<input type="text" name="weight" value="<?php printf($dimensions_format, $weight); ?>" placeholder="<?php echo $ms_catalog_products_weight; ?>" id="input-weight" class="form-control" size="8"/>
									<select name="weight_class_id" id="input-weight-class" class="form-control">
										<?php foreach ($weight_classes as $weight_class) { ?>
											<?php if ($weight_class['weight_class_id'] == $weight_class_id) { ?>
												<option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
											<?php } else { ?>
												<option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</div>
							<?php } ?>
						</fieldset>
					<?php } ?>

					<!-- attributes -->
					<?php if (in_array('attributes', $this->config->get('msconf_product_included_fields'))) { ?>
						<fieldset id="mm_product_attributes">
							<legend><?php echo $ms_account_product_attributes; ?></legend>
							<span class="mm_clone"></span>
							<div class="form-group mm_attribute ffSample">
								<label class="mm_label col-sm-2">
									<input type="hidden" name="product_attribute[0][name]" value="" />
									<input type="hidden" name="product_attribute[0][attribute_id]" value="" />
								</label>

								<div class="col-sm-10">
									<?php foreach ($languages as $language) { ?>
										<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
										<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
											<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
										</div>
										<textarea name="product_attribute[0][product_attribute_description][<?php echo $language['language_id']; ?>][text]" rows="3" placeholder="<?php echo $ms_account_product_value; ?>" class="lang-select-field lang-img-icon-textarea-input form-control inline mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>"></textarea>
									<?php } ?>
									<a class="icon-remove mm_vtop mm_remove" title="Delete"><i class="fa fa-times"></i></a>
								</div>
							</div>

							<?php if (isset($product_attributes) && !empty($product_attributes)) { ?>
								<?php $i = 1; ?>
								<?php foreach ($product_attributes as $product_attribute) { ?>
									<div class="form-group mm_attribute">
										<label class="mm_label col-sm-2">
											<input type="hidden" name="product_attribute[<?php echo $i; ?>][name]" value="<?php echo $product_attribute['name']; ?>" />
											<input type="hidden" name="product_attribute[<?php echo $i; ?>][attribute_id]" value="<?php echo $product_attribute['attribute_id']; ?>" />
											<?php echo $product_attribute['name']; ?>
										</label>

										<div class="col-sm-10">
											<?php foreach ($languages as $language) { ?>
												<?php $img = "catalog/language/{$language['code']}/{$language['code']}.png"; ?>
												<div class="lang-textarea lang-select-field" data-lang="<?php echo $language['code'] ?>" data-lang-default="<?php echo (int)$language['language_id'] == (int)$this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language')) ? 'true' : 'false'; ?>">
													<div class="lang-img-icon-input-text"><img src="<?php echo $img; ?>"></div>
												</div>
												<textarea name="product_attribute[<?php echo $i; ?>][product_attribute_description][<?php echo $language['language_id']; ?>][text]" rows="3" placeholder="<?php echo $ms_account_product_value; ?>" class="lang-select-field lang-img-icon-textarea-input form-control inline mm_input_language mm_flag mm_flag_<?php echo $language['code']; ?>" data-lang="<?php echo $language['code']; ?>"><?php echo isset($product_attribute['product_attribute_description'][$language['language_id']]) ? $product_attribute['product_attribute_description'][$language['language_id']]['text'] : ''; ?></textarea>
											<?php } ?>
											<a class="icon-remove mm_vtop mm_remove" title="Delete"><i class="fa fa-times"></i></a>
										</div>
									</div>
									<?php $i++; ?>
								<?php } ?>
							<?php } ?>

							<div>
								<label class="mm_label col-sm-3">
									<input type="text" id="mm_attribute_new" value="" placeholder="<?php echo $ms_account_product_new_attribute; ?>" class="form-control" />
								</label>
								<div class="col-sm-9"></div>
							</div>
						</fieldset>
					<?php } ?>

					<!-- options -->
					<?php if (in_array('options', $this->config->get('msconf_product_included_fields'))) { ?>
						<fieldset id="mm_product_options">
							<legend><?php echo $ms_account_product_tab_options; ?></legend>

							<div class="form-group mm_option ffSample container-fluid"></div>

							<?php $option_row = 0; ?>
							<?php foreach ($product_options as $product_option) { ?>
								<div class="form-group mm_option container-fluid"><?php echo $product_option['val']; ?></div>
								<?php $option_row++; ?>
							<?php } ?>

							<div>
								<label class="mm_label col-sm-3">
									<input type="text" id="mm_option_new" value="" placeholder="<?php echo $ms_options_add; ?>" class="form-control" />
								</label>
								<div class="col-sm-9"></div>
							</div>
						</fieldset>
					<?php } ?>

					<!-- specials -->
					<?php if (in_array('special_prices', $this->config->get('msconf_product_included_fields'))) { ?>
						<fieldset id="mm_product_specials">
							<legend><?php echo $ms_account_product_tab_specials; ?></legend>
							<table class="table table-borderless table-hover">
								<thead>
								<tr>
									<td><?php echo $ms_account_product_priority; ?></td>
									<!--<td <?php if ($hide_customer_groups) { ?> class="hidden" <?php } ?>><?php echo $ms_account_product_customer_group; ?></td>-->
									<td><?php echo $ms_account_product_price; ?></td>
									<td><?php echo $ms_account_product_date_start; ?></td>
									<td><?php echo $ms_account_product_date_end; ?></td>
									<td></td>
								</tr>
								</thead>

								<tbody>
								<!-- sample row -->
								<tr class="ffSample">
									<td><input type="text" class="form-control inline small" name="product_specials[0][priority]" value="" size="2" /></td>
									<td><input type="text" class="form-control inline small" name="product_specials[0][price]" value="" /></td>
									<td>
										<div class="input-group date">
											<input type="text" class="form-control inline" name="product_specials[0][date_start]" value="" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</td>
									<td>
										<div class="input-group date">
											<input type="text" class="form-control inline" name="product_specials[0][date_end]" value="" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</td>
									<td><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
								</tr>

								<?php if (isset($product['specials'])) { ?>
									<?php $special_row = 1; ?>
									<?php foreach ($product['specials'] as $product_special) { ?>
										<tr>
											<td><input type="text" class="form-control inline small" name="product_specials[<?php echo $special_row; ?>][priority]" value="<?php echo $product_special['priority']; ?>" size="2" /></td>
											<td><input type="text" class="form-control inline small" name="product_specials[<?php echo $special_row; ?>][price]" value="<?php echo $product_special['price']; ?>" /></td>
											<td>
												<div class="input-group date">
													<input type="text" class="form-control inline" name="product_specials[<?php echo $special_row; ?>][date_start]" value="<?php echo $product_special['date_start']; ?>" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
												</div>
											</td>
											<td>
												<div class="input-group date">
													<input type="text" class="form-control inline" name="product_specials[<?php echo $special_row; ?>][date_end]" value="<?php echo $product_special['date_end']; ?>" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
												</div>
											</td>
											<td><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
										</tr>
										<?php $special_row++; ?>
									<?php } ?>
								<?php } ?>
								</tbody>
							</table>

							<div>
								<label class="mm_label col-sm-2">
									<a class="btn btn-default ffClone"><?php echo $ms_button_add_special; ?></a>
								</label>
								<div class="col-sm-10"></div>
							</div>
						</fieldset>
					<?php } ?>

					<!-- discounts -->
					<?php if (in_array('quantity_discounts', $this->config->get('msconf_product_included_fields'))) { ?>
						<fieldset id="mm_product_discounts">
							<legend><?php echo $ms_account_product_tab_discounts; ?></legend>
							<table class="table table-borderless table-hover">
								<thead>
								<tr>
									<td><?php echo $ms_account_product_priority; ?></td>
									<td><?php echo $ms_account_product_quantity; ?></td>
									<!--<td <?php if ($hide_customer_groups) { ?> class="hidden" <?php } ?>><?php echo $ms_account_product_customer_group; ?></td>-->
									<td><?php echo $ms_account_product_price; ?></td>
									<td><?php echo $ms_account_product_date_start; ?></td>
									<td><?php echo $ms_account_product_date_end; ?></td>
									<td></td>
								</tr>
								</thead>

								<tbody>
								<!-- sample row -->
								<tr class="ffSample">
									<td><input type="text" class="form-control inline small" name="product_discounts[0][priority]" value="" size="2" /></td>
									<td><input type="text" class="form-control inline small" name="product_discounts[0][quantity]" value="" size="2" /></td>
									<td><input type="text" class="form-control inline small" name="product_discounts[0][price]" value="" /></td>
									<td>
										<div class="input-group date">
											<input type="text" class="form-control inline" name="product_discounts[0][date_start]" value="" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</td>
									<td>
										<div class="input-group date">
											<input type="text" class="form-control inline" name="product_discounts[0][date_end]" value="" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</td>
									<td><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
								</tr>

								<?php if (isset($product['discounts'])) { ?>
									<?php $discount_row = 1; ?>
									<?php foreach ($product['discounts'] as $product_discount) { ?>
										<tr>
											<td><input type="text" class="form-control inline small" name="product_discounts[<?php echo $discount_row; ?>][priority]" value="<?php echo $product_discount['priority']; ?>" size="2" /></td>
											<td><input type="text" class="form-control inline small" name="product_discounts[<?php echo $discount_row; ?>][quantity]" value="<?php echo $product_discount['quantity']; ?>" size="2" /></td>
											<td><input type="text" class="form-control inline small" name="product_discounts[<?php echo $discount_row; ?>][price]" value="<?php echo $product_discount['price']; ?>" /></td>
											<td>
												<div class="input-group date">
													<input type="text" class="form-control inline" name="product_discounts[<?php echo $discount_row; ?>][date_start]" value="<?php echo $product_discount['date_start']; ?>" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
												</div>
											</td>
											<td>
												<div class="input-group date">
													<input type="text" class="form-control inline" name="product_discounts[<?php echo $discount_row; ?>][date_end]" value="<?php echo $product_discount['date_end']; ?>" data-date-format="YYYY-MM-DD" />
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
												</div>
											</td>
											<td><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
										</tr>
										<?php $discount_row++; ?>
									<?php } ?>
								<?php } ?>
								</tbody>
							</table>

							<div>
								<label class="mm_label col-sm-2">
									<a class="btn btn-default ffClone"><?php echo $ms_button_add_discount; ?></a>
								</label>
								<div class="col-sm-10"></div>
							</div>
						</fieldset>
					<?php } ?>

					<!-- Vendor Shipping -->
					<?php if($this->config->get('msconf_shipping_type') == 2) { ?>
						<fieldset id="mm_product_shipping">
							<legend><?php echo $ms_account_product_tab_shipping; ?></legend>

							<?php if($this->config->get('msconf_vendor_shipping_type') == 1 || $this->config->get('msconf_vendor_shipping_type') == 3) { ?>
								<div class="alert alert-info"><?php echo $ms_account_product_shipping_combined_enabled; ?></div>
							<?php } ?>

							<?php if($this->config->get('msconf_vendor_shipping_type') == 3) { ?>
								<div class="form-group">
									<div class="col-sm-6 combined_shipping_override">
										<div class="checkbox">
											<input type="hidden" name="product_shipping[override]" value="0"/>
											<input type="checkbox" name="product_shipping[override]" value="1" <?php echo (isset($product['shipping']['override']) && (int)$product['shipping']['override'] == 1) ? 'checked="checked"' : '' ;?>/>
										</div>
										<div class="inline">
											<span><?php echo $ms_account_product_shipping_combined_override; ?></span>
										</div>
									</div>
									<div class="col-sm-6"></div>
								</div>
							<?php } ?>

							<div class="clearfix"></div>

							<div style="position: relative;">
								<div class="grey_out <?php echo $this->config->get('msconf_vendor_shipping_type') == 3 ? ((int)$product['shipping']['override'] == 1 ? 'hidden' : '') : ($this->config->get('msconf_vendor_shipping_type') == 1 ? '' : 'hidden'); ?>"></div>

								<div class="form-group">
									<label class="mm_label col-sm-3">
										<p class="ms-note"><?php echo $ms_account_product_shipping_from; ?></p>
										<input type="hidden" class="country-id" name="product_shipping[from_country][id]" value="<?php echo isset($product['shipping']['from_country_id']) ? $product['shipping']['from_country_id'] : '0' ;?>" />
										<input type="text" id="mm_shipping_from_country" class="form-control ac-shipping-countries" name="product_shipping[from_country][name]" value="<?php echo isset($product['shipping']['from_country_name']) ? $product['shipping']['from_country_name'] : '' ;?>" />
									</label>
									<div class="col-sm-9"></div>
								</div>

								<div class="clearfix"></div>

								<div class="form-group">
									<label class="mm_label col-sm-3">
										<p class="ms-note"><?php echo $ms_account_product_shipping_processing_time; ?></p>
										<div class = "input-group">
											<span class="input-group-addon">Up to</span>
											<input type="text" class="form-control text-center" name="product_shipping[processing_time]" value="<?php echo isset($product['shipping']['processing_time']) ? $product['shipping']['processing_time'] : 0 ;?>"/>
											<span class="input-group-addon">days</span>
										</div>
									</label>
									<div class="col-sm-9"></div>
								</div>

								<div class="clearfix"></div>

								<div class="form-group">
									<div class="col-sm-6 product_free_shipping">
										<div class="checkbox">
											<input type="hidden" name="product_shipping[free_shipping]" value="0"/>
											<input type="checkbox" name="product_shipping[free_shipping]" value="1" <?php echo (isset($product['shipping']['free_shipping']) && $product['shipping']['free_shipping']) ? 'checked="checked"' : '' ;?>/>
										</div>
										<div class="inline">
											<span><?php echo $ms_account_product_shipping_free; ?></span>
											<p class="inline ms-note"><?php echo $ms_account_product_shipping_free_note; ?></p>
										</div>
									</div>
									<div class="col-sm-6"></div>
								</div>

								<div class="clearfix"></div>

								<div class="panel panel-default shipping-locations <?php echo (isset($product['shipping']['free_shipping']) && $product['shipping']['free_shipping']) ? 'hidden' : ''; ?>">
									<div class="panel-heading"><?php echo $ms_account_product_shipping_locations_to; ?></div>
									<table class="table table-borderless table-hover">
										<thead>
											<tr>
												<td><?php echo $ms_account_product_shipping_locations_destination; ?></td>
												<td><?php echo $ms_account_product_shipping_locations_company; ?></td>
												<td><?php echo $ms_account_product_shipping_locations_delivery_time; ?></td>
												<td><?php echo $ms_account_product_shipping_locations_cost; ?></td>
												<td><?php echo $ms_account_product_shipping_locations_additional_cost; ?></td>
												<td></td>
											</tr>
										</thead>

										<tbody>
											<tr class="ffSample">
												<td>
													<input type="hidden" class="country-id" name="product_shipping[locations][0][to_geo_zone][id]" value="0"/>
													<input type="text" class="form-control inline ac-shipping-countries" name="product_shipping[locations][0][to_geo_zone][name]" value=""/>
												</td>
												<td>
													<input type="hidden" class="method-id" name="product_shipping[locations][0][method][id]" value="0">
													<input type="text" class="form-control inline ac-shipping-companies" name="product_shipping[locations][0][method][name]">
												</td>
												<td>
													<select class="form-control inline" name="product_shipping[locations][0][delivery_time]">
														<?php foreach($shipping_delivery_times as $delivery_time_id => $delivery_time_desc) { ?>
															<option value="<?php echo $delivery_time_id ;?>"><?php echo $delivery_time_desc[$this->config->get('config_language_id')] ;?></option>
														<?php } ?>
													</select>
												</td>
												<td>
													<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
													<input type="text" class="form-control inline small mm_price" name="product_shipping[locations][0][cost]" value="" />
													<span class="mm_currency"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
												</td>
												<td>
													<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
													<input type="text" class="form-control inline small mm_price" name="product_shipping[locations][0][additional_cost]" value="" />
													<span class="mm_currency"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
												</td>
												<td><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
											</tr>

											<?php if (isset($product['shipping']['locations'])) { ?>
												<?php $shipping_location_row = 1; ?>
												<?php foreach ($product['shipping']['locations'] as $location) { ?>
													<tr>
														<input type="hidden" name="product_shipping[locations][<?php echo $shipping_location_row ;?>][product_shipping_location_id]" value="<?php echo $location['mspl.location_id'] ;?>" />

														<td>
															<input type="hidden" class="country-id" name="product_shipping[locations][<?php echo $shipping_location_row ;?>][to_geo_zone][id]" value="<?php echo $location['to_geo_zone_id'] ;?>"/>
															<input type="text" class="form-control inline ac-shipping-countries" name="product_shipping[locations][<?php echo $shipping_location_row ;?>][to_geo_zone][name]" value="<?php echo $location['to_geo_zone_name'] ;?>"/>
														</td>
														<td>
															<input type="hidden" class="method-id" name="product_shipping[locations][<?php echo $shipping_location_row ;?>][method][id]" value="<?php echo $location['shipping_method_id'] ;?>">
															<input type="text" class="form-control inline ac-shipping-companies" name="product_shipping[locations][<?php echo $shipping_location_row ;?>][method][name]" value="<?php echo $location['shipping_method_name'] ;?>">
														</td>
														<td>
															<select class="form-control inline" name="product_shipping[locations][<?php echo $shipping_location_row ;?>][delivery_time]">
																<?php foreach($shipping_delivery_times as $delivery_time_id => $delivery_time_desc) { ?>
																	<option value="<?php echo $delivery_time_id ;?>" <?php echo ($delivery_time_id == $location['delivery_time_id']) ? 'selected="selected"' : ''; ?>><?php echo $delivery_time_desc[$this->config->get('config_language_id')] ;?></option>
																<?php } ?>
															</select>
														</td>
														<td>
															<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
															<input type="text" class="form-control inline small mm_price" name="product_shipping[locations][<?php echo $shipping_location_row ;?>][cost]" value="<?php echo $location['cost'] ;?>" />
														</td>
														<td>
															<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
															<input type="text" class="form-control inline small mm_price" name="product_shipping[locations][<?php echo $shipping_location_row ;?>][additional_cost]" value="<?php echo $location['additional_cost'] ;?>" />
														</td>
														<td><a class="icon-remove mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
													</tr>
												<?php $shipping_location_row++; ?>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>

									<div class="buttons">
										<label class="mm_label col-sm-2">
											<a class="btn btn-default ffClone"><?php echo $ms_account_product_shipping_locations_add_btn; ?></a>
										</label>
										<div class="col-sm-10"></div>
									</div>
								</div>
							</div>
						</fieldset>
					<?php } ?>

					<!-- images -->
					<?php if (in_array('images', $this->config->get('msconf_product_included_fields'))) { ?>
						<fieldset>
							<legend><?php echo $ms_account_product_image; ?></legend>
							<div class="alert alert-danger" style="display: none;"></div>
							<div class="dragndrop" id="ms-image-dragndrop">
								<p class="mm_drophere"><?php echo $ms_drag_drop_here; ?></p>
								<p class="mm_or"><?php echo $ms_or; ?></p>
								<a class="btn btn-default" href="#" id="ms-image"><span><?php echo $ms_select_files; ?></span></a>
								<p class="mm_allowed"><?php echo $ms_account_product_image_note; ?></p>
							</div>
							<?php if(isset($product['images']) && !empty($product['images'])) :?>
								<div class="ms-image" id="image-holder">
									<?php foreach($product['images'] as $image) :?>
										<div class="image-holder">
											<input type="hidden" name="product_images[]" value="<?php echo $image['name'] ;?>"/>
											<img src="<?php echo $image['thumb'] ;?>"/>
											<span class="ms-remove"><i class="fa fa-times"></i></span>
										</div>
									<?php endforeach ;?>
								</div>
							<?php else :?>
								<div class="ms-image hidden" id="image-holder">
								</div>
							<?php endif ;?>
							<div class="ms-progress progress"></div>
						</fieldset>
					<?php } ?>

					<?php if (in_array('files', $this->config->get('msconf_product_included_fields'))) { ?>
						<fieldset>
							<legend><?php echo $ms_account_product_files; ?></legend>
							<div class="alert alert-danger" style="display: none;"></div>
							<div class="dragndrop" id="ms-file-dragndrop">
								<p class="mm_drophere"><?php echo $ms_drag_drop_here; ?></p>
								<p class="mm_or"><?php echo $ms_or; ?></p>
								<a class="btn btn-default" href="#" id="ms-file"><span><?php echo $ms_select_files; ?></span></a>
								<p class="mm_allowed"><?php echo $ms_account_product_download_note; ?></p>
							</div>
							<?php if(isset($product['downloads']) && !empty($product['downloads'])) :?>
								<div class="ms-image" id="file-holder">
									<?php foreach($product['downloads'] as $download) :?>
										<div class="file-holder">
											<i class="fa fa-file"></i>
											<input type="hidden" name="product_downloads[][filename]" value="<?php echo $download['src'] ;?>">
												<span class="ms-remove">
													<i class="fa fa-times"></i>
												</span>
										<span class="file-name">
											<?php echo $download['name'] ;?>
										</span>
										</div>
									<?php endforeach ;?>
								</div>
							<?php else :?>
								<div class="ms-image hidden" id="file-holder"></div>
							<?php endif ;?>
							<div class="ms-progress progress"></div>
						</fieldset>
					<?php } ?>
				</form>

				<div class="buttons">
					<div class="pull-left"><a href="<?php echo $back; ?>"><span><?php echo $ms_button_cancel; ?></span></a></div>
					<?php if ($seller['ms.seller_status'] != MsSeller::STATUS_DISABLED && $seller['ms.seller_status'] != MsSeller::STATUS_DELETED && $seller['ms.seller_status'] != MsSeller::STATUS_INCOMPLETE) { ?>
						<div class="pull-right"><a class="btn btn-primary ms-spinner" id="ms-submit-button"><span><?php echo $ms_button_submit; ?></span></a></div>
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
		product_id: '<?php echo $product['product_id']; ?>',
		text_delete: '<?php echo htmlspecialchars($ms_delete, ENT_QUOTES, "UTF-8"); ?>',
		text_none: '<?php echo htmlspecialchars($ms_none, ENT_QUOTES, "UTF-8"); ?>',
		uploadError: '<?php echo htmlspecialchars($ms_error_file_upload_error, ENT_QUOTES, "UTF-8"); ?>',
		formError: '<?php echo htmlspecialchars($ms_error_form_submit_error, ENT_QUOTES, "UTF-8"); ?>',
		formNotice: '<?php echo htmlspecialchars($ms_error_form_notice, ENT_QUOTES, "UTF-8"); ?>',
		config_enable_rte: '<?php echo $this->config->get('msconf_enable_rte'); ?>',
		config_enable_quantities: '<?php echo $this->config->get('msconf_enable_quantities'); ?>',
		image_limit_max: '<?php echo $msconf_images_limits[1] ;?>',
		image_limit_min: '<?php echo $msconf_images_limits[0] ;?>',
		file_limit_min: '<?php echo $msconf_downloads_limits[0] ;?>',
		file_limit_max: '<?php echo $msconf_downloads_limits[1] ;?>',
		fee_priority: '<?php echo $this->config->get('msconf_fee_priority'); ?>',
		category_based_fee: '<?php echo $this->language->get('ms_account_product_listing_category'); ?>'
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
