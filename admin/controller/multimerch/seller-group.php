<?php

class ControllerMultimerchSellerGroup extends ControllerMultimerchBase {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
	}
	
	public function getTableData() {
		$colMap = array(
			'id' => 'msg.seller_group_id'
		);

		$sorts = array('id', 'name', 'description');
		$filters = $sorts;
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsSellerGroup->getSellerGroups(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/seller-group/update', 'token=' . $this->session->data['token'] . '&seller_group_id=' . $result['seller_group_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil'></i></a>";
			$actions .= "<a class='btn btn-danger' href='" . $this->url->link('multimerch/seller-group/delete', 'token=' . $this->session->data['token'] . '&seller_group_id=' . $result['seller_group_id'], 'SSL') . "' title='".$this->language->get('button_delete')."'><i class='fa fa-trash-o''></i></a>";
			
			$rates = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $result['seller_group_id']));
			$actual_fees = '';
			foreach ($rates as $rate) {
				$actual_fees .= '<span class="fee-rate-' . $rate['rate_type'] . '"><b>' . $this->language->get('ms_commission_short_' . $rate['rate_type']) . ':</b>' . ($rate['rate_type'] != MsCommission::RATE_SIGNUP ? $rate['percent'] . '%+' : '') . $this->currency->getSymbolLeft() .  $this->currency->format($rate['flat'], $this->config->get('config_currency'), '', FALSE) . $this->currency->getSymbolRight() . '&nbsp;&nbsp;';
			}
			
			$columns[] = array_merge(
				$result,
				array(
					'checkbox'          => "<input type='checkbox' name='selected[]' value='{$result['seller_group_id']}' />",
					'id' => $result['seller_group_id'],
					'name'              => $result['name'],
					'description' => (utf8_strlen($result['description']) > 80 ? mb_substr($result['description'], 0, 80) . '...' : $result['description']),
					'rates' => $actual_fees,
					'actions' => $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}
	
	// List all the seller groups
	public function index() {
		$this->validate(__FUNCTION__);
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_seller_groups_breadcrumbs'),
				'href' => $this->url->link('multimerch/seller-group', '', 'SSL'),
			)
		));
		
		$this->data['insert'] = $this->url->link('multimerch/seller-group/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('multimerch/seller-group/delete', 'token=' . $this->session->data['token'], 'SSL');
	
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_seller_groups_heading');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->document->setTitle($this->language->get('ms_catalog_seller_groups_heading'));
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/seller-group.tpl', $this->data));
	}
	
	// Insert a new seller group
	public function insert() {
		$this->load->model('tool/image');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_insert_seller_group_heading');
		$this->document->setTitle($this->language->get('ms_catalog_insert_seller_group_heading'));
		
		$this->data['cancel'] = $this->url->link('multimerch/seller-group', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		

		// badges
		$badges = $this->MsLoader->MsBadge->getBadges();
		foreach($badges as &$badge) {
			$badge['image'] = $this->model_tool_image->resize($badge['image'], 30, 30);
		}
		$this->data['badges'] = $badges;

		$this->data['settings'] = $this->MsLoader->MsSetting->getSellerGroupDefaults();
		$this->data['seller_group'] = NULL;
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_seller_groups_breadcrumbs'),
				'href' => $this->url->link('multimerch/seller-group', '', 'SSL'),
			)
		));		
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/seller-group-form.tpl', $this->data));
	}
	
	// Update a seller group
	public function update() {
		$this->validate(__FUNCTION__);
		$this->load->model('tool/image');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_edit_seller_group_heading');
		$this->document->setTitle($this->language->get('ms_catalog_edit_seller_group_heading'));
		
		$this->data['cancel'] = $this->url->link('multimerch/seller-group', 'token=' . $this->session->data['token'], 'SSL'); //'multiseller/seller-group';
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$seller_group = $this->MsLoader->MsSellerGroup->getSellerGroup($this->request->get['seller_group_id']);

		if (is_null($seller_group['commission_id']))
			$rates = NULL;
		else
			$rates = $this->MsLoader->MsCommission->getCommissionRates($seller_group['msg.commission_id']);
		
		$this->data['seller_group'] = array(
			'seller_group_id' => $seller_group['seller_group_id'],
			'description' => $this->MsLoader->MsSellerGroup->getSellerGroupDescriptions($this->request->get['seller_group_id']),
			'product_period' => $seller_group['product_period'],
			'product_quantity' => $seller_group['product_quantity'],
			'commission_id' => $seller_group['commission_id'],
			'commission_rates' => $rates,
		);

		// badges
		$badges = $this->MsLoader->MsBadge->getBadges();
		foreach($badges as &$badge) {
			$badge['image'] = $this->model_tool_image->resize($badge['image'], 30, 30);
		}
		$this->data['badges'] = $badges;

		$seller_group_badges = $this->MsLoader->MsBadge->getSellerGroupBadges(array('seller_group_id' => $this->request->get['seller_group_id']));
		$this->data['seller_group']['badges'] = array();
		foreach($seller_group_badges as $b) {
			$this->data['seller_group']['badges'][] = $b['badge_id'];
		}

		$seller_group_settings = $this->MsLoader->MsSetting->getSellerGroupSettings(array('seller_group_id' => $this->request->get['seller_group_id']));
		$defaults = $this->MsLoader->MsSetting->getSellerGroupDefaults();
		$this->data['settings'] = array_merge($defaults, $seller_group_settings);

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_seller_groups_breadcrumbs'),
				'href' => $this->url->link('multimerch/seller-group', '', 'SSL'),
			)
		));		
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/seller-group-form.tpl', $this->data));
	}
	
	// Bulk delete of seller groups
	public function delete() { 
		if (isset($this->request->get['seller_group_id'])) $this->request->post['selected'] = array($this->request->get['seller_group_id']);
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $seller_group_id) {
				$this->MsLoader->MsSellerGroup->deleteSellerGroup($seller_group_id);
			}
			
			$this->session->data['success'] = $this->language->get('ms_success');
		}
		
		$this->response->redirect($this->url->link('multimerch/seller-group', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	// Validate delete of the seller group
	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'multimerch/seller-group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
      	
		foreach ($this->request->post['selected'] as $seller_group_id) {
    		if ($this->config->get('msconf_default_seller_group_id') == $seller_group_id) {
	  			$this->error['warning'] = $this->language->get('ms_error_seller_group_default');
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function jxSave() {
		$data = $this->request->post['seller_group'];
		$json = array();
		
		if (!isset($data['product_period']) || empty($data['product_period'])) $data['product_period'] = 0;
		if (empty($data['product_quantity'])) $data['product_quantity'] = 0;

		foreach ($data['description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$json['errors']['seller_group[description]['.$language_id.'][name]'] = $this->language->get('ms_error_seller_group_name');
			}
		}

		if (!empty($data['seller_group_id']) && $this->config->get('msconf_default_seller_group_id') == $data['seller_group_id']) {
			foreach ($data['commission_rates'] as &$rate) {
				if (empty($rate['flat'])) $rate['flat'] = 0;
				if (empty($rate['percent'])) $rate['percent'] = 0;
				if (!isset($rate['payment_method']) || (int)$rate['payment_method'] == 0) $rate['payment_method'] = 1;
			}
			unset($rate);
		}

		$validator = $this->MsLoader->MsValidator;

		$is_valid = $validator->validate(array(
			'name' => $this->language->get('ms_seller_group_product_number_limit'),
			'value' => $data['settings']['slr_gr_product_number_limit']
		),
			array(
				array('rule' => 'numeric')
			)
		);
		if(!$is_valid) $json['errors']['seller_group[settings][slr_gr_product_number_limit]'] = $validator->get_errors();

		if (empty($json['errors'])) {
			if (empty($data['seller_group_id'])) {
				$seller_group_id = $this->MsLoader->MsSellerGroup->createSellerGroup($data);
				$this->MsLoader->MsSetting->createSellerGroupSetting(array_merge($data, array('seller_group_id' => $seller_group_id)));

				$this->session->data['success'] = $this->language->get('ms_success_seller_group_created');
			} else {
				$this->MsLoader->MsSellerGroup->editSellerGroup($data['seller_group_id'], $data);
				$this->MsLoader->MsSetting->createSellerGroupSetting(array_merge($data, array('seller_group_id' => $data['seller_group_id'])));

				$this->session->data['success'] = $this->language->get('ms_success_seller_group_updated');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>
