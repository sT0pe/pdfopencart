<div class="modal fade" id="contactDialog" tabindex="-1" role="dialog" aria-labelledby="sendMessageLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $ms_sellercontact_close; ?></span></button>
		<h4 class="modal-title" id="sendMessageLabel"><?php echo sprintf($ms_sellercontact_sendto, $seller['nickname']); ?></h4>
	  </div>
	  <div class="modal-body">
		<form role="form" id="contactForm">
		  <input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>" />
		  <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
		  <div class="form-group">
			<label for="ms-sellercontact-text" class="control-label"><?php echo $ms_sellercontact_text; ?></label>
			<textarea class="form-control" id="ms-sellercontact-text" name="ms-sellercontact-text"></textarea>
		  </div>
		</form>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $ms_sellercontact_close; ?></button>
		<button type="button" class="btn btn-primary" id="sendMessage"><?php echo $ms_sellercontact_send; ?></button>
	  </div>
	</div>
  </div>
</div>