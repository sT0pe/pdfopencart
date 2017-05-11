<?php

class ControllerAccountMSConversation extends Controller {
	private $data = array();

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/msconversation', '', 'SSL');
			return $this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
		
		if (!$this->config->get('mmess_conf_enable')) return $this->response->redirect($this->url->link('account/account', '', 'SSL'));
	}

	public function getTableData() {
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		
		$colMap = array();
		
		$customer_id = $this->customer->getId();
		
		$sorts = array('last_message_date', 'title');
		$filters = array('');
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		//$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$conversations = $this->MsLoader->MsConversation->getConversations(
			array(
				'participant_id' => $customer_id,
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				//'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);
		
		$total = isset($conversations[0]) ? $conversations[0]['total_rows'] : 0;

		$columns = array();
		foreach ($conversations as $conversation) {
			// Actions

			if($conversation["order_id"]){
				$conversation_type = 'order';
			}else if($conversation["product_id"]){
				$conversation_type = 'product';
			}else{
				$conversation_type = 'seller';
			}

			$actions = "";
			$actions .= "<a href='" . $this->url->link('account/msmessage', 'conversation_id=' . $conversation['conversation_id'], 'SSL') ."' class='ms-button ms-button-view' title='" . $this->language->get('ms_view') . "'></a>";
			
			$columns[] = array_merge(
				$conversation,
				array(
					'date_created' => date($this->language->get('date_format_short'), strtotime($conversation['date_created'])),
					'last_message_date' => date($this->language->get('datetime_format'), strtotime($conversation['last_message_date'])),
					'conversation_type' => $conversation_type,
					'with' => '',
					'title' => (mb_strlen($conversation['title']) > 80 ? mb_substr($conversation['title'], 0, 80) . '...' : $conversation['title']),
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
		$this->document->addStyle('catalog/view/javascript/multimerch/datatables/css/jquery.dataTables.css');
		$this->document->addScript('catalog/view/javascript/multimerch/datatables/js/jquery.dataTables.min.js');
		$this->document->addScript('catalog/view/javascript/multimerch/common.js');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->language->load('account/account');
		
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_conversations_heading'));
		$customer_id = $this->customer->getId();
		
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
			)
		);
		
		if (!$this->MsLoader->MsSeller->isCustomerSeller($customer_id)) {
			unset($breadcrumbs[1]);
		}
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs($breadcrumbs);

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-conversation');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function downloadAttachment() {
		$this->load->model('tool/upload');

		if (isset($this->request->get['code'])) {
			$code = $this->request->get['code'];
		} else {
			$code = 0;
		}

		$upload_info = $this->model_tool_upload->getUploadByCode($code);

		if ($upload_info) {
			$file = DIR_UPLOAD . $upload_info['filename'];
			$mask = basename($upload_info['name']);

			if (!headers_sent()) {
				if (is_file($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					readfile($file, 'rb');
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		}
	}
}
?>