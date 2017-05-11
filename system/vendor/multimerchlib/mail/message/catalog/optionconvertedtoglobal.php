<?php

namespace MultiMerch\Mail\Message\Catalog;

use MultiMerch\Mail\Message\Message;

class OptionConvertedToGlobal extends Message
{
	protected $template = 'catalog/option_converted_to_global.tpl';

	public function beforeSend()
	{
		$this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_option_converted_to_global'));
	}
}