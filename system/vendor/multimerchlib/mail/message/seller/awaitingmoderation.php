<?php

namespace MultiMerch\Mail\Message\Seller;

use MultiMerch\Mail\Message\Message;

class AwaitingModeration extends Message
{
    protected $template = 'seller/awaiting_moderation.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_seller_account_awaiting_moderation'));
    }
}