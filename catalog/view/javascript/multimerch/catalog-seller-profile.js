// Get seller reviews
$(function() {
    var product_id = $(document).find('input[name="product_id"]').val();
    var seller_id = $(document).find('input[name="seller_id"]').val();

    function reviewsCallback() {
        var url = '';
        if(product_id !== undefined && product_id != 0) {
            url = "&product_id=" + product_id;
        } else if (seller_id !== undefined && seller_id != 0) {
            url = "&seller_id=" + seller_id;
        }

        $.ajax({
            url: "index.php?route=multimerch/product_review/jxReviewComments" + url,
            success: function (json) {
                json = $.parseJSON(json);
                var reviews = json.reviews;
                if(reviews.length) {
                    createReviewsPagination('review-comments-pag', reviews);
                } else {
                    $.getScript('catalog/view/javascript/star-rating.js');
                }
            }
        });
    }

    if(seller_id !== undefined && seller_id != 0) {
        $("#reviews").load("index.php?route=seller/catalog-seller/reviews&seller_id=" + seller_id, reviewsCallback);
    } else if (product_id !== undefined && product_id != 0) {
        $("#reviews").load("index.php?route=multimerch/product_review&product_id=" + product_id, reviewsCallback);
    }

    function createReviewsPagination(name, reviews){
        var container = $('#' + name);
        var sources = function(){
            var result = [];
            $.each(reviews, function (key, review) {
                var html = '';
                if(product_id !== undefined && product_id != 0) {
                    html += '<div class="comment">';
                    html += '   <div class="comment-header">';
                    html += '       <span class="comment-author-name">' + review.author.firstname + ' ' + review.author.lastname + '</span>';
                    html += '       <span class="comment-date">on ' + review.date_created + '</span>';
                    html += '       <input id="rating-xs-2" name="rating" class="rating" data-min="0" data-max="5" data-step="1" data-size="xs" data-readonly="true" value="' + review.rating + '">';
                    html += '   </div>';
                    html += '   <div class="comment-body">' + review.comment + '</div>';
                    html += '</div>';
                } else if (seller_id !== undefined && seller_id != 0) {
                    html += '<div class="comment">';
                    html += '   <div class="comment-header">';
                    html += '       <input id="rating-xs-2" name="rating" class="rating" data-min="0" data-max="5" data-step="1" data-size="xs" data-readonly="true" value="' + review.rating + '">';
                    html += '		<a href="' + review.product.href + '" class="product-name">' + review.product.name +  '<span class="prod-price">' + review.product.price + '</span></a>';
                    html += '   </div>';
                    html += '   <div class="comment-body">' + review.comment + '</div>';
                    html += '	<div class="comment-footer">';
                    html += '       <span class="comment-author-name">' + review.author.firstname + ' ' + review.author.lastname + '</span>';
                    html += '       <span class="comment-date">on ' + review.date_created + '</span>';
                    html += '	</div>';
                    html += '</div>';
                }

                result.push(html);
            });

            return result;
        }();

        var options = {
            dataSource: sources,
            pageSize: 5,
            className: 'paginationjs-theme-blue paginationjs-small',
            hideWhenLessThanOnePage: true,
            showPrevious: false,
            showNext: false,
            callback: function(response){
                container.prev().html(response);
            }
        };

        container.addHook('beforeInit', function(){
            $.getScript('catalog/view/javascript/star-rating.js');
        });

        container.pagination(options);

        container.addHook('beforePageOnClick', function(){
            $.getScript('catalog/view/javascript/star-rating.js');
        });

        return container;
    }
});