<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/stylesheet/bootstrap.css" type="text/css" rel="stylesheet" />
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<script src="view/javascript/jquery/datetimepicker/moment.js" type="text/javascript"></script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="screen" />
<?php foreach ($styles as $style) { ?>
<link type="text/css" href="<?php echo $style['href']; ?>" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<script src="view/javascript/common.js" type="text/javascript"></script>

				<?php global $config; $this->config = $config; ?>
				<script type="text/javascript"> if (!window.console) console = {log: function() {}}; var msGlobals = { config_limit_admin: '<?php echo $this->config->get('config_limit_admin'); ?>', config_language: <?php echo $dt_language; ?> }; </script>
			
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
</head>
<body>
<div id="container">
<header id="header" class="navbar navbar-static-top">
  <div class="navbar-header">
    <?php if ($logged) { ?>
    <a type="button" id="button-menu" class="pull-left"><i class="fa fa-indent fa-lg"></i></a>
    <?php } ?>
    <a href="<?php echo $home; ?>" class="navbar-brand"><img src="view/image/logo.png" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" /></a></div>
  <?php if ($logged) { ?>
  <ul class="nav pull-right">
    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><?php if($alerts > 0) { ?><span class="label label-danger pull-left"><?php echo $alerts; ?></span><?php } ?> <i class="fa fa-bell fa-lg"></i></a>
      <ul class="dropdown-menu dropdown-menu-right alerts-dropdown">
        <li class="dropdown-header"><?php echo $text_order; ?></li>
        <li><a href="<?php echo $processing_status; ?>" style="display: block; overflow: auto;"><span class="label label-warning pull-right"><?php echo $processing_status_total; ?></span><?php echo $text_processing_status; ?></a></li>
        <li><a href="<?php echo $complete_status; ?>"><span class="label label-success pull-right"><?php echo $complete_status_total; ?></span><?php echo $text_complete_status; ?></a></li>
        <li><a href="<?php echo $return; ?>"><span class="label label-danger pull-right"><?php echo $return_total; ?></span><?php echo $text_return; ?></a></li>
        <li class="divider"></li>
        <li class="dropdown-header"><?php echo $text_customer; ?></li>
        <li><a href="<?php echo $online; ?>"><span class="label label-success pull-right"><?php echo $online_total; ?></span><?php echo $text_online; ?></a></li>
        <li><a href="<?php echo $customer_approval; ?>"><span class="label label-danger pull-right"><?php echo $customer_total; ?></span><?php echo $text_approval; ?></a></li>
        <li class="divider"></li>
        <li class="dropdown-header"><?php echo $text_product; ?></li>
        <li><a href="<?php echo $product; ?>"><span class="label label-danger pull-right"><?php echo $product_total; ?></span><?php echo $text_stock; ?></a></li>
        <li><a href="<?php echo $review; ?>"><span class="label label-danger pull-right"><?php echo $review_total; ?></span><?php echo $text_review; ?></a></li>
        <li class="divider"></li>
        <li class="dropdown-header"><?php echo $text_affiliate; ?></li>
        <li><a href="<?php echo $affiliate_approval; ?>"><span class="label label-danger pull-right"><?php echo $affiliate_total; ?></span><?php echo $text_approval; ?></a></li>
      </ul>
    </li>
    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-home fa-lg"></i></a>
      <ul class="dropdown-menu dropdown-menu-right">
        <li class="dropdown-header"><?php echo $text_store; ?></li>
        <?php foreach ($stores as $store) { ?>
        <li><a href="<?php echo $store['href']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
        <?php } ?>
      </ul>
    </li>
    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-life-ring fa-lg"></i></a>
      <ul class="dropdown-menu dropdown-menu-right">
        <li class="dropdown-header"><?php echo $text_help; ?></li>
        <li><a href="http://www.opencart.com" target="_blank"><?php echo $text_homepage; ?></a></li>
        <li><a href="http://docs.opencart.com" target="_blank"><?php echo $text_documentation; ?></a></li>
        <li><a href="http://forum.opencart.com" target="_blank"><?php echo $text_support; ?></a></li>
      </ul>
    </li>
    <li><a href="<?php echo $logout; ?>"><span class="hidden-xs hidden-sm hidden-md"><?php echo $text_logout; ?></span> <i class="fa fa-sign-out fa-lg"></i></a></li>
  </ul>
  <?php } ?>
</header>

				<?php if($logged) { ?>
					<?php if($mm_missing_files || $mm_unnecessary_files) { ?>
						<div class="alert alert-warning" style="margin: 10px auto; width: 60%;"><i class="fa fa-exclamation-circle"></i>
							You are running OpenCart <?php echo VERSION; ?>.
							<?php if (!empty($mm_missing_files)) { ?>
								<br /><br />The following files are missing on your installation that are needed for your current version of OpenCart:<br /><br />
								<ul>
									<?php foreach($mm_missing_files as $file) { ?>
										<li><?php echo $file; ?></li>
									<?php } ?>
								</ul>
								<br />Please upload them from the latest MultiMerch archive to prevent various errors.<br />
							<?php } ?>

							<?php if (!empty($mm_unnecessary_files)) { ?>
								<br /><br />The following files exist on your installation that are not needed for your current version of OpenCart:<br /><br />
								<ul>
									<?php foreach($mm_unnecessary_files as $file) { ?>
										<li><?php echo $file; ?></li>
									<?php } ?>
								</ul>
								<br />Please remove or disable these files to prevent various errors.<br />
							<?php } ?>
						</div>
					<?php } ?>

					<!-- PHP < 5.4 warning message -->
					<?php if(version_compare(phpversion(), '5.4.0', '<')) { ?>
						<div class="alert alert-warning" style="text-align:center; margin: 10px auto; width: 90%;"><i class="fa fa-exclamation-circle"></i><?php echo $this->language->get('ms_error_php_version'); ?></div>
					<?php } ?>

					<?php MsLoader::getInstance()->getRegistry()->get('load')->model('multimerch/upgrade'); ?>
					<?php if (MsLoader::getInstance()->MsHelper->isInstalled() && !MsLoader::getInstance()->getRegistry()->get('model_multimerch_upgrade')->isDbLatest()) { ?>
						<div class="alert alert-warning" style="text-align:center; margin: 10px auto; width: 90%;"><i class="fa fa-exclamation-circle"></i><?php echo sprintf($this->language->get('ms_db_upgrade'), $this->url->link('module/multimerch/upgradeDb', 'token=' . $this->session->data['token'], 'SSL'),MsLoader::getInstance()->getRegistry()->get('model_multimerch_upgrade')->getDbVersion(),$this->MsLoader->dbVer); ?></div>
					<?php } ?>
					<?php if (MsLoader::getInstance()->MsHelper->isInstalled() && !MsLoader::getInstance()->getRegistry()->get('model_multimerch_upgrade')->isFilesLatest()) { ?>
						<div class="alert alert-warning" style="text-align:center; margin: 10px auto; width: 90%;"><i class="fa fa-exclamation-circle"></i><?php echo sprintf($this->language->get('ms_files_upgrade'), $this->MsLoader->dbVer,MsLoader::getInstance()->getRegistry()->get('model_multimerch_upgrade')->getDbVersion()); ?></div>
					<?php } ?>
				<?php } ?>

				<?php if (isset($this->session->data['ms_db_latest'])) { ?>
					<div class="alert-success" style="text-align:center; margin: 10px"><?php echo $this->session->data['ms_db_latest']; ?></div>
					<?php unset($this->session->data['ms_db_latest']); ?>
				<?php } ?>
			
