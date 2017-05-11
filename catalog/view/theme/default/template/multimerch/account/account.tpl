<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($success) { ?>
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
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h2><?php echo $text_my_account; ?></h2>
      <ul class="list-unstyled">
        <li><a href="<?php echo $edit; ?>"><i class="fa fa-edit"></i><span><?php echo $ms_account_edit_info; ?></span></a></li>
        <li><a href="<?php echo $password; ?>"><i class="fa fa-lock"></i><span><?php echo $ms_account_password; ?></span></a></li>
		<li><a href="<?php echo $newsletter; ?>"><i class="fa fa-newspaper-o"></i><span><?php echo $ms_account_newsletter; ?></span></a></li>
		<li><a href="<?php echo $logout; ?>"><i class="fa fa-sign-out"></i><span><?php echo $ms_account_logout; ?></span></a></li>

      </ul>
      <h2><?php echo $ms_account_customer; ?></h2>
      <ul class="list-unstyled">
        <li><a href="<?php echo $order; ?>"><i class="fa fa-shopping-cart"></i><span><?php echo $ms_account_order_history; ?></span></a></li>
        <li><a href="<?php echo $download; ?>"><i class="fa fa-download"></i><span><?php echo $text_download; ?></span></a></li>
		<li><a href="<?php echo $address; ?>"><i class="fa fa-home"></i><span><?php echo $ms_account_address_book; ?></a></li>
        <li><a href="<?php echo $wishlist; ?>"><i class="fa fa-heart"></i><span><?php echo $ms_account_wishlist; ?></a></li>
		<?php if ($this->config->get('mmess_conf_enable')) { ?>
			<li><a href="<?php echo $this->url->link('account/msconversation', '', 'SSL'); ?>"><i class="fa fa-envelope"></i><span><?php echo $ms_account_messages; ?></span></a></li>
		<?php } ?>

        <?php if ($reward) { ?>
        <li><a href="<?php echo $reward; ?>"><i class="fa fa-star"></i><span><?php echo $ms_account_reward_points; ?></span></a></li>
        <?php } ?>

        <li><a href="<?php echo $return; ?>"><i class="fa fa-reply"></i><span><?php echo $ms_account_returns; ?></span></a></li>
        <li><a href="<?php echo $transaction; ?>"><i class="fa fa-book"></i><span><?php echo $ms_account_transactions; ?></span></a></li>
        <li><a href="<?php echo $recurring; ?>"><i class="fa fa-usd"></i><span><?php echo $text_recurring; ?></span></a></li>
      </ul>

		<h2><?php echo $ms_seller; ?></h2>
		<ul class="list-unstyled <?php if ($this->config->get('msconf_graphical_sellermenu')) { ?>graphical<?php } ?>">
			<?php if ($ms_seller_created && $this->MsLoader->MsSeller->getStatus($this->customer->getId()) == MsSeller::STATUS_ACTIVE) { ?>
				<li><a href="<?php echo $this->url->link('seller/account-dashboard', '', 'SSL'); ?>"><i class="fa fa-tachometer"></i><span><?php echo $ms_account_dashboard; ?></span></a></li>
				<li><a href="<?php echo $this->url->link('seller/account-order', '', 'SSL'); ?>"><i class="fa fa-shopping-cart"></i><span><?php echo $ms_account_orders; ?></span></a></li>
				<li><a href="<?php echo $this->url->link('seller/account-product', '', 'SSL'); ?>"><i class="fa fa-briefcase"></i><span><?php echo $ms_account_products; ?></span></a></li>
				<li><a href="<?php echo $this->url->link('seller/account-transaction', '', 'SSL'); ?>"><i class="fa fa-book"></i><span><?php echo $ms_account_transactions; ?></span></a></li>
                <li><a href="<?php echo $this->url->link('seller/account-payment', '', 'SSL'); ?>"><i class="fa fa-usd"></i><span><?php echo $ms_account_payments; ?></span></a></li>
                <li><a href="<?php echo $this->url->link('seller/account-payment-request', '', 'SSL'); ?>"><i class="fa fa-credit-card"></i><span><?php echo $ms_account_payment_requests; ?></span></a></li>
                <li><a href="<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>"><i class="fa fa-user"></i><span><?php echo $ms_account_sellerinfo; ?></span></a></li>
                <!--<li><a href="<?php echo $this->url->link('seller/account-return', '', 'SSL'); ?>"><i class="fa fa-reply"></i><span><?php echo $ms_account_returns; ?></span></a></li>-->

                <?php if ($this->config->get('mmess_conf_enable')) { ?>
					<li><a href="<?php echo $this->url->link('account/msconversation', '', 'SSL'); ?>"><i class="fa fa-envelope"></i><span><?php echo $ms_account_messages; ?></span></a></li>
				<?php } ?>
				<li><a href="<?php echo $this->url->link('seller/account-setting', '', 'SSL'); ?>"><i class="fa fa-cog"></i><span><?php echo $ms_account_settings; ?></span></a></li>
			<?php } else { ?>
				<li><a href="<?php echo $this->url->link('seller/account-profile', '', 'SSL'); ?>"><i class="fa fa-user"></i><span><?php echo $ms_account_sellerinfo_new; ?></span></a></li>
			<?php } ?>
		</ul>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>
