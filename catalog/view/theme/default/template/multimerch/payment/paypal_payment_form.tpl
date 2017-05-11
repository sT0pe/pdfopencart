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
	<div>
		<fieldset>
			<legend><?php echo $text_payment_form_title; ?></legend>
			<?php if(isset($receiver)) { ?>
				<table class="table table-bordered">
					<thead>
						<tr>
							<td class="center"><?php echo $ms_from; ?></a></td>
							<td class="center"><?php echo $ms_to; ?></td>
						</tr>
					</thead>

					<tbody>
						<tr>
							<input type="hidden" name="sender_data[pp_address]" value="<?php echo $sender['pp_address']; ?>" />
							<td class="center"><?php echo $sender['pp_address']; ?></td>

							<input type="hidden" name="receiver_data[pp_address]" value="<?php echo $receiver['pp_address']; ?>" />
							<td class="center"><?php echo $receiver['pp_address']; ?></td>
						</tr>
					</tbody>
				</table>
			<?php } ?>
		</fieldset>
	</div>

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
				url: 'index.php?route=multimerch/payment/paypal/jxSavePayment',
				type: 'post',
				data: data,
				dataType: 'json',
				beforeSend: function () {
					$('#button-save').button('loading');
				},
				success: function (json) {
					if(json['errors']) {
						$('#button-save').button('reset');

						var html = '';
						html += '<div class="alert alert-danger">';
						html += '<ul style="list-style: none;">';
						$.map(json['errors'], function(item) {
							html += '<li>'+ item + '</li>';
						});
						html += '</ul>';
						html += '</div>';

						$('.methods-info').append(html);
					} else if (json.success) {
						if(json.pp_form) {
							// Submit PayPal Standard form
							$('<div style="display:none">' + json.pp_form + '</div>').appendTo('body').find('form#paypal_standard_form').submit();
						} else {
							$('#button-save').button('reset');

							window.location.href = $('base').attr('href') + 'index.php?route=seller/account-payment';
						}
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});
	});
</script>


