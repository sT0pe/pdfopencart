<!-- //todo Tpl is not used. MM 8.2 layout fixes were applied. -->

<?php echo $header; ?>
<div class="container ms-account-return-info">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<?php if (isset($error_warning) && $error_warning) { ?>
	<div class="alert alert-danger warning main"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
	<?php } ?>

	<?php if (isset($success) && ($success)) { ?>
	<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?></div>
	<?php } ?>

	<?php if (isset($statustext) && ($statustext)) { ?>
	<div class="alert alert-<?php echo $statusclass; ?>"><?php echo $statustext; ?></div>
	<?php } ?>

	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>

			<!-- order information -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-book"></i> <?php echo $text_order_detail; ?></h3>
				</div>
				<table class="table table-responsive table-bordered">
					<tbody>
					<tr>
						<td style="width: 50%;"><?php if ($invoice_no) { ?>
							<b><?php echo $text_invoice_no; ?></b> <?php echo $invoice_no; ?><br />
							<?php } ?>
							<b><?php echo $text_order_id; ?></b> #<?php echo $order_id; ?><br />
							<b><?php echo $ms_status; ?>:</b> <?php echo $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $order_status_id)); ?><br />
							<b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?></td>
						<td style="width: 50%;"><?php if ($payment_method) { ?>
							<b><?php echo $text_payment_method; ?></b> <?php echo $payment_method; ?><br />
							<?php } ?>
							<?php if ($shipping_method) { ?>
							<b><?php echo $text_shipping_method; ?></b> <?php echo $shipping_method; ?>
							<?php } ?></td>
					</tr>
					</tbody>
				</table>
			</div>

			<!-- addresses -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-map-marker"></i> <?php echo $ms_account_orders_addresses; ?></h3>
				</div>
				<table class="table table-responsive table-bordered">
					<thead>
					<tr>
						<td class="left"><?php echo $text_payment_address; ?></td>
						<?php if ($shipping_address_1) { ?>
						<td class="left"><?php echo $text_shipping_address; ?></td>
						<?php } ?>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="left">
							<?php echo $payment_firstname ? $payment_firstname . '<br />' : ''; ?>
							<?php echo $payment_lastname ? $payment_lastname . '<br />' : ''; ?>
							<?php echo $payment_company ? $payment_company . '<br />' : ''; ?>
							<?php echo $payment_address_1 ? $payment_address_1 . '<br />' : ''; ?>
							<?php echo $payment_address_2 ? $payment_address_2 . '<br />' : ''; ?>
							<?php echo $payment_city ? $payment_city . '<br />' : ''; ?>
							<?php echo $payment_postcode ? $payment_postcode . '<br />' : ''; ?>
							<?php echo $payment_zone ? $payment_zone . '<br />' : ''; ?>
							<?php echo $payment_zone_code ? $payment_zone_code . '<br />' : ''; ?>
							<?php echo $payment_country ? $payment_country . '<br />' : ''; ?>
							<?php echo $telephone ? $telephone . '<br />' : ''; ?>
						</td>
						<?php if ($shipping_address_1) { ?>
						<td class="left">
							<?php echo $shipping_firstname ? $shipping_firstname . '<br />' : ''; ?>
							<?php echo $shipping_lastname ? $shipping_lastname . '<br />' : ''; ?>
							<?php echo $shipping_company ? $shipping_company . '<br />' : ''; ?>
							<?php echo $shipping_address_1 ? $shipping_address_1 . '<br />' : ''; ?>
							<?php echo $shipping_address_2 ? $shipping_address_2 . '<br />' : ''; ?>
							<?php echo $shipping_city ? $shipping_city . '<br />' : ''; ?>
							<?php echo $shipping_postcode ? $shipping_postcode . '<br />' : ''; ?>
							<?php echo $shipping_zone ? $shipping_zone . '<br />' : ''; ?>
							<?php echo $shipping_zone_code ? $shipping_zone_code . '<br />' : ''; ?>
							<?php echo $shipping_country ? $shipping_country . '<br />' : ''; ?>
							<?php echo $telephone ? $telephone . '<br />' : ''; ?>
						</td>
						<?php } ?>
					</tr>
					</tbody>
				</table>
			</div>

			<!-- products -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?php echo $ms_account_products; ?></h3>
				</div>
				<table class="table table-responsive table-bordered text-center">
					<thead>
					<tr>
						<td class="left"><?php echo $column_name; ?></td>
						<td class="left"><?php echo $column_model; ?></td>
						<td class="right"><?php echo $column_quantity; ?></td>
						<td class="right"><?php echo $column_price; ?></td>
						<td class="right"><?php echo $column_total; ?></td>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($products as $product) { ?>
					<tr>
						<td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
							<?php foreach ($product['option'] as $option) { ?>
							<br />
							<?php if ($option['type'] != 'file') { ?>
							&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
							<?php } else { ?>
							&nbsp;<small> - <?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></small>
							<?php } ?>
							<?php } ?></td>
						<td class="left"><?php echo $product['model']; ?></td>
						<td class="right"><?php echo $product['quantity']; ?></td>
						<td class="right"><?php echo $product['price']; ?></td>
						<td class="right"><?php echo $product['total']; ?></td>
					</tr>
					<?php } ?>
					</tbody>
					<tfoot style="text-align: center;">
					<?php foreach ($totals as $total) { ?>
					<tr>
						<td colspan="3"></td>
						<td><b><?php echo $total['title']; ?>:</b></td>
						<td><?php echo $total['text']; ?></td>
					</tr>
					<?php } ?>
					</tfoot>
				</table>
			</div>

			<!-- history -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-history"></i> <?php echo $ms_account_orders_history; ?></h3>
				</div>

				<table class="table table-responsive table-bordered text-center">
					<thead>
					<tr>
						<td class="col-md-6"><?php echo $text_comment; ?></td>
						<td class="col-md-3"><?php echo $ms_status; ?></td>
						<td class="col-md-3"><?php echo $ms_date; ?></td>
					</tr>
					</thead>

					<tbody>
					<?php if ($order_history) { ?>
					<?php foreach ($order_history as $history) { ?>
					<tr>
						<td class="col-md-6"><?php echo nl2br($history['comment']); ?></td>
						<td class="col-md-3"><?php echo $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $history['order_status_id'])); ?></td>
						<td class="col-md-3"><?php echo date($this->language->get('date_format_short'), strtotime($history['date_added'])); ?></td>
					</tr>
					<?php } ?>
					<?php } else { ?>
					<td colspan="3" style="padding: 25px;border: 1px solid #e8e8e8;"><?php echo $ms_account_orders_nohistory; ?></td>
					<?php } ?>
					</tbody>

					<tfoot>
					<tr>
						<td>
							<input type="hidden" name="suborder_id" id="suborder_id" value="<?php echo $suborder_id; ?>" />
							<textarea class="form-control" name="order_comment" id="order_comment" placeholder="<?php echo $ms_account_orders_add_comment; ?>" rows="3"></textarea>
						</td>

						<td>
							<select name="order_status" id="order_status" class="form-control">
								<?php foreach ($order_statuses as $status) { ?>
								<option value="<?php echo $status['order_status_id']; ?>" <?php if ($suborder_status_id && $status['order_status_id'] == $suborder_status_id || !$suborder_status_id && $status['order_status_id'] == $order_status_id) { ?>selected="selected"<?php } ?>><?php echo $status['name']; ?></option>
								<?php } ?>
							</select>
						</td>

						<td>
							<button type="button" id="button-history" data-loading-text="Loading..." class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $ms_account_orders_add_history; ?></button>
						</td>
					</tr>
					</tfoot>
				</table>
			</div>

			<div class="buttons">
				<div class="pull-left"><a href="<?php echo $link_back; ?>" class="btn btn-default"><span><?php echo $button_back; ?></span></a></div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$("#button-history").click(function() {
			if (!$("#order_comment").val() && $("#order_status").val() == <?php echo $suborder_status_id; ?>) return;

			var $btn = $(this).button('loading');

			$.ajax({
				type: "POST",
				dataType: "json",
				url: $('base').attr('href') + 'index.php?route=seller/account-order/jxAddHistory',
				data: $("#order_comment,#order_status,#suborder_id").serialize(),
				success: function(jsonData) {
					window.location.reload();
				},
				error: function() {
					window.location.reload();
				}
			});

			$btn.button('reset');
		});
	});
</script>
<?php echo $footer; ?>
