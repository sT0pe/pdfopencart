<?php

class ControllerSellerAccountAttribute extends ControllerSellerAccount {

	// General

	public function index() {
		$this->_validateCall();

		$this->document->addScript('catalog/view/javascript/multimerch/account-attribute.js');
		$this->document->setTitle($this->language->get('ms_account_attribute_breadcrumbs'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_attribute_breadcrumbs'),
				'href' => $this->url->link('seller/account-attribute', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-attribute');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	private function _validateCall() {
		if(!$this->config->get('msconf_allow_seller_attributes')) {
			return $this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
	}

	/************************************************************/


	// Attributes

	public function getAttributeTableData() {
		$this->_validateCall();

		$colMap = array(
			'id' => 'msa.attribute_id',
			'name' => 'ad.name',
			'status' => 'msa.attribute_status',
			'sort_order' => 'a.sort_order'
		);

		$sorts = array('name', 'status', 'sort_order');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsAttribute->getAttributes(
			array(
				'seller_ids' => $this->customer->getId()
			),
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
			// status
			$status = "";
			if(isset($result['attribute_status'])) {
				$status .= "<p style='color: ";

				if($result['attribute_status'] == MsAttribute::STATUS_APPROVED) $status .= "blue";
				if($result['attribute_status'] == MsAttribute::STATUS_ACTIVE) $status .= "green";
				if($result['attribute_status'] == MsAttribute::STATUS_INACTIVE || $result['attribute_status'] == MsAttribute::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_seller_attribute_status_' . $result['attribute_status']) . "</p>";
			}

			// actions
			$actions = "";
			if ($result['attribute_status'] == MsAttribute::STATUS_INACTIVE || $result['attribute_status'] == MsAttribute::STATUS_APPROVED)
				$actions .= "<a class='icon-publish' href='" . $this->url->link('seller/account-attribute/activateAttribute', 'attribute_id=' . $result['attribute_id'], 'SSL') ."' title='" . $this->language->get('ms_activate') . "'><i class='fa fa-plus'></i></a>";

			if ($result['attribute_status'] == MsAttribute::STATUS_ACTIVE)
				$actions .= "<a class='icon-unpublish' href='" . $this->url->link('seller/account-attribute/deactivateAttribute', 'attribute_id=' . $result['attribute_id'], 'SSL') ."' title='" . $this->language->get('ms_deactivate') . "'><i class='fa fa-minus'></i></a>";

			$actions .= "<a class='icon-edit' href='" . $this->url->link('seller/account-attribute/updateAttribute', 'attribute_id=' . $result['attribute_id'], 'SSL') ."' title='" . $this->language->get('ms_edit') . "'><i class='fa fa-pencil'></i></a>";
			$actions .= "<a class='icon-remove' href='" . $this->url->link('seller/account-attribute/deleteAttribute', 'attribute_id=' . $result['attribute_id'], 'SSL') ."' title='" . $this->language->get('ms_delete') . "'><i class='fa fa-times'></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'name' => $result['name'],
					'group_name' => $result['ag_name'],
					'status' => $status,
					'sort_order' => $result['sort_order'],
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

	public function jxSaveAttribute() {
		$this->_validateCall();

		$MultiMerchModule = $this->MsLoader->load('\MultiMerch\Module\MultiMerch');
		$serviceLocator = $MultiMerchModule->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$data = $this->request->post;

		// Validate attribute is owned by seller
		if (isset($data['attribute_id']) && !empty($data['attribute_id'])) {
			if (!$this->MsLoader->MsAttribute->isMsAttribute($data['attribute_id'], array('seller_id' => $this->customer->getId()))) {
				return;
			}
		}

		$languages = $this->model_localisation_language->getLanguages();
		$defaultLanguageId = $this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language'));
		$validator = $this->MsLoader->MsValidator;

		// Validate fields
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$primary = true;
			if ($language_id != $defaultLanguageId)
				$primary = false;
			// attribute name
			if (!$validator->validate(array(
					'name' => $this->language->get('ms_account_attribute_name'),
					'value' => $data['attribute_description'][$language_id]['name']
				),
					array(
						!$primary ? array() : array('rule' => 'required'),
						array('rule' => 'max_len,100')
					)
				)
			) $json['errors']["attribute_description[$language_id][name]"] = $validator->get_errors();

			if (!$validator->validate(array(
				'name' => $this->language->get('ms_sort_order'),
				'value' => $data['sort_order']
			),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'numeric')
				)
			)
			) $json['errors']["sort_order"] = $validator->get_errors();

			// Copy fields data from main language
			if(!$primary) {
				if (empty($data['attribute_description'][$language_id]['name'])) $data['attribute_description'][$language_id]['name'] = $data['attribute_description'][$defaultLanguageId]['name'];
			}
		}

		// return if errors
		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		// Validate status field
		if(!isset($data['attribute_status'])) $data['attribute_status'] = MsAttribute::STATUS_DISABLED;

		// Seller
		$data['seller_id'] = $this->customer->getId();
		$seller = $this->MsLoader->MsSeller->getSeller($data['seller_id']);

		// Finish
		if (isset($data['attribute_id']) && !empty($data['attribute_id'])) {
			$this->MsLoader->MsAttribute->sellerUpdateAttribute($data['attribute_id'], $data);
			$this->session->data['success'] = $this->language->get('ms_success_attribute_updated');
		} else {
			$this->MsLoader->MsAttribute->sellerCreateAttribute($data);
			$this->session->data['success'] = $this->language->get('ms_success_attribute_created');

			$MailAttributeCreated = $serviceLocator->get('MailAttributeCreated', false)
				->setTo($MultiMerchModule->getNotificationEmail())
				->setData(array(
					'addressee' => $this->config->get('config_owner'),
					'slr_name' => $seller['ms.nickname'],
					'attr_name' => $data['attribute_description'][$defaultLanguageId]['name'],
					'attr_href' => $this->MsLoader->MsHelper->adminUrlLink('multimerch/attribute', '', true)
				));
			$mails->add($MailAttributeCreated);
		}

		if ($mails->count()) {
			$mailTransport->sendMails($mails);
		}

		$json['redirect'] = $this->url->link('seller/account-attribute#tab-attribute', '', 'SSL');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function _initAttributeForm() {
		$this->_validateCall();

		$this->load->model('localisation/language');
		$this->document->addScript('catalog/view/javascript/multimerch/account-attribute.js');
		$this->MsLoader->MsHelper->addStyle('multimerch/flags');

		$seller_id = $this->customer->getId();
		$attribute_groups = $this->MsLoader->MsAttribute->getAttributeGroups(array(
//			'attribute_group_status' => MsAttribute::STATUS_ACTIVE,
			'seller_ids' => '0,' . $seller_id // '0' means we also include global attribute groups
		));

		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['attribute_groups'] = $attribute_groups;

		$this->data['back'] = $this->url->link('seller/account-attribute', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_attribute_heading'));
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_attribute_breadcrumbs'),
				'href' => $this->url->link('seller/account-attribute', '', 'SSL'),
			)
		));
	}

	public function createAttribute() {
		$this->_validateCall();

		$this->_initAttributeForm();

		$this->data['attribute'] = FALSE;
		$this->data['heading'] = $this->language->get('ms_account_newattribute_heading');
		$this->document->setTitle($this->language->get('ms_account_newattribute_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-attribute-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function updateAttribute() {
		$this->_validateCall();

		$attribute_id = isset($this->request->get['attribute_id']) ? (int)$this->request->get['attribute_id'] : 0;

		if ($this->MsLoader->MsAttribute->isMsAttribute($attribute_id)) {
			$attribute_info = $this->MsLoader->MsAttribute->getAttributes(array('attribute_id' => $attribute_id, 'single' => 1));
		} else {
			return $this->response->redirect($this->url->link('seller/account-attribute', '', 'SSL'));
		}

		$this->_initAttributeForm();

		$this->data['attribute'] = $attribute_info;
		$this->data['heading'] = $this->language->get('ms_account_editattribute_heading');
		$this->document->setTitle($this->language->get('ms_account_editattribute_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-attribute-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function deleteAttribute() {
		$this->_validateCall();

		$json = array();
		$attribute_id = isset($this->request->get['attribute_id']) ? (int)$this->request->get['attribute_id'] : 0;

		if($attribute_id) {
			// check if any products are attached
			$product_total = $this->MsLoader->MsAttribute->ocGetTotalProductsByAttributeId($attribute_id);

			if ($product_total) {
				$json['error'] = sprintf($this->language->get('ms_error_attribute_assigned_to_products'), $product_total);
			} else {
				$this->MsLoader->MsAttribute->deleteAttribute($attribute_id);
				$this->session->data['success'] = $this->language->get('ms_success_attribute_deleted');
			}
		} else {
			$json['error'] = $this->language->get('ms_error_attribute_id');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function activateAttribute() {
		$this->_validateCall();

		$attribute_id = isset($this->request->get['attribute_id']) ? $this->request->get['attribute_id'] : false;
		$seller_id = $this->customer->getId();

		if($attribute_id && $this->MsLoader->MsAttribute->isMsAttribute($attribute_id, array('seller_id' => $seller_id))) {
			$this->MsLoader->MsAttribute->sellerUpdateAttribute($attribute_id, array('attribute_status' => MsAttribute::STATUS_ACTIVE));
			$this->session->data['success'] = $this->language->get('ms_success_attribute_activated');
		}

		$this->response->redirect($this->url->link('seller/account-attribute', '', 'SSL'));
	}

	public function deactivateAttribute() {
		$this->_validateCall();

		$attribute_id = isset($this->request->get['attribute_id']) ? $this->request->get['attribute_id'] : false;
		$seller_id = $this->customer->getId();

		if($attribute_id && $this->MsLoader->MsAttribute->isMsAttribute($this->request->get['attribute_id'], array('seller_id' => $seller_id))) {
			$this->MsLoader->MsAttribute->sellerUpdateAttribute($this->request->get['attribute_id'], array('attribute_status' => MsAttribute::STATUS_INACTIVE));
			$this->session->data['success'] = $this->language->get('ms_success_attribute_deactivated');
		}

		$this->response->redirect($this->url->link('seller/account-attribute', '', 'SSL'));
	}


	/************************************************************/


	// Attribute groups

	public function getAttributeGroupTableData() {
		$colMap = array(
			'id' => 'msag.attribute_group_id',
			'name' => 'agd.name',
			'status' => 'msag.attribute_group_status',
			'sort_order' => 'ag.sort_order'
		);

		$sorts = array('name', 'status', 'sort_order');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsAttribute->getAttributeGroups(
			array(
				'seller_ids' => $this->customer->getId()
			),
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
			$status = "";
			if(isset($result['attribute_group_status'])) {
				$status .= "<p style='color: ";

				if($result['attribute_group_status'] == MsAttribute::STATUS_APPROVED) $status .= "blue";
				if($result['attribute_group_status'] == MsAttribute::STATUS_ACTIVE) $status .= "green";
				if($result['attribute_group_status'] == MsAttribute::STATUS_INACTIVE || $result['attribute_group_status'] == MsAttribute::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_seller_attribute_status_' . $result['attribute_group_status']) . "</p>";
			}

			// actions
			$actions = "";
			if ($result['attribute_group_status'] == MsAttribute::STATUS_INACTIVE || $result['attribute_group_status'] == MsAttribute::STATUS_APPROVED)
				$actions .= "<a class='icon-publish' href='" . $this->url->link('seller/account-attribute/activateAttributeGroup', 'attribute_group_id=' . $result['attribute_group_id'], 'SSL') ."' title='" . $this->language->get('ms_activate') . "'><i class='fa fa-plus'></i></a>";

			if ($result['attribute_group_status'] == MsAttribute::STATUS_ACTIVE)
				$actions .= "<a class='icon-unpublish' href='" . $this->url->link('seller/account-attribute/deactivateAttributeGroup', 'attribute_group_id=' . $result['attribute_group_id'], 'SSL') ."' title='" . $this->language->get('ms_deactivate') . "'><i class='fa fa-minus'></i></a>";

			$actions .= "<a class='icon-edit' href='" . $this->url->link('seller/account-attribute/updateAttributeGroup', 'attribute_group_id=' . $result['attribute_group_id'], 'SSL') ."' title='" . $this->language->get('ms_edit') . "'><i class='fa fa-pencil'></i></a>";
			$actions .= "<a class='icon-remove' href='" . $this->url->link('seller/account-attribute/deleteAttributeGroup', 'attribute_group_id=' . $result['attribute_group_id'], 'SSL') ."' title='" . $this->language->get('ms_delete') . "'><i class='fa fa-times'></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'name' => $result['name'],
					'status' => $status,
					'sort_order' => $result['sort_order'],
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

	public function jxSaveAttributeGroup() {
		$MultiMerchModule = $this->MsLoader->load('\MultiMerch\Module\MultiMerch');
		$serviceLocator = $MultiMerchModule->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$data = $this->request->post;

		// Validate attribute is owned by seller
		if (isset($data['attribute_group_id']) && !empty($data['attribute_group_id'])) {
			if (!$this->MsLoader->MsAttribute->isMsAttributeGroup($data['attribute_group_id'], array('seller_id' => $this->customer->getId()))) {
				return;
			}
		}

		$languages = $this->model_localisation_language->getLanguages();
		$defaultLanguageId = $this->MsLoader->MsHelper->getLanguageId($this->config->get('config_language'));
		$validator = $this->MsLoader->MsValidator;

		// Validate fields
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$primary = true;
			if ($language_id != $defaultLanguageId)
				$primary = false;
			// attribute name
			if (!$validator->validate(array(
				'name' => $this->language->get('ms_account_attribute_group_name'),
				'value' => $data['attribute_group_description'][$language_id]['name']
			),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'max_len,100')
				)
			)
			) $json['errors']["attribute_group_description[$language_id][name]"] = $validator->get_errors();

			if (!$validator->validate(array(
				'name' => $this->language->get('ms_sort_order'),
				'value' => $data['sort_order']
			),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'numeric')
				)
			)
			) $json['errors']["sort_order"] = $validator->get_errors();

			// Copy fields data from main language
			if(!$primary) {
				if (empty($data['attribute_group_description'][$language_id]['name'])) $data['attribute_group_description'][$language_id]['name'] = $data['attribute_group_description'][$defaultLanguageId]['name'];
			}
		}

		// return if errors
		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		// Validate status field
		if(!isset($data['attribute_group_status'])) $data['attribute_group_status'] = MsAttribute::STATUS_DISABLED;

		// Seller
		$data['seller_id'] = $this->customer->getId();
		$seller = $this->MsLoader->MsSeller->getSeller($data['seller_id']);

		// Finish
		if (isset($data['attribute_group_id']) && !empty($data['attribute_group_id'])) {
			$this->MsLoader->MsAttribute->sellerUpdateAttributeGroup($data['attribute_group_id'], $data);
			$this->session->data['success'] = $this->language->get('ms_success_attribute_group_updated');
		} else {
			$this->MsLoader->MsAttribute->sellerCreateAttributeGroup($data);
			$this->session->data['success'] = $this->language->get('ms_success_attribute_group_created');

			$MailAttributeGroupCreated = $serviceLocator->get('MailAttributeGroupCreated', false)
				->setTo($MultiMerchModule->getNotificationEmail())
				->setData(array(
					'addressee' => $this->config->get('config_owner'),
					'slr_name' => $seller['ms.nickname'],
					'attr_gr_name' => $data['attribute_group_description'][$defaultLanguageId]['name'],
					'attr_gr_href' => $this->MsLoader->MsHelper->adminUrlLink('multimerch/attribute', '', true)
				));
			$mails->add($MailAttributeGroupCreated);
		}

		if ($mails->count()) {
			$mailTransport->sendMails($mails);
		}

		$json['redirect'] = $this->url->link('seller/account-attribute#tab-attribute-group', '', 'SSL');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function _initAttributeGroupForm() {
		$this->load->model('localisation/language');
		$this->document->addScript('catalog/view/javascript/multimerch/account-attribute.js');
		$this->MsLoader->MsHelper->addStyle('multimerch/flags');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['back'] = $this->url->link('seller/account-attribute', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_attribute_group_heading'));
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_attribute_group_breadcrumbs'),
				'href' => $this->url->link('seller/account-attribute', '', 'SSL'),
			)
		));
	}

	public function createAttributeGroup() {
		$this->_initAttributeGroupForm();

		$this->data['attribute_group'] = FALSE;
		$this->data['heading'] = $this->language->get('ms_account_newattributegroup_heading');
		$this->document->setTitle($this->language->get('ms_account_newattributegroup_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-attribute-group-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function updateAttributeGroup() {
		$attribute_group_id = isset($this->request->get['attribute_group_id']) ? (int)$this->request->get['attribute_group_id'] : 0;

		if ($this->MsLoader->MsAttribute->isMsAttributeGroup($attribute_group_id)) {
			$attribute_group_info = $this->MsLoader->MsAttribute->getAttributeGroups(array('attribute_group_id' => $attribute_group_id, 'single' => 1));
		} else {
			return $this->response->redirect($this->url->link('seller/account-attribute', '', 'SSL'));
		}
		$this->_initAttributeGroupForm();

		$this->data['attribute_group'] = $attribute_group_info;
		$this->data['heading'] = $this->language->get('ms_account_editattributegroup_heading');
		$this->document->setTitle($this->language->get('ms_account_editattributegroup_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-attribute-group-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function deleteAttributeGroup() {
		$json = array();
		$attribute_group_id = isset($this->request->get['attribute_group_id']) ? (int)$this->request->get['attribute_group_id'] : 0;

		if($attribute_group_id) {
			// check if any attributes are attached
			$attributes_total = $this->MsLoader->MsAttribute->ocGetTotalAttributesByAttributeGroupId($attribute_group_id);

			if ($attributes_total) {
				$json['error'] = sprintf($this->language->get('ms_error_attribute_group_assigned_to_attributes'), $attributes_total);
			} else {
				$this->MsLoader->MsAttribute->deleteAttributeGroup($attribute_group_id);
				$this->session->data['success'] = $this->language->get('ms_success_attribute_group_deleted');
			}
		} else {
			$json['error'] = $this->language->get('ms_error_attribute_group_id');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function activateAttributeGroup() {
		$attribute_group_id = isset($this->request->get['attribute_group_id']) ? $this->request->get['attribute_group_id'] : false;
		$seller_id = $this->customer->getId();

		if($attribute_group_id && $this->MsLoader->MsAttribute->isMsAttributeGroup($attribute_group_id, array('seller_id' => $seller_id))) {
			$this->MsLoader->MsAttribute->sellerUpdateAttributeGroup($attribute_group_id, array('attribute_group_status' => MsAttribute::STATUS_ACTIVE));
			$this->session->data['success'] = $this->language->get('ms_success_attribute_group_activated');
		}

		$this->response->redirect($this->url->link('seller/account-attribute', '', 'SSL'));
	}

	public function deactivateAttributeGroup() {
		$attribute_group_id = isset($this->request->get['attribute_group_id']) ? $this->request->get['attribute_group_id'] : false;
		$seller_id = $this->customer->getId();

		if($attribute_group_id && $this->MsLoader->MsAttribute->isMsAttributeGroup($attribute_group_id, array('seller_id' => $seller_id))) {
			$this->MsLoader->MsAttribute->sellerUpdateAttributeGroup($attribute_group_id, array('attribute_group_status' => MsAttribute::STATUS_INACTIVE));
			$this->session->data['success'] = $this->language->get('ms_success_attribute_group_deactivated');
		}

		$this->response->redirect($this->url->link('seller/account-attribute', '', 'SSL'));
	}
}
