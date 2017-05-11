<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-paypal" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
			<h1><?php echo $heading_title; ?></h1>
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
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form id="form-paypal" method="post" enctype="multipart/form-data" class="form-horizontal">
					<fieldset>
						<legend><?php echo $text_s_method_name; ?></legend>
						<div class="form-group required">
							<label class="col-sm-2 control-label">
								<span data-toggle="tooltip" title="<?php echo $text_pp_address_note; ?>"><?php echo $text_pp_address; ?></span>
							</label>
							<div class="col-sm-10">
								<input type="text" name="pp_address" value="<?php echo $pp_address; ?>" placeholder="<?php echo $text_pp_address; ?>" class="form-control" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">
								<span data-toggle="tooltip" title="<?php echo $text_sandbox_note; ?>"><?php echo $text_sandbox; ?></span>
							</label>
							<div class="col-sm-9">
								<label class="radio-inline"><input type="radio" name="s_sandbox" value="1" <?php if($s_sandbox == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="s_sandbox" value="0" <?php if($s_sandbox == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend><?php echo $text_mp_method_name; ?></legend>
						<div class="form-group required">
							<label class="col-sm-2 control-label">
								<span data-toggle="tooltip" title="<?php echo $text_api_username_note; ?>"><?php echo $text_api_username; ?></span>
							</label>
							<div class="col-sm-10">
								<input type="text" name="api_username" value="<?php echo $api_username; ?>" placeholder="<?php echo $text_api_username; ?>" class="form-control" />
							</div>
						</div>

						<div class="form-group required">
							<label class="col-sm-2 control-label">
								<span data-toggle="tooltip" title="<?php echo $text_api_password_note; ?>"><?php echo $text_api_password; ?></span>
							</label>
							<div class="col-sm-10">
								<input type="text" name="api_password" value="<?php echo $api_password; ?>" placeholder="<?php echo $text_api_password; ?>" class="form-control" />
							</div>
						</div>

						<div class="form-group required">
							<label class="col-sm-2 control-label">
								<span data-toggle="tooltip" title="<?php echo $text_api_signature_note; ?>"><?php echo $text_api_signature; ?></span>
							</label>
							<div class="col-sm-10">
								<input type="text" name="api_signature" value="<?php echo $api_signature; ?>" placeholder="<?php echo $text_api_signature; ?>" class="form-control" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">
								<span data-toggle="tooltip" title="<?php echo $text_sandbox_note; ?>"><?php echo $text_sandbox; ?></span>
							</label>
							<div class="col-sm-9">
								<label class="radio-inline"><input type="radio" name="mp_sandbox" value="1" <?php if($mp_sandbox == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="mp_sandbox" value="0" <?php if($mp_sandbox == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend><?php echo $ms_config_general; ?></legend>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $ms_pg_for_fee; ?></label>
							<div class="col-sm-9">
								<label class="radio-inline"><input type="radio" name="fee_enabled" value="1" <?php if($fee_enabled == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="fee_enabled" value="0" <?php if($fee_enabled == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $ms_pg_for_payout; ?></label>
							<div class="col-sm-9">
								<label class="radio-inline"><input type="radio" name="payout_enabled" value="1" <?php if($payout_enabled == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="payout_enabled" value="0" <?php if($payout_enabled == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $ms_debug_heading; ?></label>
							<div class="col-sm-9">
								<label class="radio-inline"><input type="radio" name="debug" value="1" <?php if($debug == 1) { ?> checked="checked" <?php } ?>  /><?php echo $text_yes; ?></label>
								<label class="radio-inline"><input type="radio" name="debug" value="0" <?php if($debug == 0) { ?> checked="checked" <?php } ?>  /><?php echo $text_no; ?></label>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $text_paypal_log_filename; ?></label>
							<div class="col-sm-9">
								<input type="text" name="log_filename" value="<?php echo $log_filename; ?>" placeholder="<?php echo $text_paypal_log_filename; ?>" class="form-control" />
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>