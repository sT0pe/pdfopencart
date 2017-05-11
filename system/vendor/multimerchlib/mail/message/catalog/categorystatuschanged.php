<?php

namespace MultiMerch\Mail\Message\Catalog;

use MultiMerch\Mail\Message\Message;

class CategoryStatusChanged extends Message
{
	protected $template = 'catalog/category_status_changed.tpl';

	public function beforeSend()
	{
		$this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_category_status_changed'));
	}
}