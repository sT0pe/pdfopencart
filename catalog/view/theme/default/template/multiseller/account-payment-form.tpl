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

				<form id="form" class="tab-content ms-pg-payment-form">
					<input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>" />
					<input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>" />
					<input type="hidden" name="payment_description" value="<?php echo $payment_description; ?>" />

					<div class="col-sm-4">
						<fieldset>
							<legend><?php echo $ms_pg_payment_requests; ?></legend>
							<div class="well well-sm payment-requests">
								<ul class="pull-left">
									<?php foreach($payment_requests as $payment_request) { ?>
										<li>
											<input type="hidden" name="payment_requests[]" value="<?php echo $payment_request['request_id']; ?>" />
											<?php echo $payment_request['description']; ?>
										</li>
									<?php } ?>
								</ul>
							</div>
						</fieldset>
					</div>
					<div class="col-sm-8">
						<fieldset>
							<legend><?php echo $ms_pg_payment_method; ?></legend>
							<div class="payment-methods">
								<div class="row">
									<?php if (isset($error_payment_methods) && $error_payment_methods) { ?>
										<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_payment_methods; ?></div>
										<div class="pull-right">
											<a href="<?php echo $action_back; ?>" class="btn btn-default"><?php echo $button_back; ?></a>
										</div>
									<?php } else { ?>
										<div class="col-sm-12 methods-list">
											<strong><?php echo $ms_pg_payment_form_select_method; ?></strong>
											<?php foreach($payment_methods as $payment_method) { ?>
												<div class="radio">
													<label>
														<input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" <?php echo ($payment_method === reset($payment_methods)) ? 'checked="checked"' : ''; ?> />
														<?php echo $payment_method['name']; ?>
													</label>
												</div>
											<?php } ?>
										</div>
										<div class="col-sm-12 methods-info">

										</div>
									<?php } ?>
								</div>
							</div>
						</fieldset>
					</div>
				</form>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>
