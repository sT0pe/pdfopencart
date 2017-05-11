<?php echo $header; ?>
<div class="container catalog-seller">
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
			<?php if (isset($sellers) && $sellers) { ?>
			<div class="row">

				<div class="col-sm-12">
					<div class="row">
						<div class="mm_top_products_left col-sm-6">
							<div id="search" class="input-group">
								<input type="text" name="search" value="" placeholder="Search" class="form-control input-lg">
								<span class="input-group-btn"><button type="button" class="btn btn-default btn-lg"><i class="fa fa-search"></i></button></span>
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
					<?php foreach ($sellers as $seller) { ?>
					<div class="mm_seller">
						<div class="product-thumb">
							<div class="image">
								<a href="<?php echo $seller['href']; ?>"><img src="<?php echo $seller['thumb']; ?>" title="<?php echo $seller['nickname']; ?>" alt="<?php echo $seller['nickname']; ?>" /></a>
							</div>
							<div>
								<div class="mm_sellerdescription">
									<ul class="list-unstyled">
										<li><h3 class="sellersname"><?php echo $seller['nickname']; ?></h3></li>
										<li><?php echo $seller['settings']['slr_company'] ;?></li>
										<li><a target="_blank" href="<?php echo $seller['settings']['slr_website'] ;?>"><?php echo $seller['settings']['slr_website'] ;?></a></li>
										<li><?php echo trim($seller['settings']['slr_city'] . ', ' . $seller['settings']['slr_country'], ',') ;?></li>
									</ul>
								</div>
								<div class="mm_catalogseller">
									<a class="all" href="<?php echo $this->url->link('seller/catalog-seller/products', 'seller_id=' . $seller['seller_id']) ;?>"><b><?php echo $seller['total_products']; ?></b><?php echo $ms_account_products;?></a>
									<?php foreach($seller['products'] as $product) :?>
										<a href="<?php echo $product['href'] ;?>"><img height="46px" src="<?php echo $product['p.image'] ? $product['p.image'] : '' ;?>"></a>
									<?php endforeach ;?>
								</div>
							</div>
						</div>
					</div>

					<?php } ?>
				</div>

				<div class="row">
					<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
				</div>
				<?php } else { ?>
				<div class="content"><?php echo $ms_catalog_sellers_empty; ?></div>
				<div class="buttons">
					<div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
				</div>
				<?php } ?>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>