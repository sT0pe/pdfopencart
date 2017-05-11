<?php

namespace MultiMerch\Mail\Message\Catalog;

use MultiMerch\Mail\Message\Message;

class OptionStatusChanged extends Message
{
	protected $template = 'catalog/option_status_changed.tpl';

	public function beforeSend()
	{
		$this->setSubject('[' . $this->getSender() . '] ' . $this->translate('ms_mail_subject_option_status_changed'));
	}
}