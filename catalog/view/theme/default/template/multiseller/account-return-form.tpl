<!-- //todo Tpl is not used. Rewrite the whole markup. MM 8.2 layout fixes (right_column etc.) were not applied. -->

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
		<fieldset id="content" class="<?php echo $class; ?> ms-account-dashboard"><?php echo $content_top; ?>
			<fieldset class="mm_dashboard">
				<h1><?php echo $heading; ?></h1>
				<div class="alert alert-danger fade in hidden" id="error-holder"></div>

				<form id="ms-new-return" class="tab-content ms-return">
					<!-- Customer info -->
					<fieldset id="mm_general">
						<legend><?php echo $ms_account_return_customer_info; ?></legend>

						<!-- Order id -->
						<div class="form-group required">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_return_order_id; ?></label>
							<div class="col-sm-10">
								<input type="text" class="form-control " name="order_id" id="return_order_id" value="0" />
								<!--<p class="ms-note"><?php echo $ms_return_order_id_note; ?></p>-->
							</div>
						</div>

						<!-- Customer name -->
						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_description; ?></label>
							<div class="col-sm-10">
								<p class="" id="return_customer_name"></p>
							</div>
						</div>

						<!-- Order shipping address -->
						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_description; ?></label>
							<div class="col-sm-10">
								<p class="" id="return_customer_shipping_address"></p>
							</div>
						</div>

						<!-- Customer email -->
						<div class="form-group">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_description; ?></label>
							<div class="col-sm-10">
								<p class="" id="return_customer_email"></p>
							</div>
						</div>
					</fieldset>

					<!-- Return info -->
					<fieldset>
						<legend><?php echo $ms_categories ;?></legend> <!-- Change name -->

						<!-- Select items from order -->
						<div class="form-group required">
							<label class="mm_label col-sm-2 mm_req"><?php echo $ms_account_product_category; ?></label>
							<div class="col-sm-10" id="product_category_block">
								<div class="row category-holder">
									<div class="form-group required category">
										<div class="col-sm-12">
											<select class="form-control category-select" name="product_category[]">
												<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
												<?php foreach($categories as $category) :?>
													<?php if($category['parent_id'] == 0) :?>
														<option value="<?php echo $category['category_id'] ;?>"><?php echo $category['name'] ;?></option>
													<?php endif ;?>
												<?php endforeach ;?>
											</select>
										</div>
									</div>
									<?php if ($msconf_allow_multiple_categories) :?>
										<div class="category-actions pull-right">
											<a href="#" class="remove">
												<i class="fa fa-times" aria-hidden="true"></i>
											</a>
										</div>
									<?php endif ;?>
								</div>
								<?php if ($msconf_allow_multiple_categories) :?>
									<a href="#" class="add-category">
										<i class="fa fa-plus" aria-hidden="true"></i>
									</a>
								<?php endif ;?>
								<p class="ms-note"><?php echo $ms_account_product_category_note; ?></p>
							</div>
						</div>

						<!-- ??? Status:Opened or not? Need to add to db? -->

						<!-- Return reason -->
						<div class="form-group required category" id="cloneSelect">
							<div class="col-sm-12">
								<select class="form-control category-select" name="product_category[]">
									<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
								</select>
							</div>
						</div>

						<!-- Return action -->
						<div class="form-group required category" id="cloneSelect">
							<div class="col-sm-12">
								<select class="form-control category-select" name="product_category[]">
									<option disabled="disabled" selected="selected"><?php echo $ms_account_product_category_select; ?></option>
								</select>
							</div>
						</div>
					</fieldset>
				</form>

				<div class="buttons">
					<div class="pull-left"><a href="<?php echo $back; ?>"><span><?php echo $ms_button_cancel; ?></span></a></div>
					<?php if ($seller['ms.seller_status'] != MsSeller::STATUS_DISABLED && $seller['ms.seller_status'] != MsSeller::STATUS_DELETED && $seller['ms.seller_status'] != MsSeller::STATUS_INCOMPLETE) { ?>
					<div class="pull-right"><a class="btn btn-primary" id="ms-submit-button"><span><?php echo $ms_button_submit; ?></span></a></div>
					<?php } ?>
				</div>
				<?php echo $content_bottom; ?></div>
			<?php echo $column_right; ?></div>
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
			file_limit_max: '<?php echo $msconf_downloads_limits[1] ;?>'
		};
	</script>

	<!-- Get order info -->
	<script>
		$(document).on('input', '#return_order_id', function(){
			console.log(123);
			$.ajax({
				url: 'index.php?route=account/account-return/jxOrderInfo',
				type: 'POST',
				dataType: 'json',
				data: $('#return_order_id').val(),
				beforeSend: function () {
					// create loader next to order_id input field
				},
				success: function (data) {
					if(data) {
						$.each(data, function(k, v) {
							if($(document).find('#' + k + '').length > 0) {
								$(this).html(v);
							}
						})
					}
				}
			});
		});
	</script>
</div>
<?php echo $footer; ?>
