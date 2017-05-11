<?php

class ControllerSellerAccountTransaction extends ControllerSellerAccount {
	public function getTransactionData() {
		$seller_id = $this->customer->getId();
		
		$colMap = array(
			'transaction_id' => 'balance_id',
			'seller' => '`nickname`',
			'description' => 'mb.description',
			'date_created' => 'mb.date_created'
		);
		
		$sorts = array('transaction_id', 'seller', 'amount', 'date_created');
		$filters = array_merge($sorts, array('description'));
	
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);
	
		$results = $this->MsLoader->MsBalance->getBalanceEntries(
			array(
				'seller_id' => $seller_id
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
			$columns[] = array_merge(
				$result,
				array(
					'transaction_id' => $result['balance_id'],
					'amount' => $this->currency->format($result['amount'], $this->config->get('config_currency')),
					'description' => (utf8_strlen($result['mb.description']) > 80 ? mb_substr($result['mb.description'], 0, 80) . '...' : $result['mb.description']),
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['mb.date_created'])),
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
		$seller_id = $this->customer->getId();
		
		$seller_balance = $this->MsLoader->MsBalance->getSellerBalance($seller_id);
		$pending_funds = $this->MsLoader->MsBalance->getReservedSellerFunds($seller_id);
		$waiting_funds = $this->MsLoader->MsBalance->getWaitingSellerFunds($seller_id, 14);
		$balance_formatted = $this->currency->format($seller_balance,$this->config->get('config_currency'));
		
		$balance_reserved_formatted = $pending_funds > 0 ? sprintf($this->language->get('ms_account_balance_reserved_formatted'), $this->currency->format($pending_funds)) . ', ' : '';
		$balance_reserved_formatted .= $waiting_funds > 0 ? sprintf($this->language->get('ms_account_balance_waiting_formatted'), $this->currency->format($waiting_funds)) . ', ' : ''; 
		$balance_reserved_formatted = ($balance_reserved_formatted == '' ? '' : '(' . substr($balance_reserved_formatted, 0, -2) . ')');

		$this->data['ms_balance_formatted'] = $balance_formatted;
		$this->data['ms_reserved_formatted'] = $balance_reserved_formatted;

		$earnings = $this->MsLoader->MsSeller->getTotalEarnings($seller_id);
		$this->data['earnings'] = $this->currency->format($earnings, $this->config->get('config_currency'));
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		
		$this->document->setTitle($this->language->get('ms_account_transactions_heading'));
		
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
				'text' => $this->language->get('ms_account_transactions_breadcrumbs'),
				'href' => $this->url->link('seller/account-transaction', '', 'SSL'),
			)
		));
		
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-transaction');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
}

?>
