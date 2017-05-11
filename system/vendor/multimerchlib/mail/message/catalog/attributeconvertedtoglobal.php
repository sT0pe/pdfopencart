<?php

namespace MultiMerch\Mail\Message\Catalog;

use MultiMerch\Mail\Message\Message;

class AttributeConvertedToGlobal extends Message
{
	protected $template = 'catalog/attribute_converted_to_global.tpl';

	public function beforeSend()
	{
		$this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_attribute_converted_to_global'));
	}
}