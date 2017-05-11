$(function() {
	$('body').delegate(".ffRemove", "click", function() {
		$(this).parents('tr').remove();
	});

	$('body').delegate(".ffClone", "click", function() {
		var lastRow = $(this).parents('table').find('tbody tr:last input:last').attr('name');
		if (typeof lastRow == "undefined") {
			var newRowNum = 1;
		} else {
			var newRowNum = parseInt(lastRow.match(/[0-9]+/)) + 1;
		}

		var newRow = $(this).parents('table').find('tbody tr.ffSample').clone();
		newRow.find('input,select').attr('name', function(i,name) {
			return name.replace('[0]','[' + newRowNum + ']');
		});
	
		$(this).parents('table').find('tbody').append(newRow.removeAttr('class'));
	});
});
