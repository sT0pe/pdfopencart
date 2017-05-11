<?php
class ControllerMultimerchAccountAccount extends Controller
{
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$data = array_merge($this->load->language('multiseller/multiseller'), $this->load->language('account/account'));
		$data['ms_seller_created'] = $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId());

		$this->document->setTitle($this->language->get('heading_title'));
		$this->MsLoader->MsHelper->addStyle('multimerch/account');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['edit'] = $this->url->link('account/edit', '', 'SSL');
		$data['password'] = $this->url->link('account/password', '', 'SSL');
		$data['address'] = $this->url->link('account/address', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['return'] = $this->url->link('account/return', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');
		$data['recurring'] = $this->url->link('account/recurring', '', 'SSL');
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');

		if ($this->config->get('reward_status')) {
			$data['reward'] = $this->url->link('account/reward', '', 'SSL');
		} else {
			$data['reward'] = '';
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (!isset($data['credit_cards'])) {
			$data['credit_cards'] = array();
		}

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/account/account.tpl')) {
			$this->response->setOutput($this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/account/account.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/multimerch/account/account.tpl', $data));
		}
	}
}