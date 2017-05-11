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
				<?php if (isset($statustext) && ($statustext)) { ?>
				<div class="alert alert-<?php echo $statusclass; ?>"><?php echo $statustext; ?></div>
				<?php } ?>
				<h1><i class="fa fa-usd"></i><?php echo $ms_payment_payments_heading; ?></h1>

				<!-- PAYMENTS -->
				<div class="table-responsive">
					<table class="mm_dashboard_table table table-borderless table-hover" style="text-align: center" id="list-payments-new">
						<thead class="sss">
							<tr>
								<td class="mm_size_tiny"><?php echo $ms_id; ?></td>
								<td class="mm_size_tiny"><?php echo $ms_type; ?></td>
								<td class="mm_size_tiny"><?php echo $ms_pg_payment_method; ?></td>
								<td class="large"><?php echo $ms_description; ?></td>
								<td class="small"><?php echo $ms_amount; ?></td>
								<td class="small"><?php echo $ms_status; ?></td>
								<td class="medium"><?php echo $ms_date_paid; ?></td>
							</tr>

							<tr class="filter">
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td></td>
								<td><input type="text"/></td>
								<td></td>
								<td><input type="text" class="input-date-datepicker"/></td>
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
		$('#list-payments-new').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-payment/getPaymentData",
			"aoColumns": [
				{ "mData": "payment_id" },
				{ "mData": "payment_type" },
				{ "mData": "payment_method" },
				{ "mData": "description", "bSortable": false },
				{ "mData": "amount" },
				{ "mData": "payment_status" },
				{ "mData": "date_created" },
			],
			"aaSorting":  [[3,'desc']]
		});
	});
</script>
<?php echo $footer; ?>