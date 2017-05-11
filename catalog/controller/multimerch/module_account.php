<?php
class ControllerMultimerchModuleAccount extends Controller {
	public function index() {
		$data = array_merge($this->load->language('multiseller/multiseller'), $this->load->language('account/login'), $this->load->language('module/account'), $this->load->language('extension/module/account'));
		$data['heading_title'] = $data['text_my_account'] = $this->language->get('heading_title');

		$data['ms_seller_created'] = $this->MsLoader->MsSeller->isCustomerSeller($this->customer->getId());

		$data['logged'] = $this->customer->isLogged();
		$data['register'] = $this->url->link('account/register', '', 'SSL');
		$data['login'] = $this->url->link('account/login', '', 'SSL');
		$data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		$data['account'] = $this->url->link('account/account', '', 'SSL');
		$data['edit'] = $this->url->link('account/edit', '', 'SSL');
		$data['password'] = $this->url->link('account/password', '', 'SSL');
		$data['address'] = $this->url->link('account/address', '', 'SSL');
		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order', '', 'SSL');
		$data['download'] = $this->url->link('account/download', '', 'SSL');
		$data['reward'] = $this->url->link('account/reward', '', 'SSL');
		$data['return'] = $this->url->link('account/return', '', 'SSL');
		$data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');
		$data['recurring'] = $this->url->link('account/recurring', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/module/account.tpl')) {
			return $this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/multimerch/module/account.tpl', $data);
		} else {
			return $this->load->view('default/template/multimerch/module/account.tpl', $data);
		}
	}
}