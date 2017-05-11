<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" id="saveSettings" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $ppa_adaptive; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="alert alert-danger hidden"><i class="fa fa-exclamation-circle"></i>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>

    <div class="alert alert-success hidden"><i class="fa fa-check-circle"></i>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $ppa_adaptive; ?></h3>
      </div>
      <div class="panel-body">
        <form id="settings" class="form-horizontal">
		<div class="form-group required">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_receiver_note; ?>"><?php echo $ppa_receiver; ?></span>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="msppaconf_receiver" value="<?php echo $msppaconf_receiver; ?>" size="30"/>
			</div>
		</div>
		<div class="form-group required">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_api_username_note; ?>"><?php echo $ppa_api_username; ?></span>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="msppaconf_api_username" value="<?php echo $msppaconf_api_username; ?>" size="30"/>
			</div>
		</div>
		
		<div class="form-group required">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_api_password_note; ?>"><?php echo $ppa_api_password; ?></span>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="msppaconf_api_password" value="<?php echo $msppaconf_api_password; ?>" size="30"/>
			</div>
		</div>
		
		<div class="form-group required">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_api_signature_note; ?>"><?php echo $ppa_api_signature; ?></span>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="msppaconf_api_signature" value="<?php echo $msppaconf_api_signature; ?>" size="30"/>
			</div>
		</div>
		
		<div class="form-group required">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_api_appid_note; ?>"><?php echo $ppa_api_appid; ?></span>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="msppaconf_api_appid" value="<?php echo $msppaconf_api_appid; ?>" size="30"/>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_secret_note; ?>"><?php echo $ppa_secret; ?></span>
			</label>
			<div class="col-sm-10">
				<?php echo $ppa_secret_key; ?>: <input type="text" class="form-control" name="msppaconf_secret_key" value="<?php echo $msppaconf_secret_key; ?>" size="15"/>
				<?php echo $ppa_secret_value; ?>: <input type="text" class="form-control" name="msppaconf_secret_value" value="<?php echo $msppaconf_secret_value; ?>" size="15"/>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_payment_type_note; ?>"><?php echo $ppa_payment_type; ?></span>
			</label>
			<div class="col-sm-10">
				<select id="payment_type" class="form-control" name="msppaconf_payment_type">
					<!--<option value="SIMPLE" <?php if($msppaconf_payment_type == 'SIMPLE') { ?> selected="selected" <?php } ?>><?php echo $ppa_payment_type_simple; ?></option>-->
					<option value="PARALLEL" <?php if($msppaconf_payment_type == 'PARALLEL') { ?> selected="selected" <?php } ?>><?php echo $ppa_payment_type_parallel; ?></option>
					<option value="CHAINED" <?php if($msppaconf_payment_type == 'CHAINED') { ?> selected="selected" <?php } ?>><?php echo $ppa_payment_type_chained; ?></option>
					<option value="PREAPPROVAL" <?php if($msppaconf_payment_type == 'PREAPPROVAL') { ?> selected="selected" <?php } ?>><?php echo $ppa_payment_type_preapproval; ?></option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_feespayer_note; ?>"><?php echo $ppa_feespayer; ?></span>
			</label>
			<div class="col-sm-10">
				<select class="form-control" name="msppaconf_feespayer">
					<option value="SENDER" <?php if($msppaconf_feespayer == 'SENDER') { ?> selected="selected" <?php } ?>><?php echo $ppa_feespayer_sender; ?></option>
					<option value="PRIMARYRECEIVER" <?php if($msppaconf_feespayer == 'PRIMARYRECEIVER') { ?> selected="selected" <?php } ?>><?php echo $ppa_feespayer_primaryreceiver; ?></option>
					<option value="EACHRECEIVER" <?php if($msppaconf_feespayer == 'EACHRECEIVER') { ?> selected="selected" <?php } ?>><?php echo $ppa_feespayer_eachreceiver; ?></option>
					<!--	<option value="SECONDARYONLY" <?php if($msppaconf_feespayer == 'SECONDARYONLY') { ?> selected="selected" <?php } ?>><?php echo $ppa_feespayer_secondaryonly; ?></option> -->
				</select>
			</div>
		</div>

	    <!-- deprecated -->
		<div style="display: none;" class="form-group">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_invalid_email_note; ?>"><?php echo $ppa_invalid_email; ?></span>
			</label>
			<div class="col-sm-10">
				<input type="radio" name="msppaconf_invalid_email" value="0" <?php if($msppaconf_invalid_email == 0) { ?> checked="checked" <?php } ?>  />
				<?php echo $ppa_disable_module; ?>
				<input type="radio" name="msppaconf_invalid_email" value="1" <?php if($msppaconf_invalid_email == 1) { ?> checked="checked" <?php } ?>  />
				<?php echo $ppa_balance_transaction; ?>
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_sandbox_note; ?>"><?php echo $ppa_sandbox; ?></span>
			</label>
			<div class="col-sm-10">
				<input type="radio" name="msppaconf_sandbox" value="1" <?php if($msppaconf_sandbox == 1) { ?> checked="checked" <?php } ?>  />
				<?php echo $text_yes; ?>
				<input type="radio" name="msppaconf_sandbox" value="0" <?php if($msppaconf_sandbox == 0) { ?> checked="checked" <?php } ?>  />
				<?php echo $text_no; ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_debug_note; ?>"><?php echo $ppa_debug; ?></span>
			</label>
			<div class="col-sm-10">
                <input type="radio" name="msppaconf_debug" value="1" <?php if($msppaconf_debug == 1) { ?> checked="checked" <?php } ?>  />
                <?php echo $text_yes; ?>
                <input type="radio" name="msppaconf_debug" value="0" <?php if($msppaconf_debug == 0) { ?> checked="checked" <?php } ?>  />
                <?php echo $text_no; ?>
          	</div>
        </div>

		<div class="form-group">
        	<label class="col-sm-2 control-label">
				<span data-toggle="tooltip" title="<?php echo $ppa_total_note; ?>"><?php echo $ppa_total; ?></span>
			</label>
			<div class="col-sm-10"><input type="text" class="form-control" name="msppaconf_total" value="<?php echo $msppaconf_total; ?>" /></label></div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label"><?php echo $ppa_completed_status; ?></label>
			<div class="col-sm-10"><select class="form-control" name="msppaconf_completed_status_id">
				<?php foreach ($order_statuses as $order_status) { ?>
				<?php if ($order_status['order_status_id'] == $msppaconf_completed_status_id) { ?>
				<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
				<?php } else { ?>
				<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
				<?php } ?>
				<?php } ?>
			  </select></label>
			</div>
		</div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $ppa_pending_status; ?></label>
            <div class="col-sm-10"><select class="form-control" name="msppaconf_pending_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $msppaconf_pending_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></div>
          </div>          
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $ppa_error_status; ?></label>
            <div class="col-sm-10"><select class="form-control" name="msppaconf_error_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $msppaconf_error_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $ppa_geo_zone; ?></label>
            <div class="col-sm-10"><select class="form-control" name="msppaconf_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $msppaconf_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $ppa_status; ?></label>
            <div class="col-sm-10"><select class="form-control" name="msppaconf_status">
                <?php if ($msppaconf_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $ppa_sort_order; ?></label>
            <div class="col-sm-10"><input type="text" class="form-control" name="msppaconf_sort_order" value="<?php echo $msppaconf_sort_order; ?>" size="1" /></div>
          </div>
          <div class="form-group">
			<label class="col-sm-2 control-label"><?php echo $text_ppa_log_filename; ?></label>
			<div class="col-sm-10">
		    <input type="text" name="msppaconf_ppa_log_filename" value="<?php echo $ppa_log_filename; ?>" placeholder="<?php echo $text_ppa_log_filename; ?>" class="form-control" />
		  </div>
            </div>
        </table>
      </form>
    </div>
  </div>
</div>

<script>
$(function() {
	$("#saveSettings").click(function() {
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=payment/ms_pp_adaptive/savesettings&token=<?php echo $token; ?>',
			data: $('#settings').serialize(),
			success: function(jsonData) {
				$('.alert-danger').addClass('hidden').find('i').text('');
				if (jsonData.errors) {
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $('.alert-danger').removeClass('hidden').find('i').append('<p>'+jsonData.errors[error]+'</p>');
					}				
				} else {
					$(".alert-success").removeClass('hidden').find('i').append('<p>'+jsonData.success+'</p>');
					window.location.reload();
				}
	       	}
		});
	});
});
</script>

<?php echo $footer; ?> 