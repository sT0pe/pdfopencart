<?php
/**
 * copy of appropriate controller from admin application
 */
class ControllerCatalogProduct extends Controller {

	public function jxAutocompleteRelated() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$filter_data = array(
				'filters' => array(
					'pd.name' => isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : ''
				),
				'order_by' => 'pd.name',
				'order_way' => 'ASC',
				'offset' => 0,
				'limit' => 5
			);

			$seller_id = $this->MsLoader->MsProduct->getSellerId($this->request->get['product_id']);
			$results = $this->MsLoader->MsProduct->getProducts(array('seller_id' => $seller_id), $filter_data);

			foreach ($results as $key => $result) {
				if($result['product_id'] != $this->request->get['product_id']) {
					$json[] = array(
						'product_id' => $result['product_id'],
						'name'       => strip_tags(html_entity_decode($result['pd.name'], ENT_QUOTES, 'UTF-8')),
					);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}