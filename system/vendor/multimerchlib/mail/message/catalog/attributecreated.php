<?php

namespace MultiMerch\Mail\Message\Catalog;

use MultiMerch\Mail\Message\Message;

class AttributeCreated extends Message
{
	protected $template = 'catalog/attribute_created.tpl';

	public function beforeSend()
	{
		$data = $this->getData();
		$this->setSubject('[' . $this->getSender() . '] ' . sprintf($this->translate('ms_mail_subject_attribute_created'), $data['slr_name'], $data['attr_name']));
	}
}