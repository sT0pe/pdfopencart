<?php if ($error_no_shipping_methods) { ?>
	<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <?php echo $error_no_shipping_methods; ?>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>
<?php } ?>

<div class="delivery-details">
	<div class="title">
		<span><?php echo $mm_checkout_shipping_delivery_details_title; ?></span>
		<span><a href="#collapse-shipping-address" data-toggle="collapse" data-parent="#accordion"><?php echo $mm_checkout_shipping_delivery_details_change; ?></a></span>
	</div>
	<div class="col-sm-4">
		<div>
			<?php echo $shipping_details['customer_name']; ?>
		</div>
		<div>
			<?php echo $shipping_details['customer_address']; ?>
		</div>
		<div>
			<?php echo $shipping_details['city']; ?>
		</div>
		<div>
			<?php echo $shipping_details['country']; ?>
		</div>
		<div>
			<?php echo $shipping_details['postcode']; ?>
		</div>
	</div>
	<div class="col-sm-8"></div>
</div>

<div class="clearfix"></div>

<?php if(!empty($cart_products)) { ?>
	<div class="shm-title">
		<span><?php echo $mm_checkout_shipping_products_title; ?></span>
	</div>
	<div class="cart-products table-responsive">
		<table class="table">
			<?php foreach($cart_products as $seller_id => $shipping_rules) { ?>
				<tbody>
					<tr>
						<td colspan="4">
							<?php if(MsLoader::getInstance()->MsSeller->getSellerNickname($seller_id)) { ?>
								<h3><?php echo $ms_seller;?> <?php echo MsLoader::getInstance()->MsSeller->getSellerNickname($seller_id); ?></h3>
							<?php } else { ?>
								<h3><?php echo $ms_store_owner;?></h3>
							<?php } ?>
						</td>
					</tr>

					<?php if(isset($shipping_rules['fixed_shipping']) && !empty($shipping_rules['fixed_shipping'])) { ?>
                        <!-- Products having fixed shipping rate -->

                        <?php foreach($shipping_rules['fixed_shipping'] as $product) { ?>
							<tr>
                                <td class="col-sm-1"><img src="<?php echo $product['image']; ?>"></td>
								<td class="col-sm-4">
									<div class="product-info">
										<div>
											<strong><?php echo $product['name']; ?></strong>
										</div>
										<div>
											<span><?php echo $mm_checkout_shipping_products_price; ?></span>
											<?php echo $product['price_formatted']; ?>
										</div>
										<div>
											<span><?php echo $mm_checkout_shipping_products_quantity; ?></span>
											<span><?php echo $product['quantity']; ?></span>
										</div>
									</div>
								</td>
								<td class="col-sm-4 sm-select">
                                    <div class="shipping-method-select">
                                        <?php if($product['shipping_methods']['free_shipping'] || !$product['shipping_required']) { ?>
                                            <strong><?php echo !$product['shipping_required'] ? $mm_checkout_shipping_not_required : $mm_checkout_shipping_method_free; ?></strong>

                                            <input type="hidden" name="free_shipping[<?php echo $product['product_id']; ?>]" value="1" />
                                        <?php } else if(!empty($product['shipping_methods']) && !empty($product['shipping_methods']['locations'])) { ?>
                                            <strong><?php echo $mm_checkout_shipping_method_title; ?></strong>

                                            <?php foreach($product['shipping_methods']['locations'] as $location) { ?>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="fixed_shipping_method[<?php echo $product['product_id']; ?>]" value="<?php echo $location['mspl.location_id']; ?>" <?php echo ($location === reset($product['shipping_methods']['locations'])) ? 'checked="checked"' : ''; ?> />
                                                        <?php echo $location['shipping_method_name']; ?> (<?php echo $location['delivery_time_name']; ?>)
                                                    </label>
                                                    <span class="inline cost"><?php echo $location['total_cost_formatted']; ?></span>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <strong><?php echo $mm_checkout_shipping_not_available; ?></strong>

                                            <input type="hidden" name="no_shipping_method[<?php echo $product['product_id']; ?>]" value="0" />
                                        <?php } ?>
                                    </div>
								</td>
								<td class="col-sm-3"></td>
							</tr>
						<?php } ?>
					<?php } ?>

                    <?php if(isset($shipping_rules['combined_shipping']) && !empty($shipping_rules['combined_shipping'])) { ?>
                        <!-- Products on which combined shipping is applied -->

                        <?php foreach($shipping_rules['combined_shipping'] as $product) { ?>
                            <tr>
                                <td class="col-sm-1"><img src="<?php echo $product['image']; ?>"></td>
                                <td class="col-sm-4">
                                    <div class="product-info">
                                        <div>
                                            <strong><?php echo $product['name']; ?></strong>
                                        </div>
                                        <div>
                                            <span><?php echo $mm_checkout_shipping_products_price; ?></span>
                                            <?php echo $product['price_formatted']; ?>
                                        </div>
                                        <div>
                                            <span><?php echo $mm_checkout_shipping_products_quantity; ?></span>
                                            <span><?php echo $product['quantity']; ?></span>
                                        </div>
                                    </div>
                                </td>

                                <?php if($product === reset($shipping_rules['combined_shipping'])) { ?>
                                    <td class="col-sm-4 sm-select" rowspan="<?php echo count($shipping_rules['combined_shipping']); ?>">
                                        <div class="shipping-method-select">
                                            <?php if(isset($shipping_rules['seller_combined_shipping_methods'])) { ?>
                                                <strong><?php echo $mm_checkout_shipping_method_title; ?></strong>

                                                <?php foreach($shipping_rules['seller_combined_shipping_methods'] as $method) { ?>
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="combined_shipping_method[<?php echo $seller_id; ?>]" value="<?php echo $method['seller_shipping_location_id'] . '-' . $shipping_rules['combined_product_ids']; ?>" <?php echo $method === reset($shipping_rules['seller_combined_shipping_methods']) ? 'checked="checked"' : ''; ?> />
                                                            <?php echo $method['shipping_method_name']; ?> (<?php echo $method['delivery_time_name']; ?>)
                                                        </label>
                                                        <span class="inline cost"><?php echo $method['total_cost_formatted']; ?></span>
                                                    </div>
                                                <?php } ?>
                                            <?php } else if(isset($shipping_rules['combined_products_maxweight_exceeded'])) { ?>
                                                <strong><?php echo $shipping_rules['combined_products_maxweight_exceeded']; ?></strong>
                                            <?php } else { ?>
                                                <strong><?php echo $mm_checkout_shipping_not_available; ?></strong>

                                                <input type="hidden" name="no_shipping_method[<?php echo $product['product_id']; ?>]" value="0" />
                                            <?php } ?>
                                        </div>
                                    </td>
                                <?php } ?>

                                <td class="col-sm-3"></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>

                    <?php if(isset($shipping_rules['digital']) && !empty($shipping_rules['digital'])) { ?>
                        <!-- Products that don't require shipping (digital products) -->

                        <?php foreach($shipping_rules['digital'] as $product) { ?>
                            <tr>
                                <td class="col-sm-1">
                                    <img src="<?php echo $product['image']; ?>">
                                </td>
                                <td class="col-sm-4">
                                    <div class="product-info">
                                        <div>
                                            <strong><?php echo $product['name']; ?></strong>
                                        </div>
                                        <div>
                                            <span><?php echo $mm_checkout_shipping_products_price; ?></span>
                                            <?php echo $product['price_formatted']; ?>
                                        </div>
                                        <div>
                                            <span><?php echo $mm_checkout_shipping_products_quantity; ?></span>
                                            <span><?php echo $product['quantity']; ?></span>
                                        </div>
                                    </div>
                                </td>

                                <td class="col-sm-4 sm-select">
                                    <div class="shipping-method-select">
                                        <strong><?php echo $mm_checkout_shipping_digital_products; ?></strong>
                                        <input type="hidden" name="digital[<?php echo $product['product_id']; ?>]" value="1" />
                                    </div>
                                </td>

                                <td class="col-sm-3"></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>

                    <!-- Product not available for shipping -->
                    <?php if(isset($shipping_rules['no_shipping']) && !empty($shipping_rules['no_shipping'])) { ?>
                        <?php foreach($shipping_rules['no_shipping'] as $product) { ?>
                            <tr>
                                <td class="col-sm-1">
                                    <img src="<?php echo $product['image']; ?>">
                                </td>
                                <td class="col-sm-4">
                                    <div class="product-info">
                                        <div>
                                            <strong><?php echo $product['name']; ?></strong>
                                        </div>
                                        <div>
                                            <span><?php echo $mm_checkout_shipping_products_price; ?></span>
                                            <?php echo $product['price_formatted']; ?>
                                        </div>
                                        <div>
                                            <span><?php echo $mm_checkout_shipping_products_quantity; ?></span>
                                            <span><?php echo $product['quantity']; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-sm-4 sm-select">
                                    <div class="shipping-method-select">
                                        <input type="hidden" name="no_shipping_method[<?php echo $product['product_id']; ?>]" value="0" />
                                        <strong><?php echo $mm_checkout_shipping_not_available; ?></strong>
                                    </div>
                                </td>
                                <td class="col-sm-3"></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
			<?php } ?>
		</table>
	</div>
<?php } ?>

<div class="buttons row">
    <div class="col-sm-5"></div>
    <div class="col-sm-4 shipping-total">
        <div class="pull-right">
            <div class="to-payment-methods">
                <strong class="shipping-summary">
                    <?php echo $mm_checkout_shipping_total; ?>
                    <span id="total-shipping-cost"></span>
                </strong>
            </div>
            <input type="button" value="<?php echo $button_continue; ?>" id="ms-button-shipping-method" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
        </div>
    </div>
    <div class="col-sm-3"></div>
</div>

<script>
    $(function() {
        function getTotalShippingCost() {
            var selected_methods = $('#collapse-shipping-method input[type=\'radio\']:checked');
            var total_shipping_cost = [];

            $(selected_methods).map(function(index, item) {
                var shipping_cost = $.trim($(this).closest('.radio').find('.cost').text());
                total_shipping_cost.push(shipping_cost);
            });

            $.ajax({
                url: 'index.php?route=multimerch/checkout_shipping_method/jxFormatPrice',
                type: 'post',
                data: {total_shipping_cost: total_shipping_cost},
                dataType: 'json',
                success: function(json) {
                    $('#total-shipping-cost').text(json['total_shipping_cost_formatted']);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }

        getTotalShippingCost();
        $(document).on('change', '#collapse-shipping-method input[type="radio"]', getTotalShippingCost);

        $(document).delegate('#ms-button-shipping-method', 'click', function() {
            $.ajax({
                url: 'index.php?route=multimerch/checkout_shipping_method/jxSave',
                type: 'post',
                data: $('#collapse-shipping-method input[type="radio"]:checked, input[name^="free_shipping"], input[name^="digital"], input[name^="no_shipping_method"]'),
                dataType: 'json',
                beforeSend: function() {
                    $('#button-shipping-method').button('loading');
                },
                success: function(json) {
                    console.log(json)
                    $('.alert, .text-danger').remove();

                    if (json['redirect']) {
                        window.location = json['redirect'];
                    } else if (json['error']) {
                        $('#button-shipping-method').button('reset');

                        if (json['error']['warning']) {
                            $('#collapse-shipping-method .panel-body').prepend('<div class="alert alert-danger">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                        }
                    } else {
                        $.ajax({
                            url: 'index.php?route=checkout/payment_method',
                            dataType: 'html',
                            complete: function() {
                                $('#button-shipping-method').button('reset');
                            },
                            success: function(html) {
                                $('#collapse-payment-method .panel-body').html(html);

                                $('#collapse-payment-method').parent().find('.panel-heading .panel-title').html('<a href="#collapse-payment-method" data-toggle="collapse" data-parent="#accordion" class="accordion-toggle"><?php echo $text_checkout_payment_method; ?> <i class="fa fa-caret-down"></i></a>');

                                $('a[href=\'#collapse-payment-method\']').trigger('click');

                                $('#collapse-checkout-confirm').parent().find('.panel-heading .panel-title').html('<?php echo $text_checkout_confirm; ?>');
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                console.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                alert("Error: " + xhr.responseText);
                            }
                        });
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });
    });
</script>