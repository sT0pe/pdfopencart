<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $heading; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
			<div class="pull-right">
				<a href="<?php echo $this->url->link('multimerch/payment', 'token=' . $this->session->data['token']); ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="alert alert-danger <?php echo (isset($error_warning) && $error_warning) ? '' : 'hidden'; ?>"><i class="fa fa-exclamation-circle"></i>
			<?php echo (isset($error_warning) && $error_warning) ? $error_warning : ''; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading; ?></h3>
			</div>
			<div class="panel-body">
				<form method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_pg_request; ?></label>
						<div class="col-sm-4">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th><?php echo $ms_description; ?></th>
										<th><?php echo $ms_amount; ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($payment_requests as $request) { ?>
										<tr>
											<td class="medium">
												<input type="hidden" name="request_ids[]" value="<?php echo $request['request_id']; ?>">
												<?php echo $request['description']; ?>
											</td>
											<td class="medium">
												<?php echo $request['amount_formatted']; ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="col-sm-6"></div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_type; ?></label>
						<div class="col-sm-10 readonly-field">
							<input type="hidden" name="payment_type" value="<?php echo $payment_type['id']; ?>" />
							<p><?php echo $payment_type['name']; ?></p>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $ms_payment_method; ?></label>
						<div class="col-sm-10">
							<select class="form-control" name="payment_method" id="payment-method">
								<?php foreach($payment_methods as $payment_method) { ?>
									<option value="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="methods-info">

					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	var msGlobals = {
		token: '<?php echo $token; ?>'
	};
</script>
<?php echo $footer; ?>