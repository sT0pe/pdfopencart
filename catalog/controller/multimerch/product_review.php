<?php
class ControllerMultimerchProductReview extends Controller {

	public function index() {
		$data = $this->load->language('multiseller/multiseller');

		$this->document->addScript('catalog/view/javascript/star-rating.js');

		$data['reviews'] = array();
		$data['total_reviews'] = 0;
		$data['rating_stats'] = 0;
		$data['avg_rating'] = 0;

		$rating_stats = array();
		$avg_rating = 0;

		$reviews = $this->MsLoader->MsReview->getReviews(array('product_id' => $this->request->get['product_id']));
		$total_reviews = (!empty($reviews)) ? $reviews[0]['total_rows'] : 0;

		if($total_reviews > 0) {
			foreach ($reviews as $key => $review) {
				$avg_rating += $review['rating'];
				$rating_stats[$review['rating']]['votes'] = isset($rating_stats[$review['rating']]) ? $rating_stats[$review['rating']]['votes'] + 1 : 1;
				$rating_stats[$review['rating']]['percentage'] = round($rating_stats[$review['rating']]['votes'] / $total_reviews * 100, 1);
			}

			for($i = 1; $i < 6; $i++) {
				if(!isset($rating_stats[$i])) {
					$rating_stats[$i] = ['votes' => 0, 'percentage' => 0];
				}
			}
			krsort($rating_stats);

			$data['total_reviews'] = $total_reviews;
			$data['rating_stats'] = $rating_stats;
			$data['avg_rating'] = round($avg_rating / $total_reviews, 1);

			$data['mm_review_rating_summary'] = sprintf($this->language->get('mm_review_rating_summary'), round($data['avg_rating'], 1), $data['total_reviews'], $data['total_reviews'] == 1 ? 'review' : 'reviews');
		}

		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/product/mm_review.tpl')) {
			$this->response->setOutput($this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/product/mm_review.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/product/mm_review.tpl', $data));
		}
	}

	public function jxReviewComments(){
		$data = array();
		$sql_data = array();

		if(isset($this->request->get['product_id'])) {
			$sql_data['product_id'] = $this->request->get['product_id'];
		}

		if(isset($this->request->get['seller_id'])) {
			$sql_data['seller_id'] = $this->request->get['seller_id'];
		}

		$data['reviews'] = array();
		$reviews = $this->MsLoader->MsReview->getReviews($sql_data);

		$this->load->model('account/customer');

		foreach ($reviews as $key => $review) {
			if(isset($this->request->get['seller_id'])) {
				$this->load->model('catalog/product');

				$reviewed_product = $this->model_catalog_product->getProduct($review['product_id']);

				$data['reviews'][$key]['product']['name'] = $reviewed_product['name'];
				$data['reviews'][$key]['product']['href'] = $this->url->link('product/product', 'product_id=' . $reviewed_product['product_id']);
				$data['reviews'][$key]['product']['model'] = $reviewed_product['model'];
				$data['reviews'][$key]['product']['price'] = $this->currency->format($reviewed_product['price']);
			}

			$data['reviews'][$key]['author'] = $this->model_account_customer->getCustomer($review['author_id']);
			$data['reviews'][$key]['date_created'] = date('j M Y', strtotime($review['date_created']));
			$data['reviews'][$key]['rating'] = $review['rating'];
			$data['reviews'][$key]['comment'] = $review['comment'];
		}

		$this->response->setOutput(json_encode(array(
			'reviews' => $data['reviews']
		)));
	}
}