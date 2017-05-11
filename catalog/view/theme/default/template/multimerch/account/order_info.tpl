<?php if (isset($seller_histories) && $seller_histories) { ?>
	<h3><?php echo $this->language->get('ms_order_details_by_seller'); ?></h3>

	<ul class="nav nav-tabs">
		<?php foreach ($seller_histories as $history) { ?>
			<li <?php if ($history['suborder_id'] == $active_tab) { ?>class="active"<?php } ?>><a href="#seller_tab<?php echo $history['suborder_id']; ?>" data-toggle="tab"><?php echo $history['participant']; ?></a></li>
		<?php } ?>
	</ul>

	<div class="tab-content">
		<?php foreach ($seller_histories as $history) { ?>
			<?php if(!empty($history['entries']) || $this->config->get('mmess_conf_enable')) { ?>
				<div class="tab-pane <?php if($history['suborder_id'] == $active_tab) { ?>active<?php } ?>" id="seller_tab<?php echo $history['suborder_id']; ?>">
					<div class="suborder-info">
						<ul>
							<li><?php echo $this->language->get('ms_order_products_by'); ?> <b><?php echo $history['participant']; ?></b></li>
							<li><?php echo $this->language->get('ms_order_id'); ?> <b><?php echo '#' . $order_id . '-' . $history['suborder_id']; ?></b></li>
							<li><?php echo $this->language->get('ms_order_current_status'); ?> <b><?php echo $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $history['suborder_status'])); ?></b></li>
						</ul>
					</div>

					<?php if(!empty($history['entries'])) { ?>
						<h4><?php echo $this->language->get('ms_order_status_history'); ?></h4>
						<table class="table table-bordered table-hover list">
							<thead>
								<tr>
									<td class="text-left col-sm-3"><?php echo $this->language->get('ms_date_modified'); ?></td>
									<td class="text-left col-sm-6"><?php echo $column_comment; ?></td>
									<td class="text-left col-sm-3"><?php echo $column_status; ?></td>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($history['entries'] as $he) { ?>
									<tr>
										<td class="text-left col-sm-3"><?php echo $he['date_added']; ?></td>
										<td class="text-left col-sm-6"><?php echo nl2br($he['comment']); ?></td>
										<td class="text-left col-sm-3"><?php echo $he['status']; ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } ?>

					<?php if ($this->config->get('mmess_conf_enable')) { ?>
						<h4><?php echo !empty($history['order_messages']) ? $history['conversation_title'] : sprintf($ms_account_conversations_start_with_seller, $this->MsLoader->MsSeller->getSellerNickname($history['seller_id'])); ?></h4>

						<div class="ms-account-conversation">
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
		<?php } ?>
	</div>
<?php } ?>