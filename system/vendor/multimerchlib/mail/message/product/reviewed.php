<?php

namespace MultiMerch\Mail\Message\Product;

use MultiMerch\Mail\Message\Message;

class Reviewed extends Message
{
	protected $template = 'product/reviewed.tpl';

	public function beforeSend()
	{
		$this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_product_reviewed'));
	}
}