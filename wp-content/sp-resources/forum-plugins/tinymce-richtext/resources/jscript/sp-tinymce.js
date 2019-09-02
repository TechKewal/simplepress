/* ---------------------------------
Simple:Press
TinyMCE Editor Plugin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	/* ---------------------------------------------
	   Open the dropdown editor area
	--------------------------------------------- */
	spj.editorOpen = function(formType, ajax) {
		if (ajax) {
			tinyMCE.editors=[];
			tinyMCE.EditorManager.execCommand('mceAddEditor', true, 'postitem');
		} else {
			tinyMCE.EditorManager.execCommand('mceAddControl', true, 'postitem');
		}

		if (formType == 'topic') {
			document.addtopic.spTopicTitle.focus();
		}

		$('#postitem').on("closed", function(event, ui) {
			tinyMCE.EditorManager.execCommand('mceAddControl', true, 'postitem');
		});
	};

	/* ---------------------------------------------
	   Cancels editor - removes any content
	--------------------------------------------- */
	spj.editorCancel = function() {
		tinyMCE.EditorManager.execCommand('mceRemoveControl', true, 'postitem');

		tinymce.activeEditor.setContent('');

		$('#spPostNotifications').html('');
		$('#spPostNotifications').hide();

		if (document.getElementById('previewPost') != 'undefined') {
			$('#previewPost').html('');
		}

		// remove any data in custom fields
		$('#spEditorCustomDiv').html('');
		$('#spEditorCustomValue').val('');

		spj.toggleLayer('spPostForm');
	};

	/* ---------------------------------------------
	   Insert content as Quote
	--------------------------------------------- */

	spj.editorInsertContent = function(intro, content) {
		function nl2br(str) {return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br ' + '/>' + '$2');}

		tinymce.activeEditor.insertContent('<blockquote class="spPostEmbedQuote"><strong>' + intro + '</strong><br />' + nl2br(content) + '&nbsp;&nbsp;</blockquote><br />');
	};

	/* ---------------------------------------------
	   set text
	--------------------------------------------- */
	spj.editorSetText = function(text) {
		tinymce.activeEditor.setContent('');
		tinymce.activeEditor.insertContent(text);
	};

	/* ---------------------------------------------
	   Insert a Smiley
	--------------------------------------------- */
	spj.editorInsertSmiley = function(file, title, path, code) {
		tinymce.activeEditor.insertContent('<img src="'+path+file+'" title="'+title+'" alt="'+title+'" />');
	};

	/* ---------------------------------------------
	   Insert an Attachment
	--------------------------------------------- */
	spj.editorInsertAttachment = function(file, title, path, item, width, height, twidth, theight) {
		var html = '<img data-upload="1" data-width="'+width+'" data-height="'+height+'" src="'+path+file+'" title="'+title+'" alt="'+title+'"';
		if (twidth != '') {
			html = html + ' width="'+twidth+'"';
		}
		if (theight != '') {
			html = html + ' height="'+theight+'"';
		}
		html = html + ' />';

		tinymce.activeEditor.insertContent(html);
	};

	spj.editorInsertMediaAttachment = function(file, path, width, height) {
		var audio = new Array('aac', 'ac3',  'aif',  'aiff', 'm3a',  'm4a',   'm4b',  'mka',  'mp1',  'mp2',  'mp3', 'ogg', 'oga', 'ram', 'wav', 'wma');
		var video = new Array('asf', 'avi',  'divx', 'dv',   'flv',  'm4v',   'mkv',  'mov',  'mp4',  'mpeg', 'mpg', 'mpv', 'ogm', 'ogv', 'qt',  'rm', 'vob', 'wmv');

		ext = file.split('.').pop();
		if ($.inArray(ext, audio) != -1) {
			html = "<audio controls=\"controls\" src=\"" + path + file + "\"></audio>";
			html = html + '<p></p>';
		} else if ($.inArray(ext, video) != -1) {
			html = "<video width=\"" + width + "\" height=\"" + height + "\" controls=\"controls\" src=\"" + path + file + "\"></video>";
			html = html + '<p></p>';
		} else {
			html = '';
		}

		if (html != '') tinymce.activeEditor.insertContent(html);
	};

	spj.editorInsertFileAttachment = function(file, path) {
		var html = '<a href="'+path+file+'">'+file+'</a>';
		if (html != '') tinymce.activeEditor.insertContent(html);
	};

	/* ---------------------------------------------
	   Insert text
	--------------------------------------------- */
	spj.editorInsertText = function(text) {
		tinymce.activeEditor.insertContent(text);
	};

	/* ---------------------------------------------
	   Get the current content of the editor
	--------------------------------------------- */
	spj.editorGetContent = function(theForm) {
		var content = tinymce.activeEditor.getContent();
		return content;
	};

	/* ---------------------------------------------
	   Validate editor content for known failures
	--------------------------------------------- */
	spj.editorValidateContent = function(theField, errorMsg) {
		var error = '';
		var stuff = tinyMCE.get(theField.name).getContent();
		if (stuff === '') {
			error = '<strong>' + errorMsg + '</strong><br />';
		}
		return error;
	};

	spj.editorGetSignature = function(a) {
		if (typeof tinyMCE !== 'undefined') tinyMCE.triggerSave();
	};
}(window.spj = window.spj || {}, jQuery));
