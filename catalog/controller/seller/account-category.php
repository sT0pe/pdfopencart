<?php

class ControllerSellerAccountCategory extends ControllerSellerAccount {

	// General

	public function index() {
		$this->_validateCall();

		$this->document->addScript('catalog/view/javascript/multimerch/account-category.js');
		$this->document->setTitle($this->language->get('ms_account_category_breadcrumbs'));

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
				'text' => $this->language->get('ms_account_category_breadcrumbs'),
				'href' => $this->url->link('seller/account-category', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-category');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	private function _validateCall() {
		if(!$this->config->get('msconf_allow_seller_categories')) {
			return $this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
	}


	/************************************************************/


	// Categories

	public function getTableData() {
		$this->_validateCall();

		$colMap = array(
			'id' => 'msc.category_id',
			'name' => 'mscd.name',
			'status' => 'msc.category_status',
			'sort_order' => 'msc.sort_order'
		);

		$sorts = array('name', 'status', 'sort_order');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsCategory->getCategories(
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
			if(isset($result['category_status'])) {
				$status .= "<p style='color: ";

				if($result['category_status'] == MsCategory::STATUS_APPROVED) $status .= "blue";
				if($result['category_status'] == MsCategory::STATUS_ACTIVE) $status .= "green";
				if($result['category_status'] == MsCategory::STATUS_INACTIVE || $result['category_status'] == MsCategory::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_seller_category_status_' . $result['category_status']) . "</p>";
			}

			// actions
			$actions = "";
			if ($result['category_status'] == MsCategory::STATUS_INACTIVE || $result['category_status'] == MsCategory::STATUS_APPROVED)
				$actions .= "<a class='icon-publish' href='" . $this->url->link('seller/account-category/activate', 'category_id=' . $result['category_id'], 'SSL') ."' title='" . $this->language->get('ms_activate') . "'><i class='fa fa-plus'></i></a>";

			if ($result['category_status'] == MsCategory::STATUS_ACTIVE)
				$actions .= "<a class='icon-unpublish' href='" . $this->url->link('seller/account-category/deactivate', 'category_id=' . $result['category_id'], 'SSL') ."' title='" . $this->language->get('ms_deactivate') . "'><i class='fa fa-minus'></i></a>";

			$actions .= "<a class='icon-edit' href='" . $this->url->link('seller/account-category/update', 'category_id=' . $result['category_id'], 'SSL') ."' title='" . $this->language->get('ms_edit') . "'><i class='fa fa-pencil'></i></a>";
			$actions .= "<a class='icon-remove ms_remove_category' href='" . $this->url->link('seller/account-category/delete', 'category_id=' . $result['category_id'], 'SSL') ."' title='" . $this->language->get('ms_delete') . "'><i class='fa fa-times'></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'name' => strip_tags(html_entity_decode(($result['path'] ? $result['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' : '') . $result['name'], ENT_QUOTES, 'UTF-8')),
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

	public function jxSaveCategory() {
		$this->_validateCall();

		$MultiMerchModule = $this->MsLoader->load('\MultiMerch\Module\MultiMerch');
		$serviceLocator = $MultiMerchModule->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$data = $this->request->post;

		// Validate category is owned by seller
		if (isset($data['category_id']) && !empty($data['category_id'])) {
			if (!$this->MsLoader->MsCategory->isMsCategory($data['category_id'], array('seller_id' => $this->customer->getId()))) {
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
			// category name
			if (!$validator->validate(array(
				'name' => $this->language->get('ms_account_category_name'),
				'value' => $data['category_description'][$language_id]['name']
			),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'max_len,100')
				)
			)
			) $json['errors']["category_description[$language_id][name]"] = $validator->get_errors();

			// add numeric validation for sort order
			if (!$validator->validate(array(
				'name' => $this->language->get('ms_sort_order'),
				'value' => $data['sort_order']
			),
				array(
//					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'numeric')
				)
			)
			) $json['errors']["sort_order"] = $validator->get_errors();

			// Copy fields data from main language
			if(!$primary) {
				if (empty($data['category_description'][$language_id]['name'])) $data['category_description'][$language_id]['name'] = $data['category_description'][$defaultLanguageId]['name'];
				if (empty($data['category_description'][$language_id]['meta_title'])) $data['category_description'][$language_id]['meta_title'] = $data['category_description'][$defaultLanguageId]['meta_title'];
				if (empty($data['category_description'][$language_id]['meta_description'])) $data['category_description'][$language_id]['meta_description'] = $data['category_description'][$defaultLanguageId]['meta_description'];
				if (empty($data['category_description'][$language_id]['meta_keyword'])) $data['category_description'][$language_id]['meta_keyword'] = $data['category_description'][$defaultLanguageId]['meta_keyword'];
			}
		}

		// return if errors
		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		// Validate status field
		if(!isset($data['status'])) $data['status'] = MsCategory::STATUS_DISABLED;

		// Validate seo keyword
		$data['keyword'] = $this->MsLoader->MsHelper->slugify($data['keyword']);

		// Seller
		$data['seller_id'] = $this->customer->getId();
		$seller = $this->MsLoader->MsSeller->getSeller($data['seller_id']);

		// Finish
		if (isset($data['category_id']) && !empty($data['category_id'])) {
			$this->MsLoader->MsCategory->updateCategory($data['category_id'], $data);
			$this->session->data['success'] = $this->language->get('ms_success_category_updated');
		} else {
			$this->MsLoader->MsCategory->createCategory($data);
			$this->session->data['success'] = $this->language->get('ms_success_category_created');

			$MailCategoryCreated = $serviceLocator->get('MailCategoryCreated', false)
				->setTo($MultiMerchModule->getNotificationEmail())
				->setData(array(
					'addressee' => $this->config->get('config_owner'),
					'slr_name' => $seller['ms.nickname'],
					'cat_name' => $data['category_description'][$defaultLanguageId]['name'],
					'cat_href' => $this->MsLoader->MsHelper->adminUrlLink('multimerch/category', '', true)
				));
			$mails->add($MailCategoryCreated);
		}

		if ($mails->count()) {
			$mailTransport->sendMails($mails);
		}

		$json['redirect'] = $this->url->link('seller/account-category', '', 'SSL');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxAutocompleteCategories() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/category');

			$data = array(
				'seller_ids' => $this->customer->getId(),
//				'category_status' => MsCategory::STATUS_ACTIVE
			);

			$filter_data = array(
				'filters' => array(
					'mscd.name' => $this->request->get['filter_name']
				),
				'order_by' => 'mscd.name',
				'order_way' => 'ASC',
				'offset' => 0,
				'limit' => 5
			);

			$results = $this->MsLoader->MsCategory->getCategories($data, $filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode(($result['path'] ? $result['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' : '') . $result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function _initCategoryForm() {
		$this->_validateCall();

		$this->load->model('localisation/language');
		$this->document->addScript('catalog/view/javascript/multimerch/account-category-form.js');
		$this->MsLoader->MsHelper->addStyle('multimerch/flags');

		// rte
		if ($this->config->get('msconf_enable_rte')) {
			$this->document->addScript('catalog/view/javascript/multimerch/ckeditor/ckeditor.js');
		}

		// image
		$this->load->model('tool/image');
		$this->data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$this->data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['back'] = $this->url->link('seller/account-category', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_category_heading'));
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
				'text' => $this->language->get('ms_account_category_breadcrumbs'),
				'href' => $this->url->link('seller/account-category', '', 'SSL'),
			)
		));
	}

	public function create() {
		$this->_validateCall();

		$this->_initCategoryForm();

		$this->data['category'] = FALSE;

		$this->data['heading'] = $this->language->get('ms_account_newcategory_heading');
		$this->document->setTitle($this->language->get('ms_account_newcategory_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-category-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function update() {
		$this->_validateCall();

		$category_id = isset($this->request->get['category_id']) ? (int)$this->request->get['category_id'] : 0;

		if ($this->MsLoader->MsCategory->isMsCategory($category_id)) {
			$category = $this->MsLoader->MsCategory->getCategories(array('category_id' => $category_id, 'single' => 1));
		} else {
			return $this->response->redirect($this->url->link('seller/account-category', '', 'SSL'));
		}

		$this->_initCategoryForm();

		// name, description, metas
		foreach ($category['languages'] as $id => $l) {
			$category['cat_name'][$id] = $l['name'];
			$category['cat_description'][$id] = ($this->config->get('msconf_enable_rte') ? htmlspecialchars_decode($l['description']) : strip_tags(htmlspecialchars_decode($l['description'])));
			$category['cat_meta_keyword'][$id] = $l['meta_keyword'];
			$category['cat_meta_description'][$id] = $l['meta_description'];
			$category['cat_meta_title'][$id] = $l['meta_title'];
		}

		if($category['image']) $this->data['thumb'] = $this->model_tool_image->resize($category['image'], 100, 100);

		$this->data['category'] = $category;

		$this->data['heading'] = $this->language->get('ms_account_editcategory_heading');
		$this->document->setTitle($this->language->get('ms_account_editcategory_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-category-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function delete() {
		$this->_validateCall();

		$json = array();
		$category_id = isset($this->request->get['category_id']) ? (int)$this->request->get['category_id'] : 0;

		if($category_id) {
			$this->MsLoader->MsCategory->deleteCategory($category_id);
			$this->session->data['success'] = $this->language->get('ms_success_category_deleted');
		} else {
			$json['error'] = $this->language->get('ms_error_category_id');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function activate() {
		$this->_validateCall();

		$category_id = isset($this->request->get['category_id']) ? $this->request->get['category_id'] : false;
		$seller_id = $this->customer->getId();

		if($category_id && $this->MsLoader->MsCategory->isMsCategory($category_id, array('seller_id' => $seller_id))) {
			$this->MsLoader->MsCategory->changeStatus($category_id, MsCategory::STATUS_ACTIVE);
			$this->session->data['success'] = $this->language->get('ms_success_category_activated');
		}

		$this->response->redirect($this->url->link('seller/account-category', '', 'SSL'));
	}

	public function deactivate() {
		$this->_validateCall();

		$category_id = isset($this->request->get['category_id']) ? $this->request->get['category_id'] : false;
		$seller_id = $this->customer->getId();

		if($category_id && $this->MsLoader->MsCategory->isMsCategory($category_id, array('seller_id' => $seller_id))) {
			$this->MsLoader->MsCategory->changeStatus($category_id, MsCategory::STATUS_INACTIVE);
			$this->session->data['success'] = $this->language->get('ms_success_category_deactivated');
		}

		$this->response->redirect($this->url->link('seller/account-category', '', 'SSL'));
	}
}
