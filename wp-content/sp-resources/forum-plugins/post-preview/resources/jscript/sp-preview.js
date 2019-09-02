/* ---------------------------------
Simple:Press
Post Preview Plugin Javascript
------------------------------------ */
(function(spj, $, undefined) {
	function openPreview(url) {
		var cPost = '';
		$('#previewPost').show();
		if (typeof tinyMCE != "undefined" && tinyMCE.activeEditor) {
			cPost = spj.editorGetContent();
		} else {   /* bbcode, html and textarea editors */
			cPost = $("#postitem").val();
		}
		var fid = $( "input[name=forumid]" ).val();
		$.post(url, {'cPost': cPost, 'fid': fid}, function(a, b) {
			$('#previewPost').html('');
			$('#previewPost').html(a);
			$('#previewPost').show();
		});
	}

	/*****************************
	event handlers
	*****************************/

	preview = {
		init : function() {
			$('#sfpreview').click( function() {
				var mydata = $(this).data();
				openPreview(mydata.url);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		preview.init();
	});
}(window.spj = window.spj || {}, jQuery));
