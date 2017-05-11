<input type="hidden" id="total_reviews" value="<?php echo $total_reviews ;?>">
<?php if($total_reviews > 0) :?>
<div class="review-stars">
    <h3><?php echo $mm_review_comments_title ;?></h3>
    <div class="review-stars-left">
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
</div>
<div class="cl"></div>
<div class="data-container review-comments"></div>
<div id="review-comments-pag"></div>
<?php else :?>
    <h3><?php echo $mm_review_no_reviews ;?></h3>
<?php endif ;?>

