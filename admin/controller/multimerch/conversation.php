<?php

class ControllerMultimerchConversation extends ControllerMultimerchBase {
	public function getTableData() {
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$colMap = array(
			'last_message_date' => 'last_message_date'
		);

		$sorts = array('last_message_date');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$conversations = $this->MsLoader->MsConversation->getConversations(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($conversations[0]) ? $conversations[0]['total_rows'] : 0;

		$columns = array();
		foreach ($conversations as $conversation) {
			// Actions
			$actions = "";
			$actions .= "<a class='btn btn-info' class='ms-button' href='" . $this->url->link('multimerch/conversation/view', 'token=' . $this->session->data['token'] . '&conversation_id=' . $conversation["conversation_id"], 'SSL') . "' title='".$this->language->get('ms_view')."'><i class='fa fa-search'></i></a>";
			$actions .= "<a class='btn btn-danger ms-button-delete' style='background-image: none;' href='" . $this->url->link('multimerch/conversation/delete', 'token=' . $this->session->data['token'] . '&conversation_id=' . $conversation["conversation_id"], 'SSL') . "' title='".$this->language->get('ms_delete')."'><i class='fa fa-trash-o'></i></a>";

			$columns[] = array_merge(
				$conversation,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$conversation['conversation_id']}' />",
					'title' => (mb_strlen($conversation['title']) > 80 ? mb_substr($conversation['title'], 0, 80) . '...' : $conversation['title']),
					'date_created' => date($this->language->get('date_format_short'), strtotime($conversation['date_created'])),
					'last_message_date' => date($this->language->get('datetime_format'), strtotime($conversation['last_message_date'])),
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
		$this->document->addScript('//code.jquery.com/ui/1.11.2/jquery-ui.min.js');
		$this->validate(__FUNCTION__);
		$this->data['add'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
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
		$this->data['heading'] = $this->language->get('ms_account_conversations');
		$this->document->setTitle($this->language->get('ms_account_conversations'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_conversations'),
				'href' => $this->url->link('multimerch/conversation', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/conversation.tpl', $this->data));
	}

	public function view() {
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}

		$conversation_id = isset($this->request->get['conversation_id']) ? $this->request->get['conversation_id'] : false;

		$conversation = $this->MsLoader->MsConversation->getConversations(array(
			'conversation_id' => $conversation_id,
			'single' => 1
		));

		$this->data['conversation'] = !empty($conversation) ? $conversation : false;

		$messages = $this->MsLoader->MsMessage->getMessages(
			array(
				'conversation_id' => $conversation_id
			),
			array(
				'order_by'  => 'date_created',
				'order_way' => 'ASC',
			)
		);

		foreach ($messages as $m) {
			$sender_type_id = $m['from_admin'] ? MsConversation::SENDER_TYPE_ADMIN : ($m['seller_sender'] ? MsConversation::SENDER_TYPE_SELLER : MsConversation::SENDER_TYPE_CUSTOMER);
			$sender = $m['from_admin'] ? $m['user_sender'] : ($m['seller_sender'] ? $m['seller_sender'] : $m['customer_sender']);

			$this->data['messages'][] = array_merge(
				$m,
				array(
					'date_created' => date($this->language->get('datetime_format'), strtotime($m['date_created'])),
					'sender_type_id' => $sender_type_id,
					'sender' => $sender
				)
			);
		}

		$this->document->setTitle($this->language->get('ms_account_messages_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_conversations'),
				'href' => $this->url->link('multimerch/conversation', '', 'SSL'),
			),
			array(
				'text' => isset($conversation['title']) ? $conversation['title'] : '',
				'href' => $this->url->link('multimerch/conversation/view', 'conversation_id=' . $conversation_id, 'SSL'),
			)
		));

		$this->data['send_url'] = $this->url->link('multimerch/conversation/jxSendMessage', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/conversation_view.tpl', $this->data));
	}

	public function jxSendMessage() {
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));

		$conversation_id = $this->request->post['conversation_id'];
		if (!$conversation_id) return;

		$message_text = trim($this->request->post['ms-message-text']);

		$json = array();

		if (empty($message_text)) {
			$json['errors'][] = $this->language->get('ms_error_empty_message');
			$this->response->setOutput(json_encode($json));
			return;
		}

		if (mb_strlen($message_text) > 2000) {
			$json['errors'][] = $this->language->get('ms_error_contact_text');
		}

		if (!isset($json['errors'])) {
			$message_id = $this->MsLoader->MsMessage->createMessage(
				array(
					'conversation_id' => $conversation_id,
					'from' => $this->user->getId(),
					'message' => $message_text,
					'from_admin' => true
				)
			);

			if(isset($this->request->post['attachments'])) {
				$this->load->model('tool/upload');
				foreach ($this->request->post['attachments'] as $attachment_code) {
					$upload = $this->model_tool_upload->getUploadByCode($attachment_code);
					if(isset($upload['upload_id'])) $this->MsLoader->MsMessage->createMessageAttachment($message_id, $upload['upload_id']);
				}
			}

			$this->MsLoader->MsConversation->addConversationParticipants($conversation_id,
				array($this->user->getId()),
				true);

			$this->MsLoader->MsConversation->sendMailForParticipants($conversation_id,$message_text,true);

			$json['success'] = $this->language->get('ms_sellercontact_success');
		}
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete()
	{
		if (isset($this->request->post['selected']))
		{
			$conversation_ids = $this->request->post['selected'];
		}
		else if ($this->request->get['conversation_id'])
		{
			$conversation_id = $this->request->get['conversation_id'];
			$conversation_ids = array($conversation_id);
		}
		foreach ($conversation_ids as $conversation_id)
		{
			$this->MsLoader->MsConversation->deleteConversation($conversation_id);
		}

		$this->response->redirect($this->url->link('multimerch/conversation', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function jxUploadAttachment() {
		$this->load->language('tool/upload');
		$this->load->language('multiseller/multiseller');

		$json = array();

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Validate file extension
			$json['error'] = $this->MsLoader->MsFile->checkFile($this->request->files['file'], $this->config->get('msconf_msg_allowed_file_types'));

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (empty($json['error'])) {
			unset($json['error']);

			// Hide the uploaded file name so people can not link to it directly.
			$file = $filename . '.' . token(32);

			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);

			$this->load->model('tool/upload');
			$json['code'] = $this->model_tool_upload->addUpload($filename, $file);
			$json['filename'] = $filename;
			$json['success'] = $this->language->get('ms_account_conversations_file_uploaded');
		}

		return $this->response->setOutput(json_encode($json));
	}

}
?>
