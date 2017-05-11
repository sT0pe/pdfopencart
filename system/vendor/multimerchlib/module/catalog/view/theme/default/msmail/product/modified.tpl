<p>
    <?php echo nl2br(sprintf($this->translate('ms_mail_product_modified'), $this->product_name, $this->sender, $this->translate('ms_product_status_' . $this->product_status))) ?>
</p>
<?php if (!empty($this->message)): ?>
    <p><?php echo nl2br(sprintf($this->translate('ms_mail_message'), $this->message)) ?></p>
<?php endif; ?>