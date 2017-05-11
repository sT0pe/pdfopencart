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
				<h1><i class="fa fa-shopping-cart"></i><?php echo $ms_account_conversations_heading; ?></h1>
				<div class="table-responsive">
				<table class="mm_dashboard_table table table-borderless table-hover" id="list-conversations">
					<thead>
						<tr>
							<td><?php echo $ms_account_conversations_title; ?></td>
							<td><?php echo $ms_account_conversations_type; ?></td>
							<td><?php echo $ms_last_message; ?></td>
							<td class="small"><?php echo $ms_action; ?></td>
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
		$('#list-conversations').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=account/msconversation/getTableData",
			"aaSorting": [[ 3, "desc" ]],
			"aoColumns": [
				{ "mData": "title" },
				{ "mData": "conversation_type" },
				{ "mData": "last_message_date" },
				{ "mData": "actions", "bSortable": false, "sClass": "text-center" }
			]
		});
	});
</script>
<?php echo $footer; ?>