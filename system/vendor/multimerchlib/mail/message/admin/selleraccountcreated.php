<?php

namespace MultiMerch\Mail\Message\Admin;

use MultiMerch\Mail\Message\Message;

class SellerAccountCreated extends Message
{
    protected $template = 'admin/seller_account_created.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_admin_subject_seller_account_created'));
    }
}