<?php

namespace MultiMerch\Mail\Message\Product;

use MultiMerch\Mail\Message\Message;

class Modified extends Message
{
    protected $template = 'product/modified.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_product_modified'));
    }
}