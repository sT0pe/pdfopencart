<?php

namespace MultiMerch\Mail\Message\Admin;

use MultiMerch\Mail\Message\Message;

class WithdrawRequestSubmitted extends Message
{
    protected $template = 'admin/withdraw_request_submitted.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_admin_subject_withdraw_request_submitted'));
    }
}