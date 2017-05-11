<div class="container">
	<header id="header_invoice" class="row no-padding">
		<!-- logo -->
		<div class="col-xs-4">
			<img id="avatar" src="<?php echo $logo; ?>">
		</div>

		<!-- contacts and addresses -->
		<div class="col-xs-8">
			<ul>
				<?php echo $settings["slr_full_name"] ? $settings["slr_full_name"] . '<br />' : ''; ?>
				<?php echo $settings["slr_company"] ? $settings["slr_company"] . '<br />' : ''; ?>
				<?php echo $settings["slr_address_line1"] ? $settings["slr_address_line1"] . '<br />' : ''; ?>
				<?php echo $settings["slr_address_line2"] ? $settings["slr_address_line2"] . '<br />' : ''; ?>
				<?php echo $settings["slr_city"] ? $settings["slr_city"] . '<br />' : ''; ?>
				<?php echo $settings["slr_country"] ? $settings["slr_country"]["name"] . '<br />' : ''; ?>
				<?php echo $settings["slr_phone"] ? $settings["slr_phone"] . '<br />' : ''; ?>
			</ul>
		</div>
	</header>

    <div class="row no-padding details">
	<h2>Invoice</h2>
	<!-- To -->
	<div class="col-md-4">
		<?php echo $payment_firstname; ?> <?php echo $payment_lastname; ?> <br />

		<?php echo $payment_company ? $payment_company . '<br />' : ''; ?>

		<?php echo $payment_address_1 ? $payment_address_1 . '<br />' : ''; ?>
		<?php echo $payment_address_2 ? $payment_address_2 . '<br />' : ''; ?>

		<?php echo $payment_postcode; ?> <?php echo $payment_city; ?>

		<?php echo $payment_zone ? $payment_zone . '<br />' : ''; ?>
		<?php echo $payment_country ? $payment_country . '<br />' : ''; ?>
		<?php echo $telephone ? $telephone . '<br />' : ''; ?>
	</div>

    <div class="col-md-4"></div>

    <div class="col-md-4">
		<b><?php echo $text_order_id; ?></b> #<?php echo $order_id; ?><br />
		<b><?php echo $text_date_added; ?> </b> <?php echo $order_info['date_added']; ?><br />
        <b><?php echo $text_payment_method; ?></b> <?php echo $order_info['payment_method']; ?><br />
    </div>
    </div>

	<!-- products -->
    <div class="row no-padding products">
        <div class="col-md-12">
            <table>
                <thead>
                    <tr>
                        <th class="td-left"><?php echo $column_name; ?></th>
                        <th class="td-center"><?php echo $column_model; ?></th>
                        <th class="td-center"><?php echo $column_quantity; ?></th>
                        <th class="td-center"><?php echo $column_price; ?></th>
	                    <?php if($this->config->get('msconf_shipping_type') == 2 && $mm_shipping_flag) { ?>
	                    <th class="td-center"><?php echo $column_total_shipping; ?></th>
	                    <?php } ?>
	                    <th class="td-center"><?php echo $column_total; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                    <tr>
                        <td class="td-left"><?php echo $product['name']; ?>
                            <?php foreach ($product['option'] as $option) { ?>
                                <br /> &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                            <?php } ?>
                        </td>
                        <td class="td-center"><?php echo $product['model']; ?></td>
                        <td class="td-center"><?php echo $product['quantity']; ?></td>
                        <td class="td-center"><?php echo $product['price']; ?></td>
	                    <?php if($this->config->get('msconf_shipping_type') == 2 && $mm_shipping_flag) { ?>
	                    <td class="td-center"><?php echo $product['shipping_cost']; ?></td>
	                    <?php } ?>
                        <td class="td-center"><?php echo $product['total']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <?php foreach ($totals as $total) { ?>
                    <tr>
                        <td colspan="4"></td>
                        <td class="td-center"><b><?php echo $total['title']; ?></b></td>
                        <td class="td-center"><b><?php echo $total['text']; ?></b></td>
                    </tr>
                    <?php } ?>
                </tfoot>
            </table>
        </div>
    </div>

	<div class="left-pad-5 row">
		<div class="col-xs-8 allwidth"></div>
		<div class="col-xs-4 allwidth"></div>
	</div>
</div>
