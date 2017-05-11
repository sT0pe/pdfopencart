<div class="alert alert-danger <?php echo isset($errors) ? '' : 'hidden'; ?>" style="position: relative;">
	<?php if(isset($errors)) { ?>
		<ul>
			<?php foreach($errors as $error) { ?>
				<li><?php echo $error; ?></li>
			<?php } ?>
		</ul>
	<?php } ?>
	<button type="button" class="close" data-dismiss="alert" style="position:absolute; top: 1px; right: 5px;">&times;</button>
</div>
<?php if(isset($admin)) { ?>
	<div class="sender">
		<legend><?php echo $text_sender; ?></legend>
		<div class="card_info">
			<div class="row">
				<input type="hidden" name="sender_data[name]" value="<?php echo $admin['full_name']; ?>" />
				<div class="col-sm-3">
					<strong><?php echo $ms_name; ?>: </strong>
				</div>
				<div class="col-sm-9">
					<p><?php echo $admin['full_name']; ?></p>
				</div>
			</div>
			<div class="row">
				<input type="hidden" name="sender_data[bank_name]" value="<?php echo $admin['full_bank_name']; ?>" />
				<div class="col-sm-3">
					<strong><?php echo $text_bank_name; ?>: </strong>
				</div>
				<div class="col-sm-9">
					<p><?php echo $admin['full_bank_name']; ?></p>
				</div>
			</div>
			<div class="row">
				<input type="hidden" name="sender_data[bic]" value="<?php echo $admin['bic']; ?>" />
				<div class="col-sm-3">
					<strong><?php echo $text_bic; ?>: </strong>
				</div>
				<div class="col-sm-9">
					<p><?php echo $admin['bic']; ?></p>
				</div>
			</div>
			<div class="row">
				<input type="hidden" name="sender_data[iban]" value="<?php echo $admin['iban']; ?>" />
				<div class="col-sm-3">
					<strong><?php echo $text_iban; ?>: </strong>
				</div>
				<div class="col-sm-9">
					<p><?php echo $admin['iban']; ?></p>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php if(isset($sellers) && is_array($sellers)) { ?>
	<div class="receiver">
		<legend><?php echo $text_receiver; ?></legend>
		<?php foreach($sellers as $seller_id => $seller_data) { ?>
			<div class="card_info">
				<input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][request_id]" value="<?php echo $seller_data['request_id']; ?>" />
				<input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][amount]" value="<?php echo $seller_data['amount']; ?>" />

				<h4><?php echo $seller_data['nickname']; ?>: <?php echo $seller_data['amount_formatted']; ?></h4>
				<div class="row">
					<input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][name]" value="<?php echo $seller_data['full_name']; ?>" />
					<div class="col-sm-3">
						<strong><?php echo $ms_name; ?>: </strong>
					</div>
					<div class="col-sm-9">
						<p><?php echo $seller_data['full_name']; ?></p>
					</div>
				</div>
				<div class="row">
					<input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][bank_name]" value="<?php echo $seller_data['full_bank_name']; ?>" />
					<div class="col-sm-3">
						<strong><?php echo $text_bank_name; ?>: </strong>
					</div>
					<div class="col-sm-9">
						<p><?php echo $seller_data['full_bank_name']; ?></p>
					</div>
				</div>
				<div class="row">
					<input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][bic]" value="<?php echo $seller_data['bic']; ?>" />
					<div class="col-sm-3">
						<strong><?php echo $text_bic; ?>: </strong>
					</div>
					<div class="col-sm-9">
						<p><?php echo $seller_data['bic']; ?></p>
					</div>
				</div>
				<div class="row">
					<input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][iban]" value="<?php echo $seller_data['iban']; ?>" />
					<div class="col-sm-3">
						<strong><?php echo $text_iban; ?>: </strong>
					</div>
					<div class="col-sm-9">
						<p><?php echo $seller_data['iban']; ?></p>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } ?>

<div class="buttons">
	<div class="pull-right">
		<p><strong><?php echo 'Total: ' . $total_amount_formatted; ?></strong></p>
		<input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>" />
		<input type="hidden" name="payment_description" value="<?php echo $payment_description; ?>" />

		<button id="button-save" data-toggle="tooltip" title="<?php echo $ms_button_pay; ?>" class="btn btn-primary"><i class="fa fa-money"></i> <?php echo $ms_button_pay; ?></button>
	</div>
</div>

<script>
	$(function() {
		$("#button-save").click(function(e) {
			e.preventDefault();

			var data = $(this).closest('form').serialize();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: 'index.php?route=multimerch/payment/bank_transfer/jxSave&token=' + msGlobals.token,
				data: data,
				success: function(jsonData) {
					if (!jQuery.isEmptyObject(jsonData.errors)) {
						for (i in jsonData.errors) {
							$('.methods-info .alert-danger').show().removeClass('hidden').text(' ' + jsonData.errors[i]);
						}
					} else {
						window.location = 'index.php?route=multimerch/payment&token=' + msGlobals.token;
					}
				}
			});
		});
	});
</script>