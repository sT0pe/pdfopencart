<p>
    <?php echo nl2br(sprintf($this->translate('ms_mail_seller_account_modified'), $this->sender, $this->translate('ms_seller_status_' . $this->ms_seller_status))) ?>
</p>
<?php if (!empty($this->message)): ?>
    <p><?php echo nl2br(sprintf($this->translate('ms_mail_message'), $this->message)) ?></p>
<?php endif; ?>
