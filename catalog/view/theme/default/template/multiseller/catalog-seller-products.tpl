<?php echo $header; ?>
<div class="container ms-catalog-seller-products">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li> <a href="<?php echo $breadcrumb['href']; ?>"> <?php echo $breadcrumb['text']; ?> </a> </li>
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


		<aside id="column-left" class="col-sm-3 hidden-xs">
			<!-- mm catalog seller products avatar block start -->
			<div class="mm_box mm_description">
				<div class="info-box">
					<a class="avatar-box thumbnail" href="<?php echo $seller['href']; ?>"><img src="<?php echo $seller['thumb']; ?>" /></a>
					<div>
						<ul class="list-unstyled">
							<li><h3 class="sellersname"><?php echo $seller['nickname']; ?></h3></li>
							<li><?php echo $seller['settings']['slr_company'] ;?></li>
							<li><a target="_blank" href="<?php echo $seller['settings']['slr_website'] ;?>"><?php echo $seller['settings']['slr_website'] ;?></a></li>
							<li><?php echo trim($seller['settings']['slr_city'] . ', ' . $seller['settings']['slr_country'], ',') ;?></li>
						</ul>
					</div>
				</div>
				<a href="<?php echo $seller['href']; ?>" class="btn btn-default btn-block" style="clear: both">
					<span><?php echo $ms_catalog_seller_profile; ?></span>
				</a>
			</div>
			<!-- mm catalog seller products avatar block end -->
			


			<div class="list-group">
				<a href="<?php echo $this->url->link('seller/catalog-seller/products', 'seller_id=' . $this->request->get['seller_id']); ?>" class="list-group-item"><b><?php echo $ms_all_products; ?></b></a>

				<?php foreach($seller['ms_categories'] as $category) { ?>
					<a href="<?php echo $category['href'] ;?>" class="list-group-item <?php echo isset($this->request->get['ms_category_id']) && $category['category_id'] == $this->request->get['ms_category_id'] ? 'active' : ''; ?>"><?php echo $category['name'] ;?><span class="badge"><?php echo $category['total'] ;?></span></a>
					<?php if(isset($category['childs'])) { ?>
						<?php foreach($category['childs'] as $child) { ?>
							<a href="<?php echo $child['href']; ?>" class="list-group-item <?php echo isset($this->request->get['ms_category_id']) && $child['category_id'] == $this->request->get['ms_category_id'] ? 'active' : ''; ?>">&nbsp;&nbsp;&nbsp;- <?php echo $child['name']; ?><span class="badge"><?php echo $child['total'] ;?></span></a>
							<?php if(isset($child['childs'])) { ?>
								<?php foreach($child['childs'] as $child_2) { ?>
									<a href="<?php echo $child_2['href']; ?>" class="list-group-item <?php echo isset($this->request->get['ms_category_id']) && $child_2['category_id'] == $this->request->get['ms_category_id'] ? 'active' : ''; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <?php echo $child_2['name']; ?><span class="badge"><?php echo $child_2['total'] ;?></span></a>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</div>

		</aside>
		<div id="content" class="col-sm-9"><?php echo $content_top; ?>
			<?php if ($seller['products']) { ?>
			<div class="row">

				<div class="mm_top_products_left col-sm-6">
					<div class="btn-group hidden-xs hidden">
						<button type="button" id="list-view" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_list; ?>"><i class="fa fa-th-list"></i></button>
						<button type="button" id="grid-view" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_grid; ?>"><i class="fa fa-th"></i></button>
					</div>
					<div id="search" class="input-group">
						<form action="index.php" method="get">
							<input type="hidden" name="route" value="seller/catalog-seller/products">
							<input type="hidden" name="seller_id" value="<?php echo $seller['seller_id'] ;?>">
							<input type="text" name="search" value="" placeholder="<?php echo $ms_catalog_seller_profile_search ;?>" class="form-control input-lg">
								<span class="input-group-btn">
									<button class="btn btn-default btn-lg"><i class="fa fa-search"></i></button>
								</span>
						</form>
					</div>
				</div>

				<div class="mm_top_products_right col-sm-6">
					<div class="mm_sort_group">
						<label class="control-label" for="input-sort"><?php echo $text_sort; ?></label>
						<select id="input-sort" class="form-control" onchange="location = this.value;" >
							<?php foreach ($sorts as $sorts) { ?>
							<?php if ($sorts['value'] == $sort . '-' . $order) { ?>
							<option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
							<?php } ?>
							<?php } ?>
						</select>
					</div>
					<div class="mm_sort_group">
						<label class="control-label" for="input-limit"><?php echo $text_limit; ?></label>
						<select id="input-limit" class="form-control" onchange="location = this.value;">
							<?php foreach ($limits as $limits) { ?>
							<?php if ($limits['value'] == $limit) { ?>
							<option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
							<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="cl"></div>
			</div>

			<div class="row">
				<?php foreach ($seller['products'] as $product) { ?>
				<div class="product-layout product-list col-xs-12">
					<div class="product-thumb">
						<div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
						<div class="caption">
							<h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
							<p><?php echo $product['description']; ?></p>
							<?php if ($product['rating']) { ?>
							<div class="rating">
								<?php for ($i = 1; $i <= 5; $i++) { ?>
								<?php if ($product['rating'] < $i) { ?>
								<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
								<?php } else { ?>
								<span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
								<?php } ?>
								<?php } ?>
							</div>
							<?php } ?>
							<?php if ($product['price']) { ?>
							<p class="price">
								<?php if (!$product['special']) { ?>
								<?php echo $product['price']; ?>
								<?php } else { ?>
								<span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
								<?php } ?>
								<?php if ($product['tax']) { ?>
								<span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
								<?php } ?>
							</p>
							<?php } ?>
						</div>
						<div class="button-group">
							<button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span></button>
							<button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
							<button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="row">
				<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
				<div class="col-sm-6 text-right"><?php echo $results; ?></div>
			</div>
			<?php } else { ?>
			<p><?php echo $ms_catalog_seller_products_empty; ?></p>
			<?php } ?>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>