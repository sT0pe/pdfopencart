<p>
    <?php echo nl2br(sprintf($this->translate('ms_mail_admin_edit_product_awaiting_moderation'), $this->product_name, $this->sender)) ?>
</p>
<?php if (!empty($this->message)): ?>
    <p><?php echo nl2br(sprintf($this->translate('ms_mail_message'), $this->message)) ?></p>
<?php endif; ?>