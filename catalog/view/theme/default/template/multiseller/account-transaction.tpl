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
				<h1><i class="fa fa-book"></i><?php echo $ms_account_transactions_heading; ?></h1>
				<div class="mm_blocks">
					<div class="col-sm-4">
						<div class="mm_dashboard_block transactions">
							<div class="head"><?php echo $ms_account_withdraw_balance ;?></div>
							<a><?php echo $ms_balance_formatted; ?></a> <span><?php echo $ms_reserved_formatted; ?></span>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="mm_dashboard_block transactions">
							<div class="head"><?php echo $ms_account_transactions_earnings ;?></div>
							<a><?php echo $earnings; ?></a> <span><?php echo $ms_reserved_formatted; ?></span>
						</div>
					</div>
				</div>
				<!-- BALANCE RECORDS -->
				<div class="table-responsive">
					<table class="mm_dashboard_table table table-borderless table-hover" style="text-align: center" id="list-transactions">
						<thead>
						<tr>
							<td class="tiny"><?php echo $ms_id; ?></td>
							<td class="medium"><?php echo $ms_account_transactions_amount; ?></td>
							<td><?php echo $ms_account_transactions_description; ?></td>
							<td class="medium"><?php echo $ms_date_created; ?></td>
						</tr>

						<tr class="filter">
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text" class="input-date-datepicker"/></td>
						</tr>
						</thead>

						<tbody>
						</tbody>
					</table>
				</div>
				<div class="cl"></div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<script>
	$(function() {
		$('#list-transactions').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-transaction/getTransactionData",
			"aoColumns": [
				{ "mData": "transaction_id" },
				{ "mData": "amount" },
				{ "mData": "description", "bSortable": false },
				{ "mData": "date_created" }
			],
			"aaSorting":  [[3,'desc']]
		});
	});
</script>
<?php echo $footer; ?>