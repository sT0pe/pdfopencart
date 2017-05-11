<h3><?php echo $mm_question_title; ?></h3>
<form id="question-form" class="form-horizontal">
	<div class="questions-list">
		<?php if($questions) { ?>
		<?php foreach($questions as $question) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<span class="question-title"><?php echo $question['text']; ?></span>
					<span class="pull-right">
						<?php echo $mm_question_posted_by;?> <b><?php echo $question['author']['firstname']; ?> <?php echo $question['author']['lastname']; ?></b>
						<br>
						<b><?php echo $question['date_created']; ?></b>
					</span>
				</div>
				<div class="panel-body">
					<div class="actions">
						<a href="#answers-<?php echo $question['question_id']; ?>" class="btn btn-link" data-toggle="collapse" aria-expanded="true">
							<?php echo $mm_question_answers; ?>(<?php echo $answers['total'][$question['question_id']]; ?>) <i class="fa fa-caret-down"></i>
						</a>
					</div>
					<div class="collapse in" id="answers-<?php echo $question['question_id']; ?>">
						<div class="well" id="well-<?php echo $question['question_id']; ?>">
							<?php if(!empty($answers[$question['question_id']])) { ?>
								<?php foreach($answers[$question['question_id']] as $answer) { ?>
									<div class="alert alert-info answer-content" data-rating="<?php echo $answer['rating']; ?>" style="opacity: calc(1 + <?php echo ($answer['rating']/100); ?>)">
										<div class="answer-heading">
											<span><?php echo $mm_question_answer_by; ?> <b><?php echo $answer['author']['firstname']; ?> <?php echo $answer['author']['lastname']; ?></b></span>
											<span class="pull-right"><b><?php echo $answer['date_created']; ?></b></span>
										</div>
										<p>
											<?php echo $answer['text']; ?>
										</p>
										<div class="ratings">
											<span class="rating-value"><?php echo $answer['rating']; ?></span>
											<?php if($this->customer->getId() && $this->config->get('msconf_allow_questions')) { ?>
												<a href="#" data-answer="<?php echo $answer['answer_id']; ?>"><i class="fa fa-plus"></i></a>
												<a href="#" data-answer="<?php echo $answer['answer_id']; ?>" class="bad"><i class="fa fa-minus"></i></a>
											<?php } ?>
										</div>
									</div>
								<?php } ?>
							<?php } else { ?>
							<?php echo $mm_question_no_answers; ?>
							<?php } ?>
						</div>
					</div>

					<?php if($this->customer->getId() && $this->config->get('msconf_allow_questions')) { ?>
					<div id="write-<?php echo $question['question_id']; ?>">
						<input type="hidden" value="<?php echo $question['question_id']; ?>" name="question">
							<div class="form-group">
								<label class="col-md-2"><?php echo $mm_question_write_answer; ?></label>
								<div class="col-md-10">
									<textarea placeholder="<?php echo $mm_question_write_answer; ?>" class="form-control"></textarea>
								</div>
							</div>
							<button class="btn btn-primary answer-submit"><?php echo $mm_question_submit; ?></button>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php } else { ?>
			<?php echo $mm_question_no_questions; ?>
		<?php } ?>
	</div>

	<?php if($this->customer->getId() && $this->config->get('msconf_allow_questions')) { ?>
	<div class="form-group">
	<input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
	<textarea name="question" class="form-control" placeholder="<?php echo $mm_question_ask; ?>"></textarea>
	</div>
	<button id="addQuestion" class="btn btn-primary"><?php echo $mm_question_submit; ?></button>
	<?php } else { ?>
		<?php echo $mm_question_signin; ?>
	<?php } ?>
</form>
<script>
	$(document).ready(function() {
//		getOpacity();

		$(document).on('click', "#addQuestion", function(e) {
			e.preventDefault();
			var data = $('#question-form').serialize();
			
			$.ajax({
				url: 'index.php?route=multimerch/product_question/jxAddQuestion',
				type: 'post',
				data: data,
				success: function(response) {
					if((response.errors).length > 0) {
						$('textarea[name="question"]').parent().addClass('has-error');
					} else {
						$('textarea[name="question"]').parent().removeClass('has-error');
						$('textarea[name="question"]').val('');
						$('#tab-questions').load(location.href + ' #tab-questions>*', '');
					}
				},
				error: function(error) {
					console.log(error);
				}
			});
		});
		$(document).on('click', '.answer-submit', function(e) {
			e.preventDefault();
			var data = {
				'answer' : $(this).parent().find('textarea').val(),
				'question_id' : $(this).parent().find('input').val()
			};
			
			var button = $(this);
			var holder_id = button.parent().parent().find('.well').attr('id');
			$.ajax({
				url: 'index.php?route=multimerch/product_question/jxAddAnswer',
				data: data,
				type: 'post',
				success: function(response) {
					if((response.errors).length > 0){
						$(button).parent().find('.form-group').addClass('has-error');
					} else {
						$(button).parent().find('.form-group').removeClass('has-error');
						$(button).parent().find('textarea').val('');
						$('#question-form').load(location.href + ' #question-form>*', '');
					}
				},
				error: function(error) {
					console.log(error);
				}
				
			});
		});
		
		$(document).on('click', '.ratings a', function(e) {
			e.preventDefault();
			var button = $(this);
			var action;
			if(button.hasClass("bad")) {
				action = -1;
			} else {
				action = 1;
			}
			var answer_id = button.data('answer');
			var holder_id = button.parent().parent().parent().attr('id');
			console.log(parent);
			$.ajax({
				url: 'index.php?route=multimerch/product_question/jxRating',
				data: {'action': action, 'answer_id': answer_id},
				type: 'post',
				success: function(response) {
					$('#' + holder_id).load(location.href + ' #' + holder_id+ '>*', '');
				},
				error: function(error) {
					console.log(error);
				}
			});
		});
		
	});
</script>