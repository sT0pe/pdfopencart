<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-bank-transfer" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
				<form id="form-bank-transfer" method="post" enctype="multipart/form-data" class="form-horizontal">
					<fieldset>
						<legend><?php echo $ms_config_general; ?></legend>
						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $text_fname; ?></label>
							<div class="col-sm-10">
								<input type="text" name="fname" value="<?php echo $fname; ?>" placeholder="<?php echo $text_fname; ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $text_lname; ?></label>
							<div class="col-sm-10">
								<input type="text" name="lname" value="<?php echo $lname; ?>" placeholder="<?php echo $text_lname; ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $text_bank_name; ?></label>
							<div class="col-sm-10">
								<input type="text" name="bank_name" value="<?php echo $bank_name; ?>" placeholder="<?php echo $text_bank_name; ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $text_bank_country; ?></label>
							<div class="col-sm-10">
								<input type="text" name="bank_country" value="<?php echo $bank_country; ?>" placeholder="<?php echo $text_bank_country; ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $text_bic; ?></label>
							<div class="col-sm-10">
								<input type="text" name="bic" value="<?php echo $bic; ?>" placeholder="<?php echo $text_bic; ?>" class="form-control" />
							</div>
						</div>
						<div class="form-group required">
							<label class="col-sm-2 control-label"><?php echo $text_iban; ?></label>
							<div class="col-sm-10">
								<input type="text" name="iban" value="<?php echo $iban; ?>" placeholder="<?php echo $text_iban; ?>" class="form-control" />
							</div>
						</div>

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
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>