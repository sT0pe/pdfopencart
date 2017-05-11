<?php

class ControllerMultimerchBase extends Controller {
	private $error = array();
	public  $data = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
		$parts = explode('/', $this->request->request['route']);
		if (!isset($parts[2]) || !in_array($parts[2], array('install','uninstall'))) {
		}

		//$data = array();
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->data['token'] = $this->session->data['token'];
		$this->document->addStyle('view/stylesheet/multimerch/multiseller.css');
		$this->document->addStyle('view/javascript/multimerch/datatables/css/jquery.dataTables.css');
		$this->document->addScript('view/javascript/multimerch/datatables/js/jquery.dataTables.min.js');
		$this->document->addScript('view/javascript/multimerch/common.js');
		//$this->document->addScript('//code.jquery.com/ui/1.11.2/jquery-ui.min.js');
	}

	// @todo: validation
	public function validate($action = '', $level = 'access') {
		return true;
		//var_dump($this->user->hasPermission($level, 'module/multimerch'));
//		if (in_array(strtolower($action), array('sellers', 'install','uninstall','jxsavesellerinfo', 'savesettings', 'jxconfirmpayment', 'jxcompletepayment', 'jxproductstatus'))
		if (!$this->user->hasPermission($level, 'module/multimerch')) {
			return $this->forward('error/permission');
		}
	}
}
?>
