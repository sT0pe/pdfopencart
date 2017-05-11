<?php if ($sandbox) { ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-circle"><?php echo $ppa_sandbox; ?></i>
  <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php } ?>

<div class="alert alert-danger hidden main-error"><i class="fa fa-exclamation-circle"></i>
  <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>

<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
  </div>
</div>

<script type="text/javascript">
$('#button-confirm').bind('click', function() {
	$.ajax({
		type: 'POST',
		url: '<?php echo $continue; ?>',
		dataType: 'json',
		beforeSend: function() {
			$('#button-confirm').button('loading');
			$('.alert-danger').addClass('hidden').find('span').remove();
		},
		success: function(json) {
			$('#button-confirm').button('reset');

			if (json['error']) {
				$('.main-error').removeClass('hidden').append('<span>'+json['error']+'</span>');
				return;
			}
			
			if (json['redirect']) {
				$(".main-error").addClass('hidden');
				location = json['redirect'];
			}
		}
	});
});
</script>
