<?php

class ControllerAccountMSMessage extends Controller {
	private $data = array();

	public function __construct($registry) {
		parent::__construct($registry);
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/msconversation', '', 'SSL');
			return $this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
		
		if (!$this->config->get('mmess_conf_enable')) return $this->response->redirect($this->url->link('account/account', '', 'SSL'));
	}
	
	public function jxSendMessage() {
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$conversation_id = $this->request->post['conversation_id'];
		if (!$conversation_id) return;

		$conversation_participants_ids = $this->MsLoader->MsConversation->getConversationParticipantsIds($conversation_id);
		if (!in_array($this->customer->getId(), $conversation_participants_ids)){
			return;
		}
		
		$conversation = $this->MsLoader->MsConversation->getConversations(array(
			'conversation_id' => $conversation_id,
			'single' => 1
		));
		
		if (!$conversation) return;

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
					'from' => $this->customer->getId(),
					'message' => $message_text
				)
			);

			if(isset($this->request->post['attachments'])) {
				$this->load->model('tool/upload');
				foreach ($this->request->post['attachments'] as $attachment_code) {
					$upload = $this->model_tool_upload->getUploadByCode($attachment_code);
					if(isset($upload['upload_id'])) $this->MsLoader->MsMessage->createMessageAttachment($message_id, $upload['upload_id']);
				}
			}

			$this->MsLoader->MsConversation->sendMailForParticipants($conversation_id,$message_text);
			
			$json['success'] = $this->language->get('ms_sellercontact_success');
			$json['redirect'] = $this->url->link('account/msmessage&conversation_id=' . $conversation_id, '', 'SSL');
		}
		$this->response->setOutput(json_encode($json));
	}

	public function jxSendOrderMessage() {
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$json = array();
		if (!$this->customer->getId() OR
			!isset($this->request->post['order_id']) OR
			!isset($this->request->post['suborder_id']) OR
			!isset($this->request->post['seller_id']) OR
			$this->config->get('mmess_conf_enable') == 0
		){
			$json['errors'][] = $this->language->get('ms_error_form_submit_error');
		}

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->request->post['order_id']);

		$participants = array($order_info['customer_id'],$this->request->post['seller_id']);

		if (!in_array($this->customer->getId(), $participants)){
			$json['errors'][] = $this->language->get('ms_error_form_submit_error');
		}

		$message_text = trim($this->request->post['ms-message-text']);
		if (empty($message_text)) {
			$json['errors'][] = $this->language->get('ms_error_contact_allfields');
		}
		if (utf8_strlen($message_text) > 2000) {
			$json['errors'][] = $this->language->get('ms_error_contact_text');
		}

		if (!isset($json['errors'])) {
			$customer_name = $this->customer->getFirstname() . ' ' . $this->customer->getLastname();
			$title = sprintf($this->language->get('ms_conversation_title_order'), $customer_name);

			$conversation = $this->MsLoader->MsConversation->getOrderConversation((int)$this->request->post['order_id'], (int)$this->request->post['suborder_id']);

			if (isset($conversation['conversation_id'])){
				$conversation_participants_ids = $this->MsLoader->MsConversation->getConversationParticipantsIds($conversation['conversation_id']);
				if (!in_array($this->customer->getId(), $conversation_participants_ids)){
					return;
				}
				$conversation_id = $conversation['conversation_id'];
			} else {
				$conversation_id = $this->MsLoader->MsConversation->createConversation(
					array(
						'title' => $title,
						'conversation_from' => $this->customer->getId(),
						'order_id' => (int)$this->request->post['order_id'],
						'suborder_id' => (int)$this->request->post['suborder_id']
					)
				);
				$this->MsLoader->MsConversation->addConversationParticipants($conversation_id,$participants);
			}

			$message_id = $this->MsLoader->MsMessage->createMessage(
				array(
					'conversation_id' => (int)$conversation_id,
					'from' => $this->customer->getId(),
					'message' => $message_text
				)
			);

			if(isset($this->request->post['attachments'])) {
				$this->load->model('tool/upload');
				foreach ($this->request->post['attachments'] as $attachment_code) {
					$upload = $this->model_tool_upload->getUploadByCode($attachment_code);
					if(isset($upload['upload_id'])) $this->MsLoader->MsMessage->createMessageAttachment($message_id, $upload['upload_id']);
				}
			}

			$this->MsLoader->MsConversation->sendMailForParticipants($conversation_id,$message_text);

			$json['success'] = $this->language->get('ms_sellercontact_success');
			$json['order_id'] = $this->request->post['order_id'];
		}
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->document->addScript('catalog/view/javascript/multimerch/account-message.js');
		$this->MsLoader->MsHelper->addStyle('multimerch_messaging');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->language->load('account/account');
		$customer_id = $this->customer->getId();
		
		$conversation_id = isset($this->request->get['conversation_id']) ? $this->request->get['conversation_id'] : false;
		if (!$conversation_id || !$this->MsLoader->MsConversation->isParticipant($conversation_id, array('participant_id' => $customer_id)))
			return $this->response->redirect($this->url->link('account/msconversation', '', 'SSL'));
		
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
					'sender' => ((utf8_strlen($sender) > 20) ? utf8_substr($sender, 0, 20) . '..' : $sender) . ($m['from_admin'] ? ' (' . $this->language->get('ms_account_conversations_sender_type_' . MsConversation::SENDER_TYPE_ADMIN) . ')': '')
				)
			);
		}
		
		$this->data['conversation'] = $this->MsLoader->MsConversation->getConversations(array(
			'conversation_id' => $conversation_id,
			'single' => 1
		));


		$this->document->setTitle($this->language->get('ms_account_messages_heading'));
		
		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_conversations_breadcrumbs'),
				'href' => $this->url->link('account/msconversation', '', 'SSL'),
			),
			array(
				'text' => $this->data['conversation']['title'],
				'href' => $this->url->link('account/msmessage', '&conversation_id=' . $conversation_id, 'SSL'),
			)
		);
		
		if (!$this->MsLoader->MsSeller->isCustomerSeller($customer_id)) {
			unset($breadcrumbs[1]);
		}
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs($breadcrumbs);
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-message');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
}

?>
