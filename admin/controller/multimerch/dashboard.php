<?php

class ControllerMultimerchDashboard extends ControllerMultimerchBase {
	public function index() {
		$this->response->redirect($this->url->link('module/multimerch', 'token=' . $this->session->data['token'], 'SSL'));
	}
}
?>
