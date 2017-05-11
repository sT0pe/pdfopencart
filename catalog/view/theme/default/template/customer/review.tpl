<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
            <div class="panel panel-default">
                <div class="panel-heading feedback-heading">
                    <div class="row">
                        <div class="col-md-10 sub-table">
                            <div class="sub-row">
                                <span class="sub-table-heading"><?php echo $ms_customer_product_rate_heading ;?></span>
                            </div>
                        </div>
						<span class="order-number col-md-2">
							#<?php echo $order_id ;?><br />
							<a href="<?php echo $this->url->link('account/order/info', 'order_id=' . $order_id, 'SSL') ;?>">
								<?php echo $ms_order_details ;?>
							</a>
						</span>
                    </div>
                </div>
                <div class="panel-body">
                    <?php foreach($products as $product) :?>
                        <?php if($product['product_id'] == $product_id) :?>
                            <div class="product-holder">
                                <div class="row">
                                    <div class="product-image col-md-3">
                                        <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']) ;?>">
                                            <img src="<?php echo $product['product']['image'] ;?>"/>
                                        </a>
                                    </div>
                                    <div class="product-info col-md-5">
                                        <h4>
                                            <span class="quantity"><?php echo $product['quantity'] ;?>x</span>
                                            <a href="<?php echo $this->url->link('product/product', 'product_id=' . $product['product_id']) ;?>">
                                                <?php echo $product['name'] ;?>
                                            </a>
                                            <?php if(is_array($product['options']) && !empty($product['options'])) :?>
                                            <ul class="product-options">
                                                <?php foreach($product['options'] as $option) :?>
                                                <li>
                                                    <?php echo $option['name'] ;?>: <b><?php echo $option['value'] ;?></b>
                                                </li>
                                                <?php endforeach ;?>
                                            </ul>
                                            <?php endif ;?>
                                        </h4>
                                        <span class="seller-name">
                                            <?php echo $ms_order_sold_by ;?> <a href="<?php echo $this->url->link('seller/catalog-seller/profile', 'seller_id=' . $product['seller']['seller_id']) ;?>"><?php echo $product['seller']['ms.nickname'] ;?></a>
                                        </span>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="product-price">
                                            <?php echo $product['price'] ;?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- `Rate` section -->
                            <form action="<?php echo $this->url->link('customer/review/createOrUpdate', '', 'SSL') ;?>" id="form-rate" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id'] ;?>">
                                <input type="hidden" name="order_id" value="<?php echo $product['order_id'] ;?>">
                                <input type="hidden" name="order_product_id" value="<?php echo $product['order_product_id'] ;?>">
                                <div class="form-rate-rating-stars">
                                    <label for="rating-input-xs" class="control-label mm_req"><?php echo $ms_customer_product_rate_stars_label ;?></label>
                                    <input id="rating-input-xs" name="rating" class="rating" data-min="0" data-max="5" data-step="1" data-size="xs" value="<?php echo (!empty($product['review'])) ? $product['review'][0]['rating'] : 0 ;?>">
                                </div>
                                <div class="form-rate-comment">
                                    <label for="rating-comment" class="mm_req"><?php echo $ms_customer_product_rate_comments ;?></label>
                                    <textarea class="form-control" rows="5" id="rating-comment" name="rating_comment" placeholder="<?php echo $ms_customer_product_rate_comments_placeholder ;?>" maxlength="5000"><?php echo (!empty($product['review'])) ? $product['review'][0]['comment'] : '' ;?></textarea>
                                    <span class="rating-comment-note"></span>
                                </div>
                                <div class="form-rate-quiz">
                                    <span class="mm_req"><?php echo $ms_customer_product_rate_quiz ;?></span>
                                    <input type="radio" name="prod_desc_accurate" id="pda-yes" value="1" <?php if(!empty($product['review']) && $product['review'][0]['description_accurate'] == 1) { ?> checked="checked" <?php } ?> />
                                    <label for="pda-yes"><span></span><?php echo $text_yes ;?></label>
                                    <input type="radio" name="prod_desc_accurate" id="pda-no" value="0" <?php if(!empty($product['review']) && $product['review'][0]['description_accurate'] == 0) { ?> checked="checked" <?php } ?> />
                                    <label for="pda-no"><span></span><?php echo $text_no ;?></label>
                                </div>
                                <div class="alert alert-danger hidden form-rate-error"></div>
                                <div class="form-rate-buttons">
                                    <button type="submit" form="form-rate" title="<?php echo $ms_customer_product_rate_btn_submit; ?>" class="btn btn-primary form-rate-submit"><?php echo $ms_customer_product_rate_btn_submit; ?></button>
                                    <a href="<?php echo $this->url->link('account/order', '', 'SSL') ;?>">Cancel</a>
                                </div>
                            </form>
                            <!-- End `rate` section -->
                        <?php endif ;?>
                    <?php endforeach ;?>
                </div>
            </div>
            <?php echo $content_bottom; ?>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
