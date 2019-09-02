(function(spj, $, undefined) {
	function viewThumb(item, url) {
		var p = $("#spFileTree").position();
		var top = $("#" + item).offset().top - $("#spFileTree").offset().top;
		$("#spFileThumb").css('position', 'absolute');
		$("#spFileThumb").css('left', (p.left+275));
		$("#spFileThumb").css('top', top+7);
		$("#spFileThumb").html("<img src='"+url+"' width='80' />");
		$("#spFileThumb").show();
	}

	function closeThumb() {
		$("#spFileThumb").html("");
		$("#spFileThumb").hide();
	}

	spuv_open_dialog = {
		init : function() {
			$('.spUploadsOpenDialog').click( function() {
				var mydata = $(this).data();
				spj.dialogAjax(this, mydata.site, mydata.label, mydata.width, mydata.height, mydata.align);
				$('#dialog, #spMobilePanel').on('tree-opened', function() {
					spuv_editor_insert_file.init();
				});
			});
		}
	};

	spuv_editor_insert_file = {
		init : function() {
			/* image insertions */
			$('.spUploadsEditorInsertImage').click( function() {
				var mydata = $(this).data();
				spj.editorInsertAttachment(mydata.file, mydata.file, mydata.url, '', mydata.width, mydata.height, mydata.twidth, mydata.theight);
				spuv_editor_insert_file.add(mydata.file, mydata.path);
			});

			$('.spUploadsEditorInsertImage').mouseover( function() {
				var mydata = $(this).data();
				if (mydata.mobile == 0) {
				   viewThumb(mydata.thumbfile, mydata.thumb);
				}
			});

			$('.spUploadsEditorInsertImage').mouseout( function() {
				var mydata = $(this).data();
				if (mydata.mobile == 0) {
				   closeThumb();
				}
			});

			/* media insertions */
			$('.spUploadsEditorInsertMedia').click( function() {
				var mydata = $(this).data();
				spj.editorInsertMediaAttachment(mydata.file, mydata.url, mydata.width, mydata.height);
				spuv_editor_insert_file.add(mydata.file, mydata.url, mydata.path);
			});

			/* file insertions */
			$('.spUploadsEditorInsertText').click( function() {
				var mydata = $(this).data();
				spj.editorInsertText('<a href=' + mydata.url + '>Download ' + mydata.file + '</a>');
				spuv_editor_insert_file.add(mydata.file, mydata.url, mydata.path);
			});
		},

		add: function(file, path) {
			if ($('#sp_uv_count').length) {
				$('#sp_uv_count').val(function(i, oldval) {return parseInt(oldval, 10) + 1;});
			} else {
				$('.spEditorFieldset > legend').after('<input type="hidden" value="1" id="sp_uv_count" name="sp_uv_count">');
			}

			var count = $('#sp_uv_count').val();
			$('.spEditorFieldset > #sp_uv_count').after('<input type="hidden" value="' + file + '" name="sp_uvfile_name_' + count + '">');
			$('.spEditorFieldset > #sp_uv_count').after('<input type="hidden" value="' + path + '" name="sp_uvfile_path_' + count + '">');
		}
	};

	/***********************************************
	load event handlers on forum tools dialog opened
	***********************************************/

	$(document).ready(function() {
		spuv_open_dialog.init();
	});
}(window.spj = window.spj || {}, jQuery));
