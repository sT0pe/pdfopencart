<footer>
  <div class="container">
    <div class="row">
      <?php if ($informations) { ?>
      <div class="col-sm-3">
        <h5><?php echo $text_information; ?></h5>
        <ul class="list-unstyled">
          <?php foreach ($informations as $information) { ?>
          <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>
      <div class="col-sm-3">
        <h5><?php echo $text_service; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
          <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
          <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
        </ul>
      </div>
      <div class="col-sm-3">
        <h5><?php echo $text_extra; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
          <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
          <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
          <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
        </ul>
      </div>
      <div class="col-sm-3">
        <h5><?php echo $text_account; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
          <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
          <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
          <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
        </ul>
      </div>
    </div>
    <hr>
    <p><?php echo $powered; ?></p>
  </div>
</footer>

            <?php if ($live_search_ajax_status):?>
	            <link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/live_search.css" />
				<script type="text/javascript"><!--
					var live_search = {
						selector: '#search-product input[name=\'search\']',
						text_no_matches: '<?php echo $text_empty; ?>',
						height: '50px'
					}

					$(document).ready(function() {
						var html = '';
						html += '<div class="live-search">';
						html += '	<ul>';
						html += '	</ul>';
						html += '<div class="result-text"></div>';
						html += '</div>';

						//$(live_search.selector).parent().closest('div').after(html);
						$(live_search.selector).after(html);

						$(live_search.selector).autocomplete({
							'source': function(request, response) {
								var filter_name = $(live_search.selector).val();
								var live_search_min_length = '<?php echo (int)$live_search_min_length; ?>';
								if (filter_name.length < live_search_min_length) {
									$('.live-search').css('display','none');
								}
								else{
									var html = '';
									html += '<li style="text-align: center;height:10px;">';
									html +=	'<img class="loading" src="catalog/view/theme/default/image/loading.gif" />';
									html +=	'</li>';
									$('.live-search ul').html(html);
									$('.live-search').css('display','block');

									$.ajax({
										url: 'index.php?route=product/live_search&filter_name=' +  encodeURIComponent(filter_name),
										dataType: 'json',
										success: function(result) {
											var products = result.products;
											$('.live-search ul li').remove();
											$('.result-text').html('');
											if (!$.isEmptyObject(products)) {
												var show_image = <?php echo $live_search_show_image;?>;
												var show_price = <?php echo $live_search_show_price;?>;
												var show_description = <?php echo $live_search_show_description;?>;
												$('.result-text').html('<a href="<?php echo $live_search_href;?>'+filter_name+'" class="view-all-results"><?php echo $text_view_all_results;?> ('+result.total+')</a>');

												$.each(products, function(index,product) {
													var html = '';
													
													html += '<li>';
													html += '<a href="' + product.url + '" title="' + product.name + '" target="_blank">';
													if(product.image && show_image){
														html += '	<div class="product-image"><img alt="' + product.name + '" src="' + product.image + '"></div>';
													}
													html += '	<div class="product-name">' + product.name ;
													if(show_description){
														html += '<p>' + product.extra_info + '</p>';
													}
													html += '</div>';
                                                    html += '</a>';
													if(show_price){
														if (product.special) {
															html += '	<div class="product-price"><span class="special">' + product.price + '</span><span class="price">' + product.special + '</span></div>';
														} else {
															html += '	<div class="product-price"><span class="price">' + product.price + '</span></div>';
														}
													}

                                                    html += '<form action="javascript:void(null);" method="post" onsubmit="addProduct(this);">';
                                                    html += '   <div class="quantity"><input id="product-quantity" class="form-control" name="quantity"  type="number" value="1" min="1" step="1" /></div>';
                                                    html += '   <button type="submit" class="btn btn-primary btn-sm">Add</button>';
                                                    html += '<input type="hidden" name="product_id" value="' + product.product_id + '" />';
                                                    html += '</form>';

													html += '<span style="clear:both"></span>';
													html += '</li>';
													$('.live-search ul').append(html);
												});
											} else {
												var html = '';
												html += '<li style="text-align: center;height:10px;">';
												html +=	live_search.text_no_matches;
												html +=	'</li>';

												$('.live-search ul').html(html);
											}
											$('.live-search ul li').css('height',live_search.height);
											$('.live-search').css('display','block');
											return false;
										}
									});
								}
							},
							'select': function(product) {
								$(live_search.selector).val(product.name);
							}
						});

						$(document).bind( "mouseup touchend", function(e){
						  var container = $('.live-search');
						  if (!container.is(e.target) && container.has(e.target).length === 0)
						  {
						    container.hide();
						  }
						});
					});

                    function addProduct(e) {
                        $.ajax({
                            type: 'POST',
                            url: 'index.php?route=multimerch/account_offer/addProduct',
                            dataType: 'json',
                            data: $(e).serialize(),

                            success: function (data) {
                                $('#offer-products').append(data);
                            }
                        });
                    }

				//--></script>
			<?php endif;?>
            

<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
//-->

<!-- Theme created by Welford Media for OpenCart 2.0 www.welfordmedia.co.uk -->

</body></html>