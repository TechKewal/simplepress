(function(spj, $, undefined) {
	function delPhoto(url, rowid) {
		$('#'+rowid).css({backgroundColor: '#ffcccc'});
		$('#'+rowid).fadeOut('slow');
		$('#'+rowid).load(url);
	}

	spj.profileViewThumb = function(item, url) {
		var i = $("#" + item).position();
		$("#spFileThumb").css('position', 'absolute');
		$("#spFileThumb").css('left', (i.left+250));
		$("#spFileThumb").css('top', i.top);
		$("#spFileThumb").html("<img src='"+url+"' width='150' />");
		$("#spFileThumb").show();
	};

	spj.profileCloseThumb = function() {
		$("#spFileThumb").html("");
		$("#spFileThumb").hide();
	};

	function removeAttachments() {
		var ref = $('#spAttachmentsTree').jstree(true),
		sel = ref.get_selected();
		if (!sel.length) {
			return false;
		}
		if (!confirm(sp_plup_vars.confirm)) {
			return false;
		}
		ref.delete_node(sel);
	}

	function openClose() {
		if ($('#spUploadsBox').css('display') == 'none') {
			$('#spUploadToggle').hide();
			$('#sp_file_uploader').show('fast', function() {
				$('#sp_file_uploader').plupload('refresh');
			});
			$('#sp_uploader_info').show();
		} else {
			$('#sp_file_uploader').hide();
			$('#sp_uploader_info').hide();
		}
		spj.openEditorBox('spUploadsBox');
	}

	function removeOpenClose() {
		if ($('#spUploadsBox').css('display') == 'none') {
			$('#sp_uploader_attachments').show();
		} else {
			$('#sp_uploader_attachments').hide();
		}
		spj.openEditorBox('spUploadsBox');
		$('.spPlupEditorAttachments').hide();
		$('.spFileUploaderRemoveButton').hide();
		$('.spUploadsViewerButton').hide();
	}

	/***********************************************
	event handlers
	***********************************************/

	profile_remove_attachment = {
		init : function() {
			$('.spPlupProfileRemoveAttachment').on('click', function() {
				var mydata = $(this).data();
				removeAttachments();
			});
		}
	};

	toggle_editor_button = {
		init : function() {
			$('.spPlupEditorButton').on('click', function() {
				openClose();
			});
		}
	};

	toggle_editor_remove_button = {
		init : function() {
			$('.spPlupEditorRemoveButton').on('click', function() {
				removeOpenClose();
			});
		}
	};

	toggle_editor_remove_cancel = {
		init : function() {
			$('.spPlupCancelRemove').on('click', function() {
				$('#spUploadsBox').slideToggle();
				$('#sp_uploader_attachments').hide();
			});
		}
	};

	toggle_editor_attachments = {
		init : function() {
			$('.spPlupEditorAttachments').on('click', function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.target1, 'fast');
				spj.toggleLayer(mydata.target2, 'fast');
				if (mydata.type == 'sig') {
					setTimeout(function() {spj.setProfileDataHeight();}, 250);
				} else if (mydata.type == 'photos') {
					setTimeout(function() {spj.setProfileDataHeight();}, 500);
				}
			});
		}
	};

	toggle_editor_attachments_remove = {
		init : function() {
			$('.spFileUploaderRemoveButton').click( function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.target, 'fast');
			});
		}
	};


	profile_delete_photo = {
		init : function() {
			$('.spPlupProfilePhotoDelete').on('click', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					delPhoto(mydata.url, mydata.target);
				}
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		toggle_editor_button.init();
		toggle_editor_remove_button.init();
		toggle_editor_attachments.init();
		toggle_editor_attachments_remove.init();
		toggle_editor_remove_cancel.init();

		$('#spProfileContent').on('profilecontentloaded', function() {
			profile_delete_photo.init();
			profile_remove_attachment.init();
			toggle_editor_attachments.init();
		});
	});
}(window.spj = window.spj || {}, jQuery));
