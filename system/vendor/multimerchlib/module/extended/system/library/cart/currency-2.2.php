<?php

namespace Cart;

class Currency extends OC_Currency
{
	protected $registry;

	public function __construct($registry)
	{
		$this->registry = $registry;
		parent::__construct($registry);
	}

	public function getSelectedCurrencyCode()
	{
		$session = $this->registry->get('session');
		$currency = '';
		if (isset($session->data['currency'])) {
			$currency = $session->data['currency'];
		}
		return $currency;
	}

	public function getSymbolLeft($currency = '')
	{
		if (!$currency) {
			$currency = $this->getSelectedCurrencyCode();
		}
		return parent::getSymbolLeft($currency);
	}

	public function getSymbolRight($currency = '')
	{
		if (!$currency) {
			$currency = $this->getSelectedCurrencyCode();
		}
		return parent::getSymbolRight($currency);
	}

	public function format($number, $currency = '', $value = '', $format = true)
	{
		if (!$currency) {
			$currency = $this->getSelectedCurrencyCode();
		}
		return parent::format($number, $currency, $value, $format);
	}

	public function getId($currency = '')
	{
		if (!$currency) {
			$currency = $this->getSelectedCurrencyCode();
		}
		return parent::getId($currency);
	}

	public function getCode()
	{
		return $this->getSelectedCurrencyCode();
	}

	public function getDecimalPlace($currency = '')
	{
		if (!$currency) {
			$currency = $this->getSelectedCurrencyCode();
		}
		return parent::getDecimalPlace($currency);
	}

	public function getValue($currency = '')
	{
		if (!$currency) {
			$currency = $this->getSelectedCurrencyCode();
		}
		return parent::getValue($currency);
	}

}
