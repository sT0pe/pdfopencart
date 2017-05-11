<p>
    <?php if($this->action == 1) { ?>
        <?php echo nl2br(sprintf($this->translate('ms_mail_attribute_seller_detached'), $this->attr_name)) ?>
    <?php } else if ($this->action == 2) { ?>
        <?php echo nl2br(sprintf($this->translate('ms_mail_attribute_seller_attached'), $this->attr_name)) ?>
    <?php } ?>
</p>