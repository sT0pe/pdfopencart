<?php

namespace MultiMerch\Mail\Message\Seller;

use MultiMerch\Mail\Message\Message;

class AccountModified extends Message
{
    protected $template = 'seller/account_modified.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_seller_account_modified'));
    }
}