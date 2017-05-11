<?php

namespace MultiMerch\Mail\Message\Seller;

use MultiMerch\Mail\Message\Message;

class PrivateMessage extends Message
{
	protected $template = 'seller/private_message.tpl';

	public function beforeSend()
	{
		$this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_private_message'));
	}
}