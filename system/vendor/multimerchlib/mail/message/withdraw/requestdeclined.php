<?php

namespace MultiMerch\Mail\Message\Withdraw;

use MultiMerch\Mail\Message\Message;

class RequestDeclined extends Message
{
    protected $template = 'withdraw/request_declined.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_withdraw_request_declined'));
    }
}