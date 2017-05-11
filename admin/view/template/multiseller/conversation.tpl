<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">

	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $ms_account_conversations; ?></h1>
			<div class="pull-right">
				<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" id="delete-conversation"><i class="fa fa-trash-o"></i></button>
			</div>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if (isset($success) && $success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $ms_account_conversations; ?></h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form">
						<table class="mm_dashboard_table table table-borderless table-hover" id="list-conversations">
							<thead>
							<tr>
								<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
								<td><?php echo $ms_account_conversations_title; ?></td>
								<td class="medium"><?php echo $ms_account_conversations_date_added; ?></td>
								<td class="large"><?php echo $ms_last_message; ?></td>
								<td class="large"></td>
							</tr>
							<tr class="filter">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							</thead>
							<tbody></tbody>
						</table>
					</form>
				</div>
			</div>
		</div>

	</div>
</div>

<script>
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#list-conversations').dataTable( {
			"sAjaxSource": "index.php?route=multimerch/conversation/getTableData&token=<?php echo $token; ?>",
			"aaSorting": [[ 3, "desc" ]],
			"aoColumns": [
				{ "mData": "checkbox", "bSortable": false },
				{ "mData": "title" , "bSortable": false },
				{ "mData": "date_created" },
				{ "mData": "last_message_date" },
				{ "mData": "actions" , "bSortable": false, "sClass": "text-right"}
			]
		});

		$(document).on('click', '.ms-button-delete', function() {
			return confirm("<?php echo $this->language->get('text_confirm'); ?>");
		});

		$(document).on('click', '#delete-conversation', function(e) {
			e.preventDefault();
			var form = $('#form').serialize();
			if(form) {
				if(confirm('Are you sure?')) {
					$.ajax({
						url: 'index.php?route=multimerch/conversation/delete&token=<?php echo $token; ?>',
						data: form,
						type: 'post',
						dataType: 'json',
						complete: function(response) {
							console.log(response);
							window.location.reload();
						}
					});
				}
			}
		});
	});
</script>
<?php echo $footer; ?>