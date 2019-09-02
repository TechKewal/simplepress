/* Simple:Press Threading Plugin Scripts */
(function(spj, $, undefined) {
	spj_threaded_post_button = {
		init : function() {
			$('.spNewThreadButton').off();
			$('.spNewThreadButton').click( function() {
				var mydata = $(this).data();
				spj_threaded_open_editor(mydata.postid, mydata.threadindex);
			});
		}
	};

	spj_threaded_post_button_editor = {
		init : function() {
			$('.spNewThreadButton').off();
			$('.spNewThreadButton').click( function() {
				if (confirm(sp_threading_vars.cancelverify)) {
					var mydata = $(this).data();
					spj_threaded_open_editor(mydata.postid, mydata.threadindex);
				}
			});
		}
	};

	function spj_threaded_open_editor(postid, threadindex) {
		/* hide the tooltip - sometimes seem to stay open */
		$('.ui-tooltip').hide();

		/* move the post form to the threaded post */
		$('#eachPost' + postid).after($('#spPostForm'));

		/* open the editor window */
		$('#spPostForm').slideDown();

		/* handle tinymce */
		if (parseInt(sp_platform_vars.editor) == 1) {
			$('.mce-tinymce.mce-container.mce-panel').remove();

			/* remove the current tinymce instance */
			tinyMCE.remove();
			tinyMCE.editors = [];
		}

		/* set up the thread index so we know where to put it */
		$('#spEditorCustomValue').val(threadindex);

		/* instatntiate the editor */
		spj.editorOpen('post', 1);

		/* handle tinymce */
		if (parseInt(sp_platform_vars.editor) == 1) {
			/* show the new editor instance */
			$('.mce-tinymce.mce-container.mce-panel').last().show();
		}

		$('.spNewThreadButton').off();
		$('.spTopicPostContainer .spCancelEditor').off();
		spj_threaded_post_button_editor.init();
		spj_threaded_cancel_button.init();
	}

	spj_threaded_cancel_button = {
		init : function() {
			$('.spTopicPostContainer .spCancelEditor').click( function(e) {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					/* cancel and then remove current editor */
					spj.editorCancel();

					if (parseInt(sp_platform_vars.editor) == 1) {
						tinymce.remove();
					}

					$('#spEditFormAnchor').after($('#spPostForm'));

					/* handle tinymce */
					if (parseInt(sp_platform_vars.editor) == 1) {
						/* reinit the new tinymce instance */
						tinymce.init(tinyMCEPreInit.mceInit['postitem']);

						/* show the new editor instance */
						$('.mce-tinymce.mce-container.mce-panel').last().show();
					}

					$('.spNewThreadButton').off();
					$('.spTopicPostContainer .spCancelEditor').off();
					spj_threaded_post_button.init();
				}
			});
		}
	};

	/*****************************
	threading event handlers
	*****************************/

	$(document).ready(function() {
		spj_threaded_post_button.init();
	});
}(window.spj = window.spj || {}, jQuery));
