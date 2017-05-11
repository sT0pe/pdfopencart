<?php

class ControllerMultimerchOption extends ControllerMultimerchBase {

	public function index() {
		$this->validate(__FUNCTION__);

		$this->document->addScript('view/javascript/multimerch/option.js');

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

		$this->data['heading'] = $this->language->get('ms_seller_option_heading');
		$this->document->setTitle($this->language->get('ms_seller_option_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_seller_option_breadcrumbs'),
				'href' => $this->url->link('multimerch/option', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/option', $this->data));
	}


	/************************************************************/


	// Options

	public function getTableData() {
		$colMap = array(
			'id' => 'mso.option_id',
			'name' => 'od.name',
			'seller' => 'mss.nickname',
			'status' => 'mso.option_status',
			'sort_order' => 'o.sort_order'
		);

		$sorts = array('name', 'seller', 'status', 'sort_order');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$sellers = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_status' => array(MsSeller::STATUS_ACTIVE, MsSeller::STATUS_INACTIVE)
			),
			array(
				'order_by'  => 'ms.nickname',
				'order_way' => 'ASC'
			)
		);

		$statuses = array();
		$msOption = new ReflectionClass('MsOption');
		foreach ($msOption->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}
		}

		$results = $this->MsLoader->MsOption->getOptions(
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
			// seller selection
			$seller_select = "";
			$seller_select .= "<select class='form-control'>";
			$seller_select .= "	<option value='0'>" . $this->language->get('ms_global_option') . "</option>";
			foreach($sellers as $s) {
				$seller_select .= "<option value='{$s['seller_id']}'" . ($s['seller_id'] == $result['seller_id'] ? " selected='selected'" : "") . ">{$s['ms.nickname']}</option>";
			}
			$seller_select .= "</select>";
			$seller_select .= "<button type='button' data-toggle='tooltip' title='' class='btn btn-primary ms-assign-seller ms-spinner' data-original-title='" . $this->language->get('ms_apply') . "'><i class='fa fa-check'></i></button>";

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
			if($result['option_status'] == MsOption::STATUS_DISABLED) {
				$actions .= "<button type='button' class='btn btn-success ms-change-status ms-spinner' data-status='" . MsOption::STATUS_ACTIVE . "' data-toggle='tooltip' title='' data-original-title='" . $this->language->get('ms_button_approve') . "'><i class='fa fa-check'></i></button>";
			}
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('catalog/option/edit', 'token=' . $this->session->data['token'] . '&option_id=' . $result['option_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<button type='button' class='btn btn-danger ms-delete' title='".$this->language->get('button_delete')."'><i class='fa fa-trash-o''></i></button>";

			$option_values = $this->MsLoader->MsOption->getOptionValues($result['option_id']);
			$values_list = "";
			foreach ($option_values as $option_value) {
				$values_list .= $option_value['name'] . ($option_value !== end($option_values) ? ", " : "");
			}

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['option_id']}' />",
					'name' => $result['name'],
					'values' => (mb_strlen($values_list) > 40 ? mb_substr($values_list, 0, 40) . '...' : $values_list),
					'seller' => $seller_select,
					'sort_order' => $result['sort_order'],
					'status' => $status,
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

	public function jxUpdateOption() {
		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$json = array();

		if(!isset($this->request->get['option_id']) && !isset($this->request->post['selected_options']) && (!isset($this->request->get['option_status']) || !isset($this->request->get['seller_id']))) {
			$json['error'] = $this->language->get('ms_seller_option_error_updating');
		}

		if(!isset($json['error'])) {
			$option_ids = isset($this->request->get['option_id']) ?
				array($this->request->get['option_id']) :
				(isset($this->request->post['selected_options']) ? $this->request->post['selected_options'] : array());

			foreach ($option_ids as $option_id) {
				$params = array();

				$option_info = $this->MsLoader->MsOption->getOptions(array('option_id' => $option_id, 'single' => 1));
				$seller = isset($option_info['seller_id']) ? $this->MsLoader->MsSeller->getSeller($option_info['seller_id']) : FALSE;

				if(isset($this->request->get['option_status'])) {
					$params['option_status'] = $this->request->get['option_status'];

					if($seller) {
						$MailOptionStatusChanged = $serviceLocator->get('MailOptionStatusChanged', false)
							->setTo($seller['c.email'])
							->setData(array(
								'addressee' => $seller['ms.nickname'],
								'opt_name' => $option_info['name'],
								'opt_status' => $this->language->get('ms_seller_option_status_' . $this->request->get['option_status'])
							));
						$mails->add($MailOptionStatusChanged);
					}
				}

				if(isset($this->request->get['seller_id'])) {
					$params['seller_id'] = $this->request->get['seller_id'];

					/*if($this->request->get['seller_id']) {
						if($seller) {
							// Mail old seller to let him know his option has been detached from him

							$MailOptionSellerChanged = $serviceLocator->get('MailOptionSellerChanged', false)
								->setTo($seller['c.email'])
								->setData(array(
									'addressee' => $seller['ms.nickname'],
									'opt_name' => $option_info['name'],
									'action' => 1
								));
							$mails->add($MailOptionSellerChanged);
						}

						$new_seller = $this->MsLoader->MsSeller->getSeller($this->request->get['seller_id']);

						if($new_seller) {
							// Mail new seller

							$MailOptionSellerChanged = $serviceLocator->get('MailOptionSellerChanged', false)
								->setTo($new_seller['c.email'])
								->setData(array(
									'addressee' => $new_seller['ms.nickname'],
									'opt_name' => $option_info['name'],
									'action' => 2
								));
							$mails->add($MailOptionSellerChanged);
						}
					} else {
						$MailOptionConvertedToGlobal = $serviceLocator->get('MailOptionConvertedToGlobal', false)
							->setTo($seller['c.email'])
							->setData(array(
								'addressee' => $seller['ms.nickname'],
								'opt_name' => $option_info['name']
							));
						$mails->add($MailOptionConvertedToGlobal);
					}*/
				}

				$this->MsLoader->MsOption->createOrUpdateMsOption($option_id, $params);
			}

			if ($mails->count()) {
				$mailTransport->sendMails($mails);
			}

			$this->session->data['success'] = $this->language->get('ms_seller_option_updated');
			$json['success'] = $this->language->get('ms_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteOption() {
		$json = array();

		if(!isset($this->request->get['option_id']) && !isset($this->request->post['selected_options'])) {
			$json['errors'][] = $this->language->get('ms_seller_option_error_deleting');
		}

		if(!isset($json['errors'])) {
			$option_ids = isset($this->request->get['option_id']) ?
				array($this->request->get['option_id']) :
				(isset($this->request->post['selected_options']) ? $this->request->post['selected_options'] : array());

			foreach ($option_ids as $option_id) {
				$option_info = $this->MsLoader->MsOption->getOptions(array('option_id' => $option_id, 'single' => 1));

				$this->load->model('catalog/product');
				$product_total = $this->model_catalog_product->getTotalProductsByOptionId($option_id);

				if ($product_total) {
					$json['errors'][] = sprintf($this->language->get('ms_seller_option_error_assigned'), $option_info['name'], $product_total);
				} else {
					$this->MsLoader->MsOption->deleteOption($option_id);

					$this->session->data['success'] = $this->language->get('ms_seller_option_deleted');
					$json['success'] = $this->language->get('ms_success');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>