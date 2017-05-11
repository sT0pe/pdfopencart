<?php if (isset($seller_histories) && $seller_histories && $this->config->get('mmess_conf_enable')) { ?>
	<div class="panel panel-default">
		<?php foreach ($seller_histories as $history) { ?>
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-envelope"></i> <?php echo !empty($history['order_messages']) ? $history['conversation_title'] : sprintf($ms_account_conversations_start_with_customer, $history['participant']); ?></h3>
			</div>

			<div class="panel-body">
				<div class="ms-messages">
					<?php foreach ($history['order_messages'] as $message) { ?>
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

				<div class="row ms-message-form">
					<form id="ms-message-form<?php echo $history['suborder_id']; ?>" class="ms-form form-horizontal">
						<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
						<input type="hidden" name="suborder_id" value="<?php echo $history['suborder_id']; ?>" />
						<input type="hidden" name="seller_id" value="<?php echo $history['seller_id']; ?>" />

						<div class="col-sm-10">
							<textarea class="form-control ms-message-text" rows="5" cols="50" name="ms-message-text" placeholder="<?php echo $ms_account_conversations_textarea_placeholder; ?>"></textarea>
							<div class="list">
								<ul class="attachments"></ul>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="buttons text-center">
								<button type="button" class="btn btn-default ms-message-upload"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
								<button data-suborder_id="<?php echo $history['suborder_id']; ?>" type="button" class="btn btn-primary ms-order-message"><?php echo $ms_post_message; ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } ?>