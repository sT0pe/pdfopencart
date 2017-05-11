<?php

class ControllerMultimerchCategory extends ControllerMultimerchBase {

	public function index() {
		$this->validate(__FUNCTION__);

		$this->document->addScript('view/javascript/multimerch/category.js');

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

		$this->data['heading'] = $this->language->get('ms_seller_category_heading');
		$this->document->setTitle($this->language->get('ms_seller_category_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_seller_category_breadcrumbs'),
				'href' => $this->url->link('multimerch/category', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/category', $this->data));
	}


	/************************************************************/


	// Categories

	public function getCategoryTableData() {
		$colMap = array(
			'id' => 'msc.category_id',
			'name' => 'mscd.name',
			'seller' => 'mss.nickname',
			'status' => 'msc.category_status',
			'sort_order' => 'msc.sort_order'
		);

		$sorts = array('name', 'seller', 'status', 'sort_order');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$statuses = array();
		$msCategory = new ReflectionClass('MsCategory');
		foreach ($msCategory->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}
		}

		$results = $this->MsLoader->MsCategory->getCategories(
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
			if($result['category_status'] == MsCategory::STATUS_DISABLED) {
				$actions .= "<button type='button' class='btn btn-success ms-cat-change-status ms-spinner' data-status='" . MsCategory::STATUS_ACTIVE . "' data-toggle='tooltip' title='' data-original-title='" . $this->language->get('ms_button_approve') . "'><i class='fa fa-check'></i></button>";
			}
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/category/update', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<button type='button' class='btn btn-danger ms-cat-delete' title='".$this->language->get('button_delete')."'><i class='fa fa-trash-o''></i></button>";

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['category_id']}' />",
					'name' => ($result['path'] ? $result['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' : '') . $result['name'],
					'seller' => $this->MsLoader->MsSeller->getSellerNickname($result['seller_id']),
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

	public function create() {
		$this->document->setTitle($this->language->get('ms_seller_newcategory_heading'));
		$this->_initCategoryForm();

		$this->data['category'] = FALSE;
		$this->data['heading'] = $this->language->get('ms_seller_newcategory_heading');

		$this->response->setOutput($this->load->view('multiseller/category-form', $this->data));
	}

	public function update() {
		$category_id = isset($this->request->get['category_id']) ? (int)$this->request->get['category_id'] : 0;

		if ($this->MsLoader->MsCategory->isMsCategory($category_id)) {
			$category = $this->MsLoader->MsCategory->getCategories(array('category_id' => $category_id, 'single' => 1));
		} else {
			return $this->response->redirect($this->url->link('multimerch/category', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->document->setTitle($this->language->get('ms_seller_editcategory_heading'));
		$this->_initCategoryForm();

		// name, description, metas
		foreach ($category['languages'] as $id => $l) {
			$category['cat_name'][$id] = $l['name'];
			$category['cat_description'][$id] = ($this->config->get('msconf_enable_rte') ? htmlspecialchars_decode($l['description']) : strip_tags(htmlspecialchars_decode($l['description'])));
			$category['cat_meta_keyword'][$id] = $l['meta_keyword'];
			$category['cat_meta_description'][$id] = $l['meta_description'];
			$category['cat_meta_title'][$id] = $l['meta_title'];
		}

		// image
		if($category['image']) $this->data['thumb'] = $this->model_tool_image->resize($category['image'], 100, 100);

		$this->data['category'] = $category;
		$this->data['heading'] = $this->language->get('ms_seller_editcategory_heading');

		$this->response->setOutput($this->load->view('multiseller/category-form', $this->data));
	}

	private function _initCategoryForm() {
		$this->document->addScript('view/javascript/summernote/summernote.js');
		$this->document->addScript('view/javascript/summernote/opencart.js');
		$this->document->addStyle('view/javascript/summernote/summernote.css');

		$this->document->addScript('view/javascript/multimerch/category-form.js');

		// languages
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		// sellers
		// @todo error message if no sellers
		$this->data['sellers'] = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_status' => array(MsSeller::STATUS_ACTIVE, MsSeller::STATUS_INACTIVE)
			),
			array(
				'order_by'  => 'ms.nickname',
				'order_way' => 'ASC'
			)
		);

		// stores
		$this->load->model('setting/store');
		$this->data['stores'] = $this->model_setting_store->getStores();

		// image
		$this->load->model('tool/image');
		$this->data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$this->data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// statuses
		$statuses = array();
		$msCategory = new ReflectionClass('MsCategory');
		foreach ($msCategory->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}
		}
		$this->data['statuses'] = $statuses;

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_seller_category_breadcrumbs'),
				'href' => $this->url->link('multimerch/category', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
	}

	public function jxSaveCategory() {
		$json = array();

		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();
		$validator = $this->MsLoader->MsValidator;

		$data = $this->request->post;

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$defaultLanguageId = $this->config->get('config_language_id');

		// validate primary language
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$primary = true;
			if($language_id != $defaultLanguageId)
				$primary = false;

			// validate category name
			if(!$validator->validate(array(
					'name' => $this->language->get('ms_seller_category_name'),
					'value' => $data['category_description'][$language_id]['name']
				),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'max_len,100')
				)
			)) $json['errors']["category_description[$language_id][name]"] = $validator->get_errors();

			if(!$primary) {
				if (empty($data['category_description'][$language_id]['name'])) $data['category_description'][$language_id]['name'] = $data['category_description'][$defaultLanguageId]['name'];
			}
		}

		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		if (empty($data['category_id'])) {
			if(!isset($data['status'])) $data['status'] = MsCategory::STATUS_ACTIVE;

			// create new category
			$this->MsLoader->MsCategory->createCategory($data);

			// @todo mails

			$this->session->data['success'] = $this->language->get('ms_seller_category_created');
		} else {
			// update existing category
			$this->MsLoader->MsCategory->updateCategory($data['category_id'], $data);

			// @todo mails

			$this->session->data['success'] = $this->language->get('ms_seller_category_updated');
		}

		$json['redirect'] = $this->url->link('multimerch/category', 'token=' . $this->session->data['token'], true);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxChangeSeller() {
		$json = array();

		if(!isset($this->request->get['category_id']) && !isset($this->request->get['seller_id'])) {
			$json['error'] = $this->language->get('ms_seller_category_error_updating');
		}

		if(!isset($json['error'])) {
			$this->MsLoader->MsCategory->changeSeller($this->request->get['category_id'], $this->request->get['seller_id']);

			$this->session->data['success'] = $this->language->get('ms_seller_category_updated');
			$json['redirect'] = $this->url->link('multimerch/category', 'token=' . $this->session->data['token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxChangeStatus() {
		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$json = array();

		if(!isset($this->request->get['category_id']) && !isset($this->request->post['selected_categories']) && !isset($this->request->get['category_status'])) {
			$json['error'] = $this->language->get('ms_seller_category_error_updating');
		}

		if(!isset($json['error'])) {
			$category_ids = isset($this->request->get['category_id']) ?
				array($this->request->get['category_id']) :
				(isset($this->request->post['selected_categories']) ? $this->request->post['selected_categories'] : array());

			foreach ($category_ids as $category_id) {
				$category_info = $this->MsLoader->MsCategory->getCategories(array('category_id' => $category_id, 'single' => 1));
				$seller = isset($category_info['seller_id']) ? $this->MsLoader->MsSeller->getSeller($category_info['seller_id']) : FALSE;

				if ($seller) {
					$MailCategoryStatusChanged = $serviceLocator->get('MailCategoryStatusChanged', false)
						->setTo($seller['c.email'])
						->setData(array(
							'addressee' => $seller['ms.nickname'],
							'cat_name' => $category_info['name'],
							'cat_status' => $this->language->get('ms_seller_category_status_' . $this->request->get['category_status'])
						));
					$mails->add($MailCategoryStatusChanged);
				}

				$this->MsLoader->MsCategory->changeStatus($category_id, $this->request->get['category_status']);
			}

			if ($mails->count()) {
				$mailTransport->sendMails($mails);
			}

			$this->session->data['success'] = $this->language->get('ms_seller_category_updated');
			$json['redirect'] = $this->url->link('multimerch/category', 'token=' . $this->session->data['token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteCategory() {
		$json = array();

		if(!isset($this->request->get['category_id']) && !isset($this->request->post['selected_categories'])) {
			$json['error'] = $this->language->get('ms_seller_category_error_deleting');
		}

		if(!isset($json['error'])) {
			$category_ids = isset($this->request->get['category_id']) ?
				array($this->request->get['category_id']) :
				(isset($this->request->post['selected_categories']) ? $this->request->post['selected_categories'] : array());

			foreach ($category_ids as $category_id) {
				$this->MsLoader->MsCategory->deleteCategory($category_id);
			}

			$this->session->data['success'] = $this->language->get('ms_seller_category_deleted');
			$json['redirect'] = $this->url->link('multimerch/category', 'token=' . $this->session->data['token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxAutocompleteCategories() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/category');

			$data = array(
				'seller_ids' => $this->customer->getId(),
				'category_status' => MsCategory::STATUS_ACTIVE
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

	public function jxAutocompleteFilters() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/filter');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$filters = $this->model_catalog_filter->getFilters($filter_data);

			foreach ($filters as $filter) {
				$json[] = array(
					'filter_id' => $filter['filter_id'],
					'name'      => strip_tags(html_entity_decode($filter['group'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8'))
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
}
