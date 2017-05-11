<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-payout">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $ms_payment_heading; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>

			<div class="pull-right">
				<button id="ms-button-delete" type="button" data-toggle="tooltip" title="Delete" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
			</div>
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

		<div class="error-holder"></div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $ms_payment_heading; ?></h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<form action="" method="post" enctype="multipart/form-data" id="form">
						<table class="table table-bordered table-hover" style="text-align: center" id="list-payments">
							<thead>
								<tr>
									<td class="tiny"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
									<td class="tiny"><?php echo $ms_id; ?></td>
									<td class="medium"><?php echo $ms_type; ?></td>
									<td class="medium"><?php echo $ms_method; ?></td>
									<td class="medium"><?php echo $ms_seller; ?></td>
									<td><?php echo $ms_description; ?></td>
									<td class="small"><?php echo $ms_amount; ?></td>
									<td class="small"><?php echo $ms_status; ?></td>
									<td class="medium"><?php echo $ms_date_created; ?></td>
								</tr>
								<tr class="filter">
									<td></td>
									<td></td>
									<td></td>
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
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var msGlobals = {
		token: '<?php echo $token; ?>',
		ms_pg_payment_error_no_methods: '<?php echo htmlspecialchars($ms_pg_payment_error_no_methods, ENT_QUOTES, "UTF-8"); ?>'
	};

	$(function() {
		$(document).ready(function() {
			$('#list-payments').dataTable( {
				"sAjaxSource": "index.php?route=multimerch/payment/getTableData&token=<?php echo $token; ?>",
				"aoColumns": [
					{ "mData": "checkbox", "bSortable": false },
					{ "mData": "payment_id" },
					{ "mData": "payment_type" },
					{ "mData": "payment_code" },
					{ "mData": "seller" },
					{ "mData": "description" },
					{ "mData": "amount" },
					{ "mData": "payment_status" },
					{ "mData": "date_created" },
				],
				"aaSorting":  [[8,'desc']]
			});
		});

		$(document).on('click', '.ms-confirm-bank-transfer', function(e) {
			e.preventDefault();
			var button = $(this);
			var payment_id = button.closest('tr').find('input[name="payment_id"]').val();

			if(payment_id.length) {
				$.ajax({
					url: 'index.php?route=multimerch/payment/bank_transfer/jxConfirmPayment&token=<?php echo $token; ?>',
					type: 'post',
					data: {payment_id: payment_id},
					dataType: 'json',
					beforeSend: function () {
						button.button('loading');
					},
					success: function (json) {
						if(json.success) {
							button.button('reset');
							button.parent('td').html(json.success);
						}
					}
				});
			}

		});
	});
</script>
<?php echo $footer; ?>