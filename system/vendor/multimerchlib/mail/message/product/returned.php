<?php

namespace MultiMerch\Mail\Message\Product;

use MultiMerch\Mail\Message\Message;

class Returned extends Message
{
	protected $template = 'product/returned.tpl';

	public function beforeSend()
	{
		$this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_product_returned'));
	}
}