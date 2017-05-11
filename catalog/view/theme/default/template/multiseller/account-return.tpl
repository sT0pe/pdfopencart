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
				<div class="mm_head">
					<h1><i class="fa fa-briefcase"></i><?php echo $ms_account_returns_heading; ?></h1>
				</div>
				<div class="table-responsive">
					<table class="mm_dashboard_table table table-borderless table-hover" id="list-returns">
						<thead>
							<tr>
								<td class="mm_size_small"><?php echo $ms_account_return_id; ?></td>
								<td class="col-md-1"><?php echo $ms_account_return_order_id ;?></td>
								<td class="mm_size_small"><?php echo $ms_account_return_customer; ?></td>
								<td class="col-md-1"><?php echo $ms_account_return_status; ?></td>
								<td class="mm_size_small"><?php echo $ms_account_return_date_added; ?></td>
								<td class="mm_size_medium"></td>
							</tr>

							<tr class="filter">
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
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
		$('#list-returns').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-return/getTableData",
			"aoColumns": [
				{ "mData": "return_id", "bSortable": false },
				{ "mData": "order_id", "bSortable": false },
				{ "mData": "customer", "bSortable": false },
				{ "mData": "status" },
				{ "mData": "date_added" },
				{ "mData": "actions", "bSortable": false, "sClass": "text-right" }
			]
		});

		$(document).on('click', '.ms-button-delete', function() {
			if (!confirm('<?php echo $ms_account_returns_confirmdelete; ?>')) return false;
		});
	});
</script>
<?php echo $footer; ?>