<?php
class ControllerModuleMultiMerchTopsellers extends ControllerSellerCatalog {
	public function index($setting) {
		$this->data = array_merge($this->data, $this->load->language('module/multimerch_topsellers'));
		$this->data['heading_title'] = $this->language->get('ms_topsellers_sellers');
		if (isset($setting['limit']) && (int)$setting['limit'] > 0)
			$this->data['limit'] = (int)$setting['limit'];
		else
			$this->data['limit'] = 3;

		if (!isset($setting['width']) || (int)$setting['width'] <= 0)
			$setting['width'] = $this->config->get('config_image_category_width');

		if (!isset($setting['height']) || (int)$setting['height'] <= 0)
			$setting['height'] = $this->config->get('config_image_category_height');

		$this->data['sellers_href'] = $this->url->link('seller/catalog-seller');
		$this->data['sellers'] = array();

		$results = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_status' => array(MsSeller::STATUS_ACTIVE)
			),
			array(
				'order_by'               => 'total_sales',
				'order_way'              => 'DESC',
				'offset'              => 0,
				'limit'              => $this->data['limit']
			),
			array(
				'total_sales' => 1
			)
		);

		foreach ($results as $result) {
			$this->data['sellers'][] = array(
				'seller_id'  => $result['seller_id'],
				'nickname'        => $result['ms.nickname'],
				'href'        => $this->url->link('seller/catalog-seller/profile','seller_id=' . $result['seller_id']),
				'thumb' => !empty($result['ms.avatar']) && file_exists(DIR_IMAGE . $result['ms.avatar']) ? $this->MsLoader->MsFile->resizeImage($result['ms.avatar'], $setting['width'], $setting['height']) : $this->MsLoader->MsFile->resizeImage('ms_no_image.jpg', $setting['width'], $setting['height'])
			);
		}

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/module/multimerch_topsellers.tpl')) {
			return $this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/module/multimerch_topsellers.tpl', $this->data);
		} else {
			return $this->load->view('default/template/module/multimerch_topsellers.tpl', $this->data);
		}
	}
}