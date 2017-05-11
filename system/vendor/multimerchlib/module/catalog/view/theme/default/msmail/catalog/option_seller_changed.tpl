<p>
    <?php if($this->action == 1) { ?>
        <?php echo nl2br(sprintf($this->translate('ms_mail_option_seller_detached'), $this->opt_name)) ?>
    <?php } else if ($this->action == 2) { ?>
        <?php echo nl2br(sprintf($this->translate('ms_mail_option_seller_attached'), $this->opt_name)) ?>
    <?php } ?>
</p>