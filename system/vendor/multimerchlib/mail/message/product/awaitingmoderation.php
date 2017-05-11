<?php

namespace MultiMerch\Mail\Message\Product;

use MultiMerch\Mail\Message\Message;

class AwaitingModeration extends Message
{
    protected $template = 'product/awaiting_moderation.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_product_awaiting_moderation'));
    }
}