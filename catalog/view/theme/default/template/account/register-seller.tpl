<?php echo $header; ?>
<div class="container register-seller">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>

  <div class="alert alert-danger warning main"></div>

  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h1><?php echo $ms_account_register_seller; ?></h1>
      <p><?php echo $text_account_already; ?></p>

      <form id="seller-form" class="form-horizontal">
        <fieldset id="account">
          <legend><?php echo $ms_account_register_details; ?></legend>

          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_firstname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="seller[firstname]" placeholder="<?php echo $entry_firstname; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_lastname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="seller[lastname]" placeholder="<?php echo $entry_lastname; ?>" class="form-control" />
            </div>
          </div>

		  <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $ms_account_sellerinfo_nickname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="seller[nickname]" placeholder="<?php echo $ms_account_sellerinfo_nickname_note; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_email; ?></label>
            <div class="col-sm-10">
              <input type="email" name="seller[email]" placeholder="<?php echo $entry_email; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_password; ?></label>
            <div class="col-sm-10">
              <input type="password" name="seller[password]" placeholder="<?php echo $entry_password; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_confirm; ?></label>
            <div class="col-sm-10">
              <input type="password" name="seller[password_confirm]" placeholder="<?php echo $entry_confirm; ?>" class="form-control" />
            </div>
          </div>

			<?php if (isset($seller_terms)) { ?>
			<div class="form-group required">
				<label class="col-sm-2 control-label"><?php echo $ms_account_sellerinfo_terms; ?></label>
				<div class="col-sm-10">
					<p style="margin-bottom: 0">
						<input type="checkbox" name="seller[terms]" value="1" />
						<?php echo $seller_terms; ?>
					</p>
				</div>
			</div>
			<?php } ?>
        </fieldset>

        <div class="buttons">
          <div class="pull-right">
            <a class="btn btn-primary ms-spinner" id="ms-submit-button" value="<?php echo $button_continue; ?>"><span><?php echo $button_continue; ?></span></a>
          </div>
        </div>
      </form>

      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

<script>
	var msGlobals = {
		formError: '<?php echo htmlspecialchars($ms_error_form_submit_error, ENT_QUOTES, "UTF-8"); ?>'
	};
</script>
<?php echo $footer; ?>