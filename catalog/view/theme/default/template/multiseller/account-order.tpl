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
				<h1><i class="fa fa-shopping-cart"></i><?php echo $ms_account_orders_heading; ?></h1>
				<div class="table-responsive">
					<table class="mm_dashboard_table table table-borderless table-hover" id="mm_orders">
						<thead>
						<tr>
							<td class="mm_size_tiny"><?php echo $ms_account_orders_id; ?></td>
							<td class="mm_size_small"><?php echo $ms_status; ?></td>
							<td class="mm_size_medium"><?php echo $ms_date_created; ?></td>
							<td class="mm_size_large"><?php echo $ms_account_orders_customer; ?></td>
							<td><?php echo $ms_account_orders_products; ?></td>
							<td class="mm_size_small"><?php echo $ms_account_orders_total; ?></td>
							<td class="mm_size_small"></td>
						</tr>
						<tr class="filter">
							<td></td>
							<td><input type="text"/></td>
							<td><input type="text" class="input-date-datepicker"/></td>
							<td><input type="text" /></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td></td>
						</tr>
						</thead>

						<tbody></tbody>
					</table>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<script>
	$(function() {
		$('#mm_orders').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-order/getTableData",
			"aoColumns": [
				{ "mData": "order_id" },
				{ "mData": "suborder_status", "bSortable": false },
				{ "mData": "date_created" },
				{ "mData": "customer_name" },
				{ "mData": "products", "bSortable": false, "sClass": "products" },
				{ "mData": "total_amount" },
				{ "mData": "view_order", "bSortable": false, "sClass": "action" }
			],
			"aaSorting":  [[4,'desc']]
		});
	});
</script>
<?php echo $footer; ?>