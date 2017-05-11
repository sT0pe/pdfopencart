<?php echo $header; ?>
<div class="container">

	<?php if (isset($success) && $success) { ?>
	<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
	<?php } ?>

	<?php if (isset($product_number_limit_exceeded) && $product_number_limit_exceeded) { ?>
	<div class="alert alert-warning"><i class="fa fa-check-exclamation-circle"></i> <?php echo $product_number_limit_exceeded; ?></div>
	<?php } ?>

	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?> ms-account-dashboard"><?php echo $content_top; ?>
			<div class="mm_dashboard">
				<h1><i class="fa fa-briefcase"></i><?php echo $ms_account_products_heading; ?></h1>

				<?php if(!isset($product_number_limit_exceeded)) { ?>
					<a href="<?php echo $this->url->link('seller/account-product/create', '', 'SSL'); ?>" class="btn btn-primary" id="ms-submit-button"><span><?php echo $ms_account_newproduct; ?></span></a>
				<?php } ?>

				<?php if($this->config->get('msconf_allow_seller_attributes') || $this->config->get('msconf_allow_seller_options') || $this->config->get('msconf_allow_seller_categories')) { ?>
					<div class="dropdown pull-right">
						<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-cog" aria-hidden="true"></i>
							<span class="caret"></span></button>
						<ul class="dropdown-menu">
							<?php if($this->config->get('msconf_allow_seller_attributes')) { ?>
								<li><a href="<?php echo $this->url->link('seller/account-attribute', '', 'SSL'); ?>"><?php echo $ms_account_attributes; ?></span></a></li>
							<?php } ?>

							<?php if($this->config->get('msconf_allow_seller_categories')) { ?>
								<li><a href="<?php echo $this->url->link('seller/account-category', '', 'SSL'); ?>"><?php echo $ms_account_categories; ?></a></li>
							<?php } ?>

							<?php if($this->config->get('msconf_allow_seller_options')) { ?>
								<li><a href="<?php echo $this->url->link('seller/account-option', '', 'SSL'); ?>"><?php echo $ms_account_options; ?></a></li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>

				<div class="table-responsive">
					<table class="mm_dashboard_table table table-borderless table-hover" id="list-products">
						<thead>
							<tr>
								<td class="mm_size_small"></td>
								<td><?php echo $ms_account_products_product; ?></td>
								<td class="mm_size_small"><?php echo $ms_account_product_price; ?></td>
								<td class="col-md-1"><?php echo $ms_account_product_quantity ;?></td>
								<td class="mm_size_small"><?php echo $ms_account_products_earnings; ?></td>
								<td class="mm_size_small"><?php echo $ms_account_products_sales; ?></td>
								<td><?php echo $ms_account_products_status; ?></td>
								<td class="mm_size_medium"></td>
							</tr>

							<tr class="filter">
								<td></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td><input type="text"/></td>
								<td></td>
								<td></td>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<script>
	$(function() {
		$('#list-products').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-product/getTableData",
			"aoColumns": [
				{ "mData": "image", "bSortable": false },
				{ "mData": "product_name", "sClass": "text-left" },
				{ "mData": "product_price" },
				{ "mData": "quantity"},
				{ "mData": "product_earnings" },
				{ "mData": "number_sold" },
				{ "mData": "product_status" },
				{ "mData": "actions", "bSortable": false, "sClass": "text-right" }
			]
		});

		$(document).on('click', '.ms-button-delete', function() {
			if (!confirm('<?php echo $ms_account_products_confirmdelete; ?>')) return false;
		});
	});
</script>
<?php echo $footer; ?>