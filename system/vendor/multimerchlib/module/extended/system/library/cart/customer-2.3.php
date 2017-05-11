<?php

namespace Cart;

class Customer extends OC_Customer
{
	public function logout()
	{
		unset($this->session->data['multiseller']);
		return parent::logout();
	}
}