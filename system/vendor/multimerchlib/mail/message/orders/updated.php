<?php

namespace MultiMerch\Mail\Message\Orders;

use MultiMerch\Mail\Message\Message;

class Updated extends Message
{
    protected $template = 'orders/updated.tpl';

    public function beforeSend()
    {
        $data = $this->getData();
        $this->setSubject('[' . $this->getSender() . '] ' . sprintf($this->translate('ms_mail_subject_order_updated'), $data['order_id'], $data['seller_nickname']));
    }
}