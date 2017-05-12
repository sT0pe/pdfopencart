/**
 * Initializes pluploader
 * @param {String} id
 * @param {String} url
 * @param {String} size
 * @param {Boolean} multiple
 * @param {Boolean} isFile
 * @return {Number} paramsId
 */
function initUploader(id, url, size, multiple, isFile) {
	// Id of the DOM element or DOM element itself to use as a drop zone for Drag-n-Drop
	var drop_element = (multiple ? (id + '-dragndrop') : id);
	var target = $('#' + drop_element);

	// Possible extensions of files to upload
	var files_possible_extensions = 'zip, rar, pdf';

	// Possible extensions of images to upload
	var images_possible_extensions = 'png, jpg, jpeg';

	// Maximum file size of each type that the user can pick, in bytes
	var max_file_size = '10mb';
	if(drop_element == 'ms-avatar') max_file_size = '20mb';
	if(drop_element == 'ms-banner') max_file_size = '10mb';
	if(drop_element == 'ms-logo') max_file_size = '5mb';
    if(drop_element == 'by_image') max_file_size = '5mb';
    if(drop_element == 'for_image') max_file_size = '5mb';
	if(drop_element == 'ms-image-dragndrop') max_file_size = '20mb';
	if(drop_element == 'ms-file-dragndrop') max_file_size = '500mb';

	// A set of file type filters
	var filters = {
		title: (isFile ? "Files" : "Images"),
		extensions: (isFile ? files_possible_extensions : images_possible_extensions)
	};

	var uploader = new plupload.Uploader({
		browse_button : id,
		url: url,
		drop_element : drop_element,
		file_data_name: id,
		max_file_size: max_file_size,
		filters: filters,
		multi_selection: multiple,
		runtimes : 'html5',
		image_counter: 0,
		file_counter: 0
	});

	uploader.bind('Init', function(up, params) {
		if (uploader.features.dragdrop) {
			var style = (size == 'mini') ? 'dragndropmini' : 'dragndrop';

			target.ondragover = function(event) {
				event.dataTransfer.dropEffect = "copy";
				this.className = style + " dragover";
			};

			target.ondragenter = function() {
				this.className = style + " dragover";
			};

			target.ondragleave = function() {
				this.className = style;
			};

			target.ondrop = function() {
				this.className = style;
			};
		}
	});

	uploader.init();

	uploader.bind('BeforeUpload', function(up, file) {
		if(isFile) {
			up.settings.file_counter++;
			up.settings.multipart_params = {'fileCount': up.settings.file_counter}
		} else {
			up.settings.image_counter++;
			up.settings.multipart_params = {'imageCount': up.settings.image_counter}
		}
	});

	// Hide error messages and start upload immediately
	uploader.bind('FilesAdded', function(up, files) {
		$(target).siblings('.alert').hide();
		setTimeout(up.start(), 1); // "detach" from the main thread
	});

	// Show and hide progress bar on uploader state changes
	uploader.bind('StateChanged', function(up) {
		if (up.state == plupload.STOPPED) {
			$(target).siblings('.progress').fadeOut(300, function() { /*$(this).html("").hide();*/ });
		} else {
			$(target).siblings('.progress').show();
		}
	});

	// Change progress bar values on upload progress
	uploader.bind('UploadProgress', function(up, file) {
		// For some reason file.percent is always set to 100, therefore this workaround is needed
		var percent = file.size > 0 ? Math.ceil(file.loaded / file.size * 100) : 100;

		$(target).siblings('.progress').attr("aria-valuenow", percent);
		$(target).siblings('.progress').css('width', percent + '%');
		$(target).siblings('.progress').html(percent + '%');
	});

	// Fires on any errors
	uploader.bind("Error", function(up, err) {
		var err_message = '';
		var file = err.file;

		if (file) {
			var debug_message = err.message;
			if (err.details) {
				debug_message += " (" + err.details + ")";
			}

			if (err.code == plupload.FILE_SIZE_ERROR) {
				err_message = "Error: File too large:" + " " + file.name;
			}

			if (err.code == plupload.FILE_EXTENSION_ERROR) {
				err_message = "Error: Invalid file extension:" + " " + file.name;
			}
		}

		if (err.code === plupload.INIT_ERROR) {
			setTimeout(function() {
				uploader.destroy();
			}, 1);
			err_message = "Error: Plupload init error";
		}

		$(target).siblings('.alert').text(err_message).show();

		up.refresh();
	});

	// Post upload actions
	uploader.bind('FileUploaded', function(up, file, data) {
		data = $.parseJSON(data.response);

		// Show back-end errors if any
		if(data.errors.length > 0) {
			$(target).siblings('.alert').text(data.errors).show();
		} else {
			$(target).siblings('.alert').hide();

			var image = $(target).siblings('.ms-image');

			if(multiple) {
				for(var i = 0; i < data.files.length; i++) {
					var html = '';
					html += (isFile ? '<div class="file-holder"><i class="fa fa-file"></i>' : '<div class="image-holder">');
					html += (isFile ? ('<input type="hidden" name="product_downloads[][filename]" value="' + data.files[i].fileName + '"/>')
									: ('<input type="hidden" name="product_images[]" value="' + data.files[i].name + '"/>')
					);
					html += (isFile ? '' : ('<img src="' + data.files[i].thumb + '"/>'));
					html += '<span class="ms-remove"><i class="fa fa-times"></i></span>';
					html += (isFile ? ('<span class="file-name">' + data.files[i].fileMask + '</span>') : '');
					html += '</div>';

					image.append(html);
				}
			} else {
				image.children().hide().fadeIn(2000);
				$(target).hide();
				image.find('input').val(data.files[0].name);
				image.find('img').attr('src', data.files[0].thumb);
			}
			image.removeClass('hidden');
		}
	});

	return uploader;
}

$(document).ajaxSuccess(function() {
	setTimeout(function() {
		$(".ms-spinner" ).button('reset');
	}, 1000);
});

$(document).ajaxStart(function() {
	$(".ms-spinner").attr("data-loading-text","<i class='fa fa-spinner fa-spin '></i>")
	$(".ms-spinner").button('loading');
});
