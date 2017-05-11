<form id="slr-pg-paypal" class="ms-form form-horizontal">
    <input type="hidden" id="pg-name" value="<?php echo $pg_name; ?>">

    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_pp_address; ?></span></label>
        <div class="col-sm-10">
            <input type="text" name="pp_address" value="<?php echo $pp_address; ?>" placeholder="<?php echo $text_pp_address; ?>" class="form-control" />
        </div>
    </div>

    <div class="buttons">
        <div class="pull-right">
            <a class="btn btn-primary ms-pg-submit"><span><?php echo $button_save; ?></span></a>
        </div>
    </div>
</form>
