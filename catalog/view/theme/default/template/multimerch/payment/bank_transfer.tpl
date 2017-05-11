<form id="slr-pg-bank-transfer" class="ms-form form-horizontal">
    <input type="hidden" id="pg-name" value="<?php echo $pg_name; ?>">

    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_fname; ?></span></label>
        <div class="col-sm-10">
            <input type="text" name="fname" value="<?php echo $fname; ?>" placeholder="<?php echo $text_fname; ?>" class="form-control" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_lname; ?></span></label>
        <div class="col-sm-10">
            <input type="text" name="lname" value="<?php echo $lname; ?>" placeholder="<?php echo $text_lname; ?>" class="form-control" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_bank_name; ?></span></label>
        <div class="col-sm-10">
            <input type="text" name="bank_name" value="<?php echo $bank_name; ?>" placeholder="<?php echo $text_bank_name; ?>" class="form-control" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_bank_country; ?></span></label>
        <div class="col-sm-10">
            <input type="text" name="bank_country" value="<?php echo $bank_country; ?>" placeholder="<?php echo $text_bank_country; ?>" class="form-control" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_bic; ?></span></label>
        <div class="col-sm-10">
            <input type="text" name="bic" value="<?php echo $bic; ?>" placeholder="<?php echo $text_bic; ?>" class="form-control" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_iban; ?></span></label>
        <div class="col-sm-10">
            <input type="text" name="iban" value="<?php echo $iban; ?>" placeholder="<?php echo $text_iban; ?>" class="form-control" />
        </div>
    </div>

    <div class="buttons">
        <div class="pull-right">
            <a class="btn btn-primary ms-pg-submit"><span><?php echo $button_save; ?></span></a>
        </div>
    </div>
</form>
