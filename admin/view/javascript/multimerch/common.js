$(function() {
	$.fn.dataTableExt.sErrMode = 'throw';

	if (typeof msGlobals.config_language != 'undefined') {
		$.extend($.fn.dataTable.defaults, {
			"oLanguage": {
				"sUrl": msGlobals.config_language
			}
		});
	}

	$.extend($.fn.dataTable.defaults, {
		"bProcessing": true,
		"bSortCellsTop": true,
		"bServerSide": true,
		"sPaginationType": "full_numbers",
		"aaSorting": [],
		"bAutoWidth": false,
		"bLengthChange": false,
		"iDisplayLength": 50
		//"iDisplayLength": msGlobals.config_admin_limit
		/*
		"fnDrawCallback":function(){
			if ( $('.dataTables_paginate span span.paginate_button').size()) {
				$('.dataTables_paginate')[0].style.display = "block";
			} else {
				$('.dataTables_paginate')[0].style.display = "none";
			}
		}*/
	});
	
	
	$("body").delegate(".dataTable .filter input[type='text']", "keyup",  function() {
		$(this).parents(".dataTable").dataTable().fnFilter(this.value, $(this).parents(".dataTable").find("thead tr.filter td").index($(this).parent("td")));
	});

	$(document).ready(function() {
		$('.input-date-datepicker').datetimepicker({
			format: 'YYYY-MM-DD',
			pickTime: false,
			useCurrent: false,
			focusOnShow: false,
			showClear: true
		})
		.on('dp.change', function() {
			$(this).trigger('keyup');
		})
		.attr('readonly', 'readonly');
	});
});