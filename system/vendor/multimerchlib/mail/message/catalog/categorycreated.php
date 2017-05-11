<?php

namespace MultiMerch\Mail\Message\Catalog;

use MultiMerch\Mail\Message\Message;

class CategoryCreated extends Message
{
	protected $template = 'catalog/category_created.tpl';

	public function beforeSend()
	{
		$data = $this->getData();
		$this->setSubject('[' . $this->getSender() . '] ' . sprintf($this->translate('ms_mail_subject_category_created'), $data['slr_name'], $data['cat_name']));
	}
}