<?php

namespace MultiMerch\Mail\Message\Catalog;

use MultiMerch\Mail\Message\Message;

class OptionCreated extends Message
{
	protected $template = 'catalog/option_created.tpl';

	public function beforeSend()
	{
		$data = $this->getData();
		$this->setSubject('[' . $this->getSender() . '] ' . sprintf($this->translate('ms_mail_subject_option_created'), $data['slr_name'], $data['opt_name']));
	}
}