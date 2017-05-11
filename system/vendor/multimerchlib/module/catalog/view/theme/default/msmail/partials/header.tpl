<div id="header">
    <?php if (isset($addressee)): ?>
        <p><?php echo nl2br(sprintf($this->translate('ms_mail_greeting'), $addressee)); ?></p>
    <?php else: ?>
        <p><?php echo nl2br($this->translate('ms_mail_greeting_no_name')); ?></p>
    <?php endif; ?>
</div>