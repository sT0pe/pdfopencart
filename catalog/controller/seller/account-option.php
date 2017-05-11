<?php

class ControllerSellerAccountOption extends ControllerSellerAccount {

	// General

	public function index() {
		$this->_validateCall();

		$this->document->addScript('catalog/view/javascript/multimerch/account-option.js');
		$this->document->setTitle($this->language->get('ms_account_option_breadcrumbs'));

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
				'text' => $this->language->get('ms_account_option_breadcrumbs'),
				'href' => $this->url->link('seller/account-option', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-option');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	private function _validateCall() {
		if(!$this->config->get('msconf_allow_seller_options')) {
			return $this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
	}


	/************************************************************/


	// Options

	public function getTableData() {
		$this->_validateCall();

		$colMap = array(
			'id' => 'mso.option_id',
			'name' => 'od.name',
			'status' => 'mso.option_status',
			'sort_order' => 'o.sort_order'
		);

		$sorts = array('name', 'status', 'sort_order');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsOption->getOptions(
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
			if(isset($result['option_status'])) {
				$status .= "<p style='color: ";

				if($result['option_status'] == MsOption::STATUS_APPROVED) $status .= "blue";
				if($result['option_status'] == MsOption::STATUS_ACTIVE) $status .= "green";
				if($result['option_status'] == MsOption::STATUS_INACTIVE || $result['option_status'] == MsOption::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_seller_option_status_' . $result['option_status']) . "</p>";
			}

			// actions
			$actions = "";
			if ($result['option_status'] == MsOption::STATUS_INACTIVE || $result['option_status'] == MsOption::STATUS_APPROVED)
				$actions .= "<a class='icon-publish' href='" . $this->url->link('seller/account-option/activateOption', 'option_id=' . $result['option_id'], 'SSL') ."' title='" . $this->language->get('ms_activate') . "'><i class='fa fa-plus'></i></a>";

			if ($result['option_status'] == MsOption::STATUS_ACTIVE)
				$actions .= "<a class='icon-unpublish' href='" . $this->url->link('seller/account-option/deactivateOption', 'option_id=' . $result['option_id'], 'SSL') ."' title='" . $this->language->get('ms_deactivate') . "'><i class='fa fa-minus'></i></a>";

			$actions .= "<a class='icon-edit' href='" . $this->url->link('seller/account-option/updateOption', 'option_id=' . $result['option_id'], 'SSL') ."' title='" . $this->language->get('ms_edit') . "'><i class='fa fa-pencil'></i></a>";
			$actions .= "<a class='icon-remove ms_remove_option' href='" . $this->url->link('seller/account-option/deleteOption', 'option_id=' . $result['option_id'], 'SSL') ."' title='" . $this->language->get('ms_delete') . "'><i class='fa fa-times'></i></a>";

			$option_values = $this->MsLoader->MsOption->getOptionValues($result['option_id']);
			$values_list = "";
			foreach ($option_values as $option_value) {
				$values_list .= $option_value['name'] . ($option_value !== end($option_values) ? ", " : "");
			}

			$columns[] = array_merge(
				$result,
				array(
					'name' => $result['name'],
					'values' => (mb_strlen($values_list) > 40 ? mb_substr($values_list, 0, 40) . '...' : $values_list),
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

	public function jxSaveOption() {
		$this->_validateCall();

		$MultiMerchModule = $this->MsLoader->load('\MultiMerch\Module\MultiMerch');
		$serviceLocator = $MultiMerchModule->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$data = $this->request->post;

		// Validate option is owned by seller
		if (isset($data['option_id']) && !empty($data['option_id'])) {
			if (!$this->MsLoader->MsOption->isMsOption($data['option_id'], array('seller_id' => $this->customer->getId()))) {
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
			// option name
			if (!$validator->validate(array(
				'name' => $this->language->get('ms_account_option_name'),
				'value' => $data['option_description'][$language_id]['name']
			),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'max_len,100')
				)
			)
			) $json['errors']["option_description[$language_id][name]"] = $validator->get_errors();

			if (isset($data['option_value'])) {
				// Unset sample row
				unset($data['option_value'][0]);

				$i = 1;
				foreach ($data['option_value'] as $option_value) {
					if (!$validator->validate(array(
						'name' => $this->language->get('ms_account_option_value'),
						'value' => $option_value['option_value_description'][$language_id]['name']
					),
						array(
							!$primary ? array() : array('rule' => 'required'),
							array('rule' => 'max_len,100')
						)
					)
					) $json['errors']["option_value[$i][option_value_description][$language_id][name]"] = $validator->get_errors();

					if (!$validator->validate(array(
						'name' => $this->language->get('ms_sort_order'),
						'value' => $option_value['sort_order']
					),
						array(
							!$primary ? array() : array('rule' => 'required'),
							array('rule' => 'numeric')
						)
					)
					) $json['errors']["option_value[$i][sort_order]"] = $validator->get_errors();

					$i++;
				}
			}

			// add numeric validation for sort order
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
				if (empty($data['option_description'][$language_id]['name'])) $data['option_description'][$language_id]['name'] = $data['option_description'][$defaultLanguageId]['name'];

				if (isset($data['option_value'])) {
					foreach ($data['option_value'] as $key => $option_value) {
						if (empty($option_value['option_value_description'][$language_id]['name'])) $data['option_value'][$key]['option_value_description'][$language_id]['name'] = $option_value['option_value_description'][$defaultLanguageId]['name'];
					}
				}
			}
		}

		// return if errors
		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		// Validate status field
		if(!isset($data['option_status'])) $data['option_status'] = MsOption::STATUS_DISABLED;

		// Seller
		$data['seller_id'] = $this->customer->getId();
		$seller = $this->MsLoader->MsSeller->getSeller($data['seller_id']);

		// Finish
		if (isset($data['option_id']) && !empty($data['option_id'])) {
			$this->MsLoader->MsOption->sellerUpdateOption($data['option_id'], $data);
			$this->session->data['success'] = $this->language->get('ms_success_option_updated');
		} else {
			$this->MsLoader->MsOption->sellerCreateOption($data);
			$this->session->data['success'] = $this->language->get('ms_success_option_created');

			$MailOptionCreated = $serviceLocator->get('MailOptionCreated', false)
				->setTo($MultiMerchModule->getNotificationEmail())
				->setData(array(
					'addressee' => $this->config->get('config_owner'),
					'slr_name' => $seller['ms.nickname'],
					'opt_name' => $data['option_description'][$defaultLanguageId]['name'],
					'opt_href' => $this->MsLoader->MsHelper->adminUrlLink('multimerch/option', '', true)
				));
			$mails->add($MailOptionCreated);
		}

		if ($mails->count()) {
			$mailTransport->sendMails($mails);
		}

		$json['redirect'] = $this->url->link('seller/account-option', '', 'SSL');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function _initOptionForm() {
		$this->_validateCall();

		$this->load->model('localisation/language');
		$this->document->addScript('catalog/view/javascript/multimerch/account-option.js');
		$this->MsLoader->MsHelper->addStyle('multimerch/flags');

		$seller_id = $this->customer->getId();

		// @todo Rebuild default option types array
		$this->data['option_types'] = array(
			'choose' => array('select', 'radio', 'checkbox'),
			'input' => array('text', 'textarea'),
			'file' => array('file'),
			'date' => array('date', 'time', 'datetime'),
		);

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['back'] = $this->url->link('seller/account-option', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_option_heading'));
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
				'text' => $this->language->get('ms_account_option_breadcrumbs'),
				'href' => $this->url->link('seller/account-option', '', 'SSL'),
			)
		));
	}

	public function createOption() {
		$this->_validateCall();

		$this->_initOptionForm();

		$this->data['option'] = FALSE;
		$this->data['option_values'] = FALSE;
		
		$this->data['heading'] = $this->language->get('ms_account_newoption_heading');
		$this->document->setTitle($this->language->get('ms_account_newoption_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-option-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function updateOption() {
		$this->_validateCall();

		$option_id = isset($this->request->get['option_id']) ? (int)$this->request->get['option_id'] : 0;

		if ($this->MsLoader->MsOption->isMsOption($option_id)) {
			$option_info = $this->MsLoader->MsOption->getOptions(array('option_id' => $option_id, 'single' => 1));
		} else {
			return $this->response->redirect($this->url->link('seller/account-option', '', 'SSL'));
		}

		$this->_initOptionForm();

		$this->data['option'] = $option_info;
		$this->data['option_values'] = $this->MsLoader->MsOption->getOptionValues($option_id);

		$this->data['heading'] = $this->language->get('ms_account_editoption_heading');
		$this->document->setTitle($this->language->get('ms_account_editoption_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-option-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function deleteOption() {
		$this->_validateCall();

		$json = array();
		$option_id = isset($this->request->get['option_id']) ? (int)$this->request->get['option_id'] : 0;

		if($option_id) {
			// check if any products are attached
			$product_total = $this->MsLoader->MsOption->ocGetTotalProductsByOptionId($option_id);

			if ($product_total) {
				$json['error'] = sprintf($this->language->get('ms_error_option_assigned_to_products'), $product_total);
			} else {
				$this->MsLoader->MsOption->deleteOption($option_id);
				$this->session->data['success'] = $this->language->get('ms_success_option_deleted');
			}
		} else {
			$json['error'] = $this->language->get('ms_error_option_id');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function activateOption() {
		$this->_validateCall();

		$option_id = isset($this->request->get['option_id']) ? $this->request->get['option_id'] : false;
		$seller_id = $this->customer->getId();

		if($option_id && $this->MsLoader->MsOption->isMsOption($option_id, array('seller_id' => $seller_id))) {
			$this->MsLoader->MsOption->sellerActivateOption($option_id);
			$this->session->data['success'] = $this->language->get('ms_success_option_activated');
		}

		$this->response->redirect($this->url->link('seller/account-option', '', 'SSL'));
	}

	public function deactivateOption() {
		$this->_validateCall();

		$option_id = isset($this->request->get['option_id']) ? $this->request->get['option_id'] : false;
		$seller_id = $this->customer->getId();

		if($option_id && $this->MsLoader->MsOption->isMsOption($option_id, array('seller_id' => $seller_id))) {
			$this->MsLoader->MsOption->sellerDeactivateOption($option_id);
			$this->session->data['success'] = $this->language->get('ms_success_option_deactivated');
		}

		$this->response->redirect($this->url->link('seller/account-option', '', 'SSL'));
	}
}
