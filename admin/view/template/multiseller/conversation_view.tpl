<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $ms_account_conversations; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<?php if ($error_warning) { ?>
			<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
		<?php } ?>

		<div style="display: none" class="alert alert-danger error_text"><i class="fa fa-exclamation-circle"></i> <span id="error_text"></span>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<?php if (isset($success) && $success) { ?>
			<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo isset($conversation['title']) ? $conversation['title'] : $ms_account_conversations; ?></h3>
			</div>

			<div class="panel-body">
				<?php if ($messages) { ?>
					<div class="ms-messages">
						<?php foreach ($messages as $message) { ?>
							<div class="row ms-message <?php echo $message['sender_type_id'] == MsConversation::SENDER_TYPE_ADMIN ? 'admin' : ($message['sender_type_id'] == MsConversation::SENDER_TYPE_SELLER ? 'seller' : ''); ?>">
								<div class="col-sm-12 ms-message-body">
									<div class="title">
										<?php echo ucwords($message['sender']); ?>
										<span class="date"><?php echo $message['date_created']; ?></span>
									</div>

									<div class="body">
										<?php echo nl2br($message['message']); ?>
									</div>

									<?php if(!empty($message['attachments'])) { ?>
										<div class="attachments">
											<?php foreach($message['attachments'] as $attachment) { ?>
												<a href="<?php echo $this->url->link('account/msconversation/downloadAttachment', 'code=' . $attachment['code'], true); ?>"><i class="fa fa-file-o" aria-hidden="true"></i> <?php echo $attachment['name']; ?></a>
												<br/>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php } ?>

				<div class="row ms-message-form">
					<form id="ms-message-form" class="ms-form form-horizontal">
						<input type="hidden" name="conversation_id" value="<?php echo $conversation['conversation_id']; ?>" />

						<div class="col-sm-10">
							<textarea class="form-control" rows="3" cols="50" name="ms-message-text" id="ms-message-text" placeholder="<?php echo $ms_account_conversations_textarea_placeholder; ?>"></textarea>
							<div class="list">
								<ul class="attachments"></ul>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="buttons text-center">
								<button type="button" class="btn btn-default ms-message-upload"><i class="fa fa-upload"></i> <?php echo $ms_account_conversations_upload; ?></button>
								<button type="button" class="btn btn-primary" id="ms-message-reply"><?php echo $ms_post_message; ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#ms-message-reply").click(function() {
			$('.error_text').hide();
			$.ajax({
				type: "POST",
				dataType: "json",
				url:  $('base').attr('href') + 'index.php?route=multimerch/conversation/jxSendMessage&token=<?php echo $this->session->data["token"]; ?>',
				data: $(this).parents("form").serialize(),
				beforeSend: function() {
					//$('#ms-message-form a.button').hide().before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
				},
				success: function(jsonData) {
					$('.error').text('');
					if (!jQuery.isEmptyObject(jsonData.errors)) {
						$('#ms-message-form a.button').show().prev('span.wait').remove();
						for (error in jsonData.errors) {
							if (!jsonData.errors.hasOwnProperty(error)) {
								continue;
							}
							$('.error_text').show();
							$('#error_text').text(jsonData.errors[error]);
							window.scrollTo(0,0);
						}
					} else {
						location.reload();
					}
				}
			});
		});

		$(document).on('click', '.ms-message-upload', function() {
			var node = this;

			$('#form-upload').remove();

			$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

			$('#form-upload input[name=\'file\']').trigger('click');

			if (typeof timer != 'undefined') {
				clearInterval(timer);
			}

			timer = setInterval(function() {
				if ($('#form-upload input[name=\'file\']').val() != '') {
					clearInterval(timer);

					$.ajax({
						url: 'index.php?route=multimerch/conversation/jxUploadAttachment&token=<?php echo $token; ?>',
						type: 'post',
						dataType: 'json',
						data: new FormData($('#form-upload')[0]),
						cache: false,
						contentType: false,
						processData: false,
						beforeSend: function() {
							$(node).button('loading');
						},
						complete: function() {
							$(node).button('reset');
						},
						success: function(json) {
							if (json['error']) {
								alert(json['error']);
							}

							if (json['success']) {
								alert(json['success']);

								var html = '<li>';
								html += '<input type="hidden" name="attachments[]" value="' + json['code'] + '" />';
								html += json['filename'];
								html += '<span class="ms-remove"><i class="fa fa-times"></i></span>';
								html += '</li>';
								$('ul.attachments').append(html);
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}
			}, 500);
		});
	});
</script>

<?php echo $footer; ?>