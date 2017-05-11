<?php

class ControllerMultimerchXtSettings extends ControllerMultimerchXtBase {
	private $error = array();
	public  $data = array();
	
	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
	}
}	
?>