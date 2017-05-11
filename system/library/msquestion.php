<?php
class MsQuestion extends Model {
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	
	public function getQuestion($question_id = 0) {
		
		return;
	}
	
	public function addQuestion($data) {
		$sql = "
			INSERT INTO `" . DB_PREFIX . "ms_question` SET 
			`author_id` = " . (int)$data['author_id'] . ",
			`product_id` = " . (int)$data['product_id'] . ",
			`text` = '" . $this->db->escape($data['question']) . "',
			`date_created` = '" . $this->db->escape(date('Y-m-d H:i:s', time())) . "'";
			
		$this->db->query($sql);
	}
	
	public function removeQuestion($question_id) {
		
	}
	
	public function getQuestions($product_id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "ms_question` WHERE `product_id` = " . (int)$product_id;
		$query = $this->db->query($sql);
		
		
		return $query->rows;
	}
	
	public function getQuestionTotal($product_id) {
		$sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "ms_question` WHERE `product_id` = " . (int)$product_id;
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}
	
	public function getAnswers($question_id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "ms_answer` WHERE `question_id` = " . (int)$question_id;
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	public function getAnswersTotal($question_id) {
		$sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "ms_answer` WHERE `question_id` = " . (int)$question_id;
		$query = $this->db->query($sql);
		
		return $query->row;
	}
	
	public function addAnswer($data) {
		$sql = "
			INSERT INTO `" . DB_PREFIX . "ms_answer` SET
			`author_id` = " . (int)$data['author_id'] . ",
			`question_id` = " . (int)$data['question_id'] . ",
			`text` = '" . $this->db->escape($data['answer']) . "',
			`date_created` = '" . $this->db->escape(date('Y-m-d H:i:s', time())) . "',
			`rating` = 0";
		
		$this->db->query($sql);
	}
	
	public function getRating($answer_id) {
		$sql = "SELECT rating FROM `" . DB_PREFIX ."ms_answer` WHERE `answer_id` = " . (int)$answer_id;
		
		return $this->db->query($sql)->row['rating'];
	}
	
	public function setRating($rating, $answer_id) {
		$sql = "UPDATE `" . DB_PREFIX . "ms_answer` SET `rating` = " . (int) $rating . " WHERE `answer_id` = " . (int)$answer_id;

		$this->db->query($sql);
	}
	
	public function hasVoted($user_id, $answer_id, $type) {
		if($type < 0) {
			$type = 0;
		} else {
			$type = 1;
		}
		$sql = "
			SELECT * FROM `" . DB_PREFIX . "ms_user_vote` 
			WHERE `type` = " . (int)$type . "
			AND `user_id` = " . (int)$user_id . "
			AND `answer_id` = " . (int)$answer_id;

		$query = $this->db->query($sql);
		if(empty($query->rows)) {
			return false;
		} else {
			return true;
		}
	}
	
	public function setVoted($user_id, $answer_id, $type) {
		if($type < 0) {
			$type = 0;
		} else {
			$type = 1;
		}

		$sql = "
			INSERT INTO `" . DB_PREFIX . "ms_user_vote` SET
			`type` = " . (int)$type . ",
			`user_id` = " . (int)$user_id . ",
			`answer_id` = " . (int)$answer_id . "
			ON DUPLICATE KEY UPDATE `type` = " . (int)$type;
		
		$this->db->query($sql);
	}
	
}
?>