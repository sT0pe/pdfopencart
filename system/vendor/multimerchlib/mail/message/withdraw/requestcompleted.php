<?php

namespace MultiMerch\Mail\Message\Withdraw;

use MultiMerch\Mail\Message\Message;

class RequestCompleted extends Message
{
    protected $template = 'withdraw/request_completed.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_withdraw_request_completed'));
    }
}