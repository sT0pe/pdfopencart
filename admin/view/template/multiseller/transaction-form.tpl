<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button id="ms-submit-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $this->url->link('multimerch/transaction', 'token=' . $this->session->data['token']); ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $ms_transactions_heading; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div style="display: none" class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $ms_transactions_new; ?></h3>
      </div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $ms_from; ?></label>
                <div class="col-sm-10">
					<select class="form-control" name="transaction[from]">
						<option value=""><?php echo $ms_none; ?></option>
						<?php foreach($sellers as $seller) { ?>
						<option value="<?php echo $seller['seller_id']; ?>"><?php echo $seller['name']; ?></option>
						<?php } ?>
					</select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $ms_to; ?></label>
                <div class="col-sm-10">
					<select class="form-control" name="transaction[to]">
						<option value=""><?php echo $ms_none; ?></option>
						<?php foreach($sellers as $seller) { ?>
						<option value="<?php echo $seller['seller_id']; ?>"><?php echo $seller['name']; ?></option>
						<?php } ?>
					</select>
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $ms_amount; ?></label>
                <div class="col-sm-10 control-inline">
                    <?php echo $this->currency->getSymbolLeft(); ?>
                    <input type="text" name="transaction[amount]" size="5"></input>
                    <?php echo $this->currency->getSymbolRight(); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $ms_description; ?></label>
                <div class="col-sm-10 control-inline">
                    <textarea class="form-control" name="transaction[description]" cols="40" rows="5"></textarea>
                </div>
            </div>
		</form>
      </div>
	</div>
</div>

<script>
$("#ms-submit-button").click(function() {
	var button = $(this);
	$.ajax({
		type: "POST",
		dataType: "json",
		url: 'index.php?route=multimerch/transaction/jxSave&token=<?php echo $token; ?>',
		data: $('#form').serialize(),
		beforeSend: function() {
			$('div.text-danger').remove();
            $('.alert-danger').hide().find('i').text('');
		},
		complete: function(jqXHR, textStatus) {
            button.show().prev('span.wait').remove();
            $('.alert-danger').hide().find('i').text('');
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('.alert-danger').show().find('i').text(textStatus);
		},			
		success: function(jsonData) {
			if (!jQuery.isEmptyObject(jsonData.errors)) {
				for (error in jsonData.errors) {
					$('[name="'+error+'"]').after('<div class="text-danger">' + jsonData.errors[error] + '</div>');
				}
			} else {
				window.location = 'index.php?route=multimerch/transaction&token=<?php echo $token; ?>';
			}
		}
	});
});
</script>
<?php echo $footer; ?> 