<?php echo $header; ?>
<div class="container">
	<?php if (isset($success) && $success) { ?>
	<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
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
			<div class="mm_dashboard col-sm-12">
				<h1><i class="fa fa-tachometer"></i><?php echo $ms_account_dashboard ;?></h1>
				<div class="mm_boards col-md-12">
					<div class="col-md-4">
						<div class="mm_dashboard_block">
							<div class="head"><?php echo $ms_account_orders ;?></div>
							<a><?php echo $seller['total_orders'] ;?></a>
							<p><?php echo $seller['sales_month'];?> <?php echo $ms_account_dashboard_in_month ;?></p>
						</div>
					</div>
					<div class="col-md-4">
						<div class="mm_dashboard_block">
							<div class="head"><?php echo $ms_account_revenue ;?></div>
							<a><?php echo $seller['total_earnings'] ;?></a>
							<p><?php echo $seller['earnings_month'] ;?> <?php echo $ms_account_dashboard_in_month ;?></p>
						</div>
					</div>
					<div class="col-md-4">
						<div class="mm_dashboard_block">
							<div class="head"><?php echo $ms_account_views ;?></div>
							<a><?php echo $seller['total_views'] ?></a>
						</div>
					</div>
				</div>
				<div class="mm_submead"><h3><?php echo $ms_account_dashboard_orders ;?></h3><a class="mm_view" href="<?php echo $this->url->link('seller/account-order', '', 'SSL'); ?>"><?php echo $ms_account_dashboard_view_all_orders; ?></a></div>

				<div class="table-responsive">
				<table class="mm_dashboard_table table table-borderless table-hover" id="mm_dashboard_orders">
					<thead>
					<tr>
						<td class="mm_size_small"><?php echo $ms_account_orders_id; ?></td>
						<td class="mm_size_medium"><?php echo $ms_status; ?></td>
						<td class="mm_size_medium"><?php echo $ms_date_created; ?></td>
						<td class="mm_size_large"><?php echo $ms_account_orders_products; ?></td>
						<td class="mm_size_medium"><?php echo $ms_account_orders_total; ?></td>
						<td class="mm_size_small"><?php echo $ms_action; ?></td>
					</tr>
					</thead>

					<tbody>
					<?php if (isset($orders) && $orders) { ?>
						<?php foreach ($orders as $order) { ?>
						<tr>
							<td><?php echo $order['order_id']; ?></td>
							<td><?php echo $order['status']; ?></td>
							<td><?php echo $order['date_created']; ?></td>
							<td class="products">
								<?php foreach ($order['products'] as $p) { ?>
								<p class="product_options">
									<span class="name"><?php if ($p['quantity'] > 1) { echo "{$p['quantity']} x "; } ?> <a href="<?php echo $this->url->link('product/product', 'product_id=' . $p['product_id'], 'SSL'); ?>"><?php echo $p['name']; ?></a></span>
									<?php foreach ($p['options'] as $option) { ?>
									<br />
									&nbsp;<small> - <?php echo $option['name']; ?>:<?php echo $option['value']; ?></small>
									<?php } ?>
									<span class="total"><?php echo $this->currency->format($p['seller_net_amt'] + $p['shipping_cost'], $this->config->get('config_currency')); ?></span>
								</p>
								<?php } ?>
							</td>
							<td><?php echo $order['total']; ?></td>
							<td class="action">
								<a class="icon-view" href="<?php echo $this->url->link('seller/account-order/viewOrder', 'order_id=' . $order['order_id']); ?>" title="<?php echo $this->language->get('ms_view_modify') ;?>"><i class="fa fa-search"></i></a>
								<a class="icon-invoice" href="<?php echo $this->url->link('seller/account-order/invoice', 'order_id=' . $order['order_id']); ?>" title="<?php echo $this->language->get('ms_view_invoice') ;?> "><i class="fa fa-file-text-o"></i></a>
							</td>
						</tr>
						<?php } ?>
					<?php } else { ?>
					<tr>
						<td class="center" colspan="6"><?php echo $ms_account_orders_noorders; ?></td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
				</div>

			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<?php echo $footer; ?>
