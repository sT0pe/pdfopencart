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

<?php if(isset($sellers)) { ?>
    <div>
        <fieldset class="receiver_info">
            <legend><?php echo $text_dialog_confirm; ?></legend>
            <div class="alert alert-info">
                <ul>
                    <li><?php echo $text_method_one_receiver; ?></li>
                    <li><?php echo $text_method_mutli_receiver; ?></li>
                </ul>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td class="center"><?php echo $ms_seller; ?></a></td>
                    <td class="center"><?php echo $ms_paypal; ?></a></td>
                    <td class="center"><?php echo $ms_amount; ?></td>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($sellers as $seller_id => $seller_data) { ?>
                    <tr>
                        <input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][request_id]" value="<?php echo $seller_data['request_id']; ?>" />
                        <input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][amount]" value="<?php echo $seller_data['amount']; ?>" />
                        <input type="hidden" name="receiver_data[<?php echo $seller_id; ?>][pp_address]" value="<?php echo $seller_data['pp_address']; ?>" />

                        <td class="center"><?php echo $seller_data['ms.nickname']; ?></td>
                        <td class="center"><?php echo $seller_data['pp_address']; ?></td>
                        <td class="center"><?php echo $seller_data['amount_formatted']; ?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td class="text-right" colspan="2"><?php echo $ms_total; ?></td>
                    <td class="center"><?php echo $total_amount_formatted; ?> <?php echo $text_dialog_ppfee; ?></td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </div>

    <div class="buttons">
        <div class="pull-right">
            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>" />
            <input type="hidden" name="payment_description" value="<?php echo $payment_description; ?>" />

            <button id="button-save" data-toggle="tooltip" title="<?php echo $ms_button_pay; ?>" class="btn btn-primary"><i class="fa fa-money"></i> <?php echo $ms_button_pay; ?></button>
        </div>
    </div>
<?php } ?>

<script>
    $(function() {
        $("#button-save").click(function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            var data = $(form).serialize();

            $.ajax({
				type: "POST",
				dataType: "json",
				url: 'index.php?route=multimerch/payment/paypal/jxCompletePayment&token=' + msGlobals.token,
				data: data,
                beforeSend: function() {
                    $('#button-save').button('loading');
                    $('.alert-danger').addClass('hidden').find('span').remove();
                },
				success: function(jsonData) {
                    if (!jQuery.isEmptyObject(jsonData.errors)) {
                        $('#button-save').button('reset');

                        for (i in jsonData.errors) {
							$('.methods-info .alert-danger').show().removeClass('hidden').text(' ' + jsonData.errors[i]);
						}
					} else if (jsonData.success) {
                        if(jsonData.pp_form) {
                            $('<div style="display:none">' + jsonData.pp_form + '</div>').appendTo('body').find('form#paypal_standard_form').submit();
                        } else {
                            $('#button-save').button('reset');

                            window.location = 'index.php?route=multimerch/payment&token=' + msGlobals.token;
                        }
					}
				}
			});
        });
    });
</script>
