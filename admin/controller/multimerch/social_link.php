<?php
class ControllerMultimerchSocialLink extends ControllerMultimerchBase {
	private $error = array();
	
	public function __construct($registry) {
		parent::__construct($registry);
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
	}
	
	public function getTableData() {
		$colMap = array(
			'id' => 'mc.channel_id'
		);

		$this->load->model('tool/image');
		$sorts = array('id', 'name', 'description');
		$filters = $sorts;
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsSocialLink->getChannels(
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
			// image
			$image = $this->model_tool_image->resize($result['image'], 50, 50);
			
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/social_link/update', 'token=' . $this->session->data['token'] . '&channel_id=' . $result['channel_id'], 'SSL') . "' data-toggle='tooltip' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil'></i></a> ";
			$actions .= "<a class='btn btn-danger' href='" . $this->url->link('multimerch/social_link/delete', 'token=' . $this->session->data['token'] . '&channel_id=' . $result['channel_id'], 'SSL') . "' data-toggle='tooltip' title='".$this->language->get('button_delete')."'><i class='fa fa-trash-o'></i></a> ";

			$columns[] = array_merge(
				$result,
				array(
					'id' => $result['channel_id'],
					'name' => $result['name'],
					'description' => (mb_strlen($result['description']) > 80 ? mb_substr($result['description'], 0, 80) . '...' : $result['description']),
					'image' => "<img src='$image' />",
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
	
	public function index() {
		$this->validate(__FUNCTION__);
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_sl'),
				'href' => $this->url->link('multimerch/social_link', '', 'SSL'),
			)
		));
		
		$this->data['insert'] = $this->url->link('multimerch/social_link/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('multimerch/social_link/delete', 'token=' . $this->session->data['token'], 'SSL');
	
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
		$this->data['heading'] = $this->language->get('ms_sl');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->document->setTitle($this->language->get('ms_sl'));
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view("multiseller/social_link.tpl", $this->data));
	}
	
	// Insert a new channel
	public function insert() {
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_sl_create');
		$this->document->setTitle($this->language->get('ms_sl_create'));

		$this->load->model('tool/image');
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$this->data['cancel'] = $this->url->link('multimerch/social_link', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$this->data['channel'] = NULL;
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_sl_create'),
				'href' => $this->url->link('multimerch/social_link', '', 'SSL'),
			)
		));		
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view("multiseller/social_link_form.tpl", $this->data));
	}
	
	// Update channel
	public function update() {
		$this->validate(__FUNCTION__);
		
		$this->load->model('tool/image');
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_sl_update');
		$this->document->setTitle($this->language->get('ms_sl_update'));
		
		$this->data['cancel'] = $this->url->link('multimerch/social_link', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$channel = $this->MsLoader->MsSocialLink->getChannels(array('single' => 1, 'channel_id' => $this->request->get['channel_id']), array());

		$this->data['channel'] = array(
			'channel_id' => $channel['channel_id'],
			'description' => $this->MsLoader->MsSocialLink->getChannelDescriptions($this->request->get['channel_id']),
			'image' => $channel['image'],
			'image_resized' => $this->model_tool_image->resize($channel['image'], 50, 50)
		);
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_sl_update'),
				'href' => $this->url->link('multimerch/social_link', '', 'SSL'),
			)
		));		
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view("multiseller/social_link_form.tpl", $this->data));
	}
	
	// Delete sls
	public function delete() { 
		if (isset($this->request->get['channel_id'])) $this->request->post['selected'] = array($this->request->get['channel_id']);
		
		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $channel_id) {
				$this->MsLoader->MsSocialLink->deleteChannel($channel_id);
			}
			
			$this->session->data['success'] = $this->language->get('ms_success');
		}
		
		$this->response->redirect($this->url->link('multimerch/social_link', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	// Get form for adding/editing
	private function getEditForm()
	{
		$this->data['heading'] = $this->language->get('ms_sl_create');

		if (!isset($this->request->get['channel_id'])) {
			$this->data['action'] = $this->url->link('multimerch/social_link/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('multimerch/social_link/update', 'token=' . $this->session->data['token'] . '&channel_id=' . $this->request->get['channel_id'] . $url, 'SSL');
		}

		$this->data['cancel'] = $this->url->link('multimerch/social_link', 'token=' . $this->session->data['token'] . $url, 'SSL');


		if (isset($this->request->post['channel_description'])) {
			$this->data['channel_description'] = $this->request->post['channel_description'];
		} elseif (isset($this->request->get['channel_id'])) {
			$this->data['channel_description'] = 'a';
		} else {
			$this->data['channel_description'] = array();
		}

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view("multiseller/social_link_form.tpl", $this->data));
	}

	// Validate delete
	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'multiseller/social_link')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function jxSave() {
		$data = $this->request->post['channel'];
		$json = array();

		foreach ($data['description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$json['errors']['channel[description]['.$language_id.'][name]'] = $this->language->get('ms_error_channel_name');
			}
		}
		
		if (empty($json['errors'])) {
			if (empty($data['channel_id'])) {
				$this->MsLoader->MsSocialLink->createChannel($data);
				$this->session->data['success'] = $this->language->get('ms_success_channel_created');
			} else {
				$this->MsLoader->MsSocialLink->editChannel($data['channel_id'], $data);
				$this->session->data['success'] = $this->language->get('ms_success_channel_updated');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>