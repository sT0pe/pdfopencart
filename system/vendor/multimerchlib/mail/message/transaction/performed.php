<?php

namespace MultiMerch\Mail\Message\Transaction;

use MultiMerch\Mail\Message\Message;

class Performed extends Message
{
    protected $template = 'transaction/performed.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_transaction_performed'));
    }
}