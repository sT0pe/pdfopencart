<?php

namespace MultiMerch\Mail\Message\Admin;

use MultiMerch\Mail\Message\Message;

class ProductCreated extends Message
{
    protected $template = 'admin/product_created.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_admin_subject_product_created'));
    }
}