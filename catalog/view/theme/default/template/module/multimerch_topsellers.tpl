<h3><?php echo $heading_title; ?></h3>
<div class="row module">
	<?php foreach ($sellers as $seller) { ?>
	  <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    	<div class="product-thumb transition">
		<div class="image">
			<a href="<?php echo $seller['href']; ?>"><img src="<?php echo $seller['thumb']; ?>" title="<?php echo $seller['nickname']; ?>" alt="<?php echo $seller['nickname']; ?>" /></a>
		</div>

		<div>
		<div class="caption">
			<h4><a href="<?php echo $seller['href']; ?>"><?php echo $seller['nickname']; ?></a></h4>
		</div>
		</div>
	  </div>
	  </div>
	<?php } ?>
</div>