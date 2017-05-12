$(function() {
	/**
	 * Save seller settings
	 */
	$("#ms-submit-button").click(function (e) {
		var data = $('#ms-sellersettings').serialize();
		$.ajax({
			url: 'index.php?route=seller/account-setting/jxsavesellerinfo',
			data: data,
			dataType: 'json',
			type: 'post',
			success: function (jsonData) {
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('.error').text('');

					for (error in jsonData.errors) {
						if ($('#error_' + error).length > 0) {
							$('#error_' + error).text(jsonData.errors[error]);
							$('#error_' + error).parents('.form-group').addClass('has-error');
						} else if ($('[name="settings[' + error + ']"]').length > 0) {
							$('[name="settings[' + error + ']"]').parents('.form-group').addClass('has-error');
							$('[name="settings[' + error + ']"]').parents('div:first').append('<p class="error" id="error_' + error + '">' + jsonData.errors[error] + '</p>');
						} else $(".warning.main").append("<p>" + jsonData.errors[error] + "</p>").show();
					}
				} else {
					window.location.reload();
				}
			},
			error: function (error) {
				console.log(error);
			}
		});
	});


	/**
	 * Seller settings > General tab
	 */
	var logo =  initUploader('ms-logo', 'index.php?route=seller/account-setting/jxUploadSellerLogo', 'mini', false, false);

	$("#seller_settings_logo").delegate(".ms-remove", "click", function() {
		var par = $(this).parent();
		par.addClass('hidden');
		par.find('input').val('');
		par.parent().find('.dragndropmini').show();
		par.parent().find('.dragndropmini').removeClass('hidden');
	});

    /**
     * Offer image upload
     */
    var by_image  =  initUploader('by_image',  'index.php?route=multimerch/account_offer/uploadImage', 'mini', false, false);
    var for_image =  initUploader('for_image', 'index.php?route=multimerch/account_offer/uploadImage', 'mini', false, false);

    $("#offer_by_image").delegate(".ms-remove", "click", function() {
        var par = $(this).parent();
        par.addClass('hidden');
        par.find('input').val('');
        par.parent().find('.dragndropmini').show();
        par.parent().find('.dragndropmini').removeClass('hidden');
    });

    $("#offer_for_image").delegate(".ms-remove", "click", function() {
        var par = $(this).parent();
        par.addClass('hidden');
        par.find('input').val('');
        par.parent().find('.dragndropmini').show();
        par.parent().find('.dragndropmini').removeClass('hidden');
    });

	/**
	 * Seller settings > Payments tab
	 */
	$("ul.pg-topbar li").click(function(e) {
		if($(document).find('.pg-message').length !== 0) {
			$(".pg-message").remove();
		}
	});

	$(".ms-pg-submit").click(function (e) {
		var form = $(this).closest('form');
		var pg_name = $(form).find('#pg-name').val();
		var data = $(form).serialize();

		$.ajax({
			url: 'index.php?route=multimerch/payment/' + pg_name + '/jxSaveSettings',
			data: data,
			dataType: 'json',
			type: 'post',
			success: function (jsonData) {
				var html = '';
				if(jsonData['success']) {
					html += '<div class="alert alert-success pg-message">' + jsonData['success'] + '</div>';
				}
				if(jsonData['error']) {
					html += '<div class="alert alert-danger pg-message">';
					html += '<ul>';
					$.map(jsonData['error'], function(value, index) {
						html += '<li>' + value + '</li>';
					});
					html += '</ul>';
					html += '</div>';
				}
				if($(document).find('.pg-message').length !== 0) {
					$(".pg-message").remove();
				}
				$(".pg-topbar").after(html);
			},
			error: function (error) {
				console.error(error);
			}
		});
	});


	/**
	 * Seller settings > Shipping tab
	 */
	(function() {
		var $sellerShippingContainer = $('#tab-seller-shipping'),
			$currentRequest = null;

		$(document).on('focus', '.ac-shipping-from-country', shippingFieldAutocomplete('from_country'));
		$(document).on('focus', '.ac-shipping-locations', shippingFieldAutocomplete('location'));
		$(document).on('focus', '.ac-shipping-methods', shippingFieldAutocomplete('method'));

		$(document).on('focusout', '.ac-shipping-from-country, .ac-shipping-locations, .ac-shipping-methods', function() {
			if($(this).siblings('input[type="hidden"]').val() == 0 && !$(this).hasClass('ac-shipping-locations')) $(this).val('');
		});

		function shippingFieldAutocomplete(shippingFieldType) {
			if (shippingFieldType == undefined) {
				shippingFieldType = 'method';
			}

			return function () {
				var $this = $(this);
				if($sellerShippingContainer.find('ul.dropdown-menu').length) {
					$sellerShippingContainer.find('ul.dropdown-menu').remove();
				}

				var itemLabelKey = 'name',
					itemValueKey = '',
					sourceUrl = function(reqEndPoint) {
						var url = 'index.php?route=seller/account-setting';
						url += reqEndPoint;
						return function (request) {
							return url + '&filter_name=' + encodeURIComponent(request);
						}
					};

				// Shipping locations
				if (shippingFieldType == 'location') {
					sourceUrl = sourceUrl('/jxAutocompleteShippingLocation&referrer=' + encodeURIComponent('to_geo_zone'));
					itemValueKey = 'location_id';

				// Shipping from country
				} else if(shippingFieldType == 'from_country') {
					sourceUrl = sourceUrl('/jxAutocompleteShippingLocation&referrer=' + encodeURIComponent('country_from'));
					itemValueKey = 'location_id';

				// Shipping companies
				} else {
					sourceUrl = sourceUrl('/jxAutocompleteShippingMethod');
					itemValueKey = 'method_id';
				}

				$(this).autocomplete({
					'delay': 250,
					'source': function(request, response) {
						$currentRequest = $.ajax({
							url: sourceUrl(request),
							dataType: 'json',
							beforeSend: function() {
								if($currentRequest != null) {
									$currentRequest.abort();
								}
							},
							success: function(json) {
								var items = $.map(json, function(item) {
									return {
										label: item[itemLabelKey],
										value: item[itemValueKey]
									}
								});

								if(shippingFieldType == 'location' && items.length > 1) {
									setTimeout(function() {
										$this.siblings('ul.dropdown-menu').children('li').first().css('margin-bottom', '10px');
									}, 5);
								}

								response(items.slice(0, 10));
								$currentRequest = null;
							},
							error: function(e) {
								console.error(e);
								$currentRequest = null;
							}
						});
					},
					'select': function(item) {
						$this.val(item.label);
						$this.siblings('.' + itemValueKey.replace(/\_/g, '-')).val(item.value);
					}
				});
			}
		}
	})();

	$(".ffClone").click(function(e) {
		var lastRow = $(this).closest('.panel').find('table tbody tr:last input:last').attr('name');

		if (typeof lastRow == "undefined") {
			var newRowNum = 1;
		} else {
			var newRowNum = parseInt(lastRow.match(/[0-9]+/)) + 1;
		}

		var newRow = $(this).closest('.panel').find('table tbody tr.ffSample').clone();

		newRow.find('input,select').attr('name', function(i,name) {
			return name.replace('[0]','[' + newRowNum + ']');
		});

		$(this).closest('.panel').find('table tbody tr:last').after(newRow.removeClass('ffSample'));
	});

	$(document).on('click', '.mm_remove', function(e) {
		e.preventDefault();
		$(this).closest('tr').remove();
	});

	$("#ms-ssm-submit").click(function() {
		var data = $('#ms-seller-shipping').serialize();

		$.ajax({
			url: 'index.php?route=seller/account-setting/jxSaveSellerShipping',
			data: data,
			dataType: 'json',
			type: 'post',
			success: function (jsonData) {
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					for (error in jsonData.errors) {
						var $element = $('[name="' + error + '"]');

						if ($element.length > 0) {
							if(!$element.closest('td').hasClass('has-error'))
								$element.closest('td').addClass('has-error');

							if($element.closest('td').find('p.error').length == 0)
								$element.closest('td').append('<p class="error" style="width: 100px;" id="error_' + error + '">' + jsonData.errors[error] + '</p>');
						} else {
							$('#ssm-error-holder').removeClass('hidden');
							$('#ssm-error-holder').text(jsonData.errors[error]);
						}
					}
				} else {
					window.location.reload();
				}
			},
			error: function (error) {
				// console.log(error);
			}
		});
	});
});