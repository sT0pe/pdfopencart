<input type="hidden" id="total_reviews" value="<?php echo $total_reviews ;?>">
<?php if($total_reviews > 0) :?>
<div class="review-stars">
	<h3><?php echo $mm_review_comments_title ;?></h3>
	<div class="review-stars-left col-sm-6 col-xs-12">
		<div class="ratings">
			<div class="rating-stars">
				<input id="rating-xs-1" name="rating" class="rating" data-min="0" data-max="5" data-step="1" data-size="xs" data-readonly="true" value="<?php echo $avg_rating ;?>">
			</div>
			<span class="rating-summary">
				<?php echo $mm_review_rating_summary ;?>
			</span>
		</div>
		<div class="rating-stats">
			<?php foreach($rating_stats as $star => $info) :?>
			<div class="rating-row">
				<span><?php echo sprintf($mm_review_stats_stars, $star) ;?></span>
				<div class="progress">
					<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $info['percentage'] ;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $info['percentage'] ;?>%;"></div>
				</div>
				<span><?php echo $info['votes'] ;?></span>
			</div>
			<?php endforeach ;?>
		</div>
	</div>
	<div class="review-stars-right col-sm-6 col-xs-12">
		<h5><?php echo $mm_review_seller_profile_history ;?></h5>
		<div class="review-history table-responsive">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th></th>
						<th><?php echo $mm_review_one_month ;?></th>
						<th><?php echo $mm_review_three_months ;?></th>
						<th><?php echo $mm_review_six_months ;?></th>
						<th><?php echo $mm_review_twelve_months ;?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($feedback_history as $key => $history) :?>
					<tr>
						<td><?php echo $key == 'positive' ? $mm_review_seller_profile_history_positive : ( $key == 'neutral' ? $mm_review_seller_profile_history_neutral : $mm_review_seller_profile_history_negative) ;?></td>
						<td><?php echo $history['one_month'] ;?></td>
						<td><?php echo $history['three_months'] ;?></td>
						<td><?php echo $history['six_months'] ;?></td>
						<td><?php echo $history['twelve_months'] ;?></td>
					</tr>
				<?php endforeach ;?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="cl"></div>
<div class="data-container review-comments"></div>
<div id="review-comments-pag"></div>
<?php else :?>
	<h3><?php echo $mm_review_no_reviews ;?></h3>
<?php endif ;?>

