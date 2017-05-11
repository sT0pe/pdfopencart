<?php
//questions controller
class ControllerMultimerchProductQuestion extends Controller {

	public function index() {
		$data = $this->load->language('multiseller/multiseller');

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$data['product_id'] = $product_id;
		$questions = MsLoader::getInstance()->MsQuestion->getQuestions($product_id);
		$answers = array();
		$this->load->model('account/customer');
		foreach($questions as $key=>$question) {
			$answers[$question['question_id']] = MsLoader::getInstance()->MsQuestion->getAnswers($question['question_id']);

			if(!empty($answers[$question['question_id']])) {
				foreach($answers[$question['question_id']] as $k=>$v) {
					$answers[$question['question_id']][$k]['author'] = $this->model_account_customer->getCustomer($answers[$question['question_id']][$k]['author_id']);
				}
			}
			$answers['total'][$question['question_id']] = MsLoader::getInstance()->MsQuestion->getAnswersTotal($question['question_id'])['total'];
			$questions[$key]['author'] = $this->model_account_customer->getCustomer($question['author_id']);
		}
		
		$data['questions'] = $questions;
		$data['answers'] = $answers;
		
		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/product/mm_question.tpl')) {
			return $this->load->view(MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template/product/mm_question.tpl', $data);
		} else {
			return $this->load->view('default/template/product/mm_question.tpl', $data);
		}
	}
	
	public function jxAddQuestion() {
		$validator = MsLoader::getInstance()->MsValidator;
		$errors = array();
		
		$data = $this->request->post;
		$data['author_id'] = $this->customer->getId();
		
		if (!$validator->validate(array(
			'name' => 'question',
			'value' => $data['question']
		),
			array(
				array('rule' => 'required'),
				array('rule' => 'min_len,10'),
				array('rule' => 'max_len,200')
			)
		)) $errors = $validator->get_errors();
		
		if(!count($errors) > 0) {
			MsLoader::getInstance()->MsQuestion->addQuestion($data);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(array('errors' => $errors)));
	}
	
	public function jxAddAnswer() {
		$data = $this->request->post;
		$data['author_id'] = $this->customer->getId();
		$validator = MsLoader::getInstance()->MsValidator;
		$errors = array();

		if (!$validator->validate(array(
			'name' => 'answer',
			'value' => $data['answer']
		),
			array(
				array('rule' => 'required'),
				array('rule' => 'min_len,10'),
				array('rule' => 'max_len, 500')
			)
		)) $errors = $validator->get_errors();

		if(!count($errors) > 0) {
			MsLoader::getInstance()->MsQuestion->addAnswer($data);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(array('errors' => $errors)));
	}
	
	public function jxRating() {
		$action = $this->request->post['action'];
		$answer_id = $this->request->post['answer_id'];
		$rating = MsLoader::getInstance()->MsQuestion->getRating($answer_id);
		$rating += $action;
		if(!MsLoader::getInstance()->MsQuestion->hasVoted($this->customer->getId(), $answer_id, $action)) {
			MsLoader::getInstance()->MsQuestion->setRating($rating, $answer_id);
			MsLoader::getInstance()->MsQuestion->setVoted($this->customer->getId(), $answer_id, $action);
		}
	}
}