<?php

namespace MultiMerch\Mail\Message\Seller;

use MultiMerch\Mail\Message\Message;

class RemindListing extends Message
{
    protected $template = 'seller/remind_listing.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_remind_listing'));
    }
}