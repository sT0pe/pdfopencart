<?php

namespace MultiMerch\Mail\Message\Admin;

use MultiMerch\Mail\Message\Message;

class EditProductAwaitingModeration extends Message
{
    protected $template = 'admin/edit_product_awaiting_moderation.tpl';

    public function beforeSend()
    {
        $this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_admin_subject_edit_product_awaiting_moderation'));
    }
}