<?php if(isset($errors)) { ?>
    <div class="alert alert-danger" style="position: relative;">
		<ul style="list-style: none;">
			<?php foreach($errors as $error) { ?>
				<li><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?></li>
			<?php } ?>
		</ul>
		<button type="button" class="close" data-dismiss="alert" style="position:absolute; top: 1px; right: 5px;">&times;</button>
	</div>
<?php } else { ?>
    <div class="global_info">
        <div class="alert alert-info">
            <?php echo $text_global_info; ?>
        </div>
    </div>

	<fieldset class="receiver">
		<legend><?php echo $text_receiver; ?></legend>
		<?php if(isset($admin_info)) { ?>
			<div class="form-group">
				<input type="hidden" name="receiver_data[name]" value="<?php echo $admin_info['full_name']; ?>" />
				<label class="col-sm-3 control-label"><?php echo $ms_name; ?></label>
				<div class="col-sm-9">
					<p><?php echo $admin_info['full_name']; ?></p>
				</div>
			</div>
			<div class="form-group">
				<input type="hidden" name="receiver_data[bank]" value="<?php echo $admin_info['full_bank_name']; ?>" />
				<label class="col-sm-3 control-label"><?php echo $text_bank_name; ?></label>
				<div class="col-sm-9">
					<p><?php echo $admin_info['full_bank_name']; ?></p>
				</div>
			</div>
			<div class="form-group">
				<input type="hidden" name="receiver_data[bic]" value="<?php echo $admin_info['bic']; ?>" />
				<label class="col-sm-3 control-label"><?php echo $text_bic; ?></label>
				<div class="col-sm-9">
					<p><?php echo $admin_info['bic']; ?></p>
				</div>
			</div>
			<div class="form-group">
				<input type="hidden" name="receiver_data[iban]" value="<?php echo $admin_info['iban']; ?>" />
				<label class="col-sm-3 control-label"><?php echo $text_iban; ?></label>
				<div class="col-sm-9">
					<p><?php echo $admin_info['iban']; ?></p>
				</div>
			</div>
		<?php } ?>
	</fieldset>

	<fieldset class="sender">
		<legend>
			<?php echo $text_sender; ?>
			<span style="font-size: 12px; margin-left: 10px;"><a href="<?php echo $this->url->link('seller/account-setting', '', 'SSL'); ?>"><?php echo $ms_edit; ?></a></span>
		</legend>

		<?php if(isset($seller_info)) { ?>
			<div class="form-group">
				<input type="hidden" name="sender_data[name]" value="<?php echo $seller_info['full_name']; ?>" />
				<label class="col-sm-3 control-label"><?php echo $ms_name; ?></label>
				<div class="col-sm-9">
					<p><?php echo $seller_info['full_name']; ?></p>
				</div>
			</div>
			<div class="form-group">
				<input type="hidden" name="sender_data[bank]" value="<?php echo $seller_info['full_bank_name']; ?>" />
				<label class="col-sm-3 control-label"><?php echo $text_bank_name; ?></label>
				<div class="col-sm-9">
					<p><?php echo $seller_info['full_bank_name']; ?></p>
				</div>
			</div>
			<div class="form-group">
				<input type="hidden" name="sender_data[bic]" value="<?php echo $seller_info['bic']; ?>" />
				<label class="col-sm-3 control-label"><?php echo $text_bic; ?></label>
				<div class="col-sm-9">
					<p><?php echo $seller_info['bic']; ?></p>
				</div>
			</div>
			<div class="form-group">
				<input type="hidden" name="sender_data[iban]" value="<?php echo $seller_info['iban']; ?>" />
				<label class="col-sm-3 control-label"><?php echo $text_iban; ?></label>
				<div class="col-sm-9">
					<p><?php echo $seller_info['iban']; ?></p>
				</div>
			</div>
		<?php } ?>
	</fieldset>

	<div class="buttons">
		<div class="pull-right">
			<button id="button-save" data-toggle="tooltip" title="<?php echo $ms_button_submit; ?>" class="btn btn-primary"><i class="fa fa-money"></i> <?php echo $ms_button_submit; ?></button>
		</div>
	</div>
<?php } ?>

<script>
	$(function() {
		$('#button-save').click(function(e) {
			e.preventDefault();
			var data = $(this).closest('form').serialize();

			$.ajax({
				url: 'index.php?route=multimerch/payment/bank_transfer/jxSavePayment',
				type: 'post',
				data: data,
				dataType: 'json',
				success: function(json) {
					if(!json['errors']) {
						window.location.href = $('base').attr('href') + 'index.php?route=seller/account-payment';
					} else {
						var html = '';
						html += '<div class="alert alert-danger">';
						html += '<ul style="list-style: none;">';										// todo
						$.map(json['errors'], function(item) {
							html += '<li>'+ item + '</li>';
						});
						html += '</ul>';
						html += '</div>';

						$('.methods-info').append(html);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});
	});
</script>


