<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>

  <?php if (isset($success) && ($success)) { ?>
		<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?></div>
  <?php } ?>

  <?php if (isset($error_warning) && $error_warning) { ?>
  	<div class="alert alert-danger warning main"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>

	<div style="display: none" class="alert alert-danger error_text"><i class="fa fa-exclamation-circle"></i> <span id="error_text"></span>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>

  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?> ms-account-order"><?php echo $content_top; ?>
	    <h4><?php echo $conversation['title']; ?></h4>

		<div class="ms-account-conversation">
			<div class="ms-messages">
				<?php if (isset($messages)) { ?>
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
				<?php } ?>
			</div>

			<div class="row ms-message-form">
				<form id="ms-message-form" class="ms-form form-horizontal">
					<input type="hidden" name="conversation_id" value="<?php echo $conversation['conversation_id']; ?>" />

					<div class="col-sm-10">
						<textarea class="form-control" rows="5" cols="50" name="ms-message-text" id="ms-message-text" placeholder="<?php echo $ms_account_conversations_textarea_placeholder; ?>"></textarea>
						<div class="list">
							<ul class="attachments"></ul>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="buttons text-center">
							<button type="button" class="btn btn-default ms-message-upload"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
							<button type="button" class="btn btn-primary" id="ms-message-reply"><?php echo $ms_post_message; ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>

      <?php echo $content_bottom; ?>
	</div>
    <?php echo $column_right; ?>
  </div>
</div>

<?php echo $footer; ?>