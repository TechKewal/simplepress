(function(spj, $, undefined) {
	function getContentFromEditor()
	{
		var data = '';
		if ((typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) { /* Tiny MCE editor */
			data = spj.editorGetContent();
		} else {   /* bbcode, html and textarea editors */
			data = $("#postitem").val();
		}

		/* Trim data */
		data = data.replace(/^\s+/, '' ).replace( /\s+$/, '');
		if (data != '') {
			data = strip_tags(data);
		}

		return data;
	}

	function registerClickTags() {
		$("#spTagsSuggested .spTagClickContainer a").click(function() {
			addTag(this.innerHTML);
		});

		$('#spTagsLoading').hide();
		if ($('#spTagsSuggested .spTagsInside').css('display') != 'block')
		{
			$('#spTagsSuggested').toggleClass('closed');
		}
	}

	function strip_tags(str) {
	   return str.replace(/&lt;\/?[^&gt;]+&gt;/gi, "");
	}

	function addTag(tag) {
		/* Trim tag */
		tag = tag.replace(/^\s+/, '' ).replace( /\s+$/, '');

		var newtags = $('#spTopicTags').val();
		var tagexp = new RegExp('\\b'+tag+'\\b','i');
		if (!tagexp.test(newtags)) {
			newtags += ',' + tag;
		}

		/* massage */
		newtags = newtags.replace(/\s+,+\s*/g, ',').replace(/,+/g, ',').replace(/,+\s+,+/g, ',').replace(/,+\s*$/g, '').replace(/^\s*,+/g, '');
		$('#spTopicTags').val(newtags);
	}

	/*****************************
	event handlers
	*****************************/

	sptags_show_topic_titles = {
		init : function() {
			$('.spTopicTagsShow').click( function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.id);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		/* Yahoo API */
		$("a.yahoo_api").click(function() {
			$('#spTagsLoading').show();
			nonce = $("#spTagsNonce").val();
			$("#spTagsSuggested .spTagClickContainer").load(sfSettings.url + 'tags-ajax&_wpnonce=' + nonce + '&targetaction=tags_from_yahoo', {content:getContentFromEditor(), title:$("#spTopicTitle").val(), tags:$("#spTopicTags").val()}, function(){
				registerClickTags();
			});
			return false;
		});

		/* Tag The Net API */
		$("a.ttn_api").click(function() {
			$('#spTagsLoading').show();
			nonce = $("#spTagsNonce").val();
			$("#spTagsSuggested .spTagClickContainer").load( sfSettings.url + 'tags-ajax&_wpnonce=' + nonce + '&targetaction=tags_from_tagthenet', {content:getContentFromEditor(),title:$("#spTopicTitle").val()}, function(){
				registerClickTags();
			});
			return false;
		});

		/* Local Tags Database */
		$("a.local_db").click(function() {
			$('#spTagsLoading').show();
			nonce = $("#spTagsNonce").val();
			$("#spTagsSuggested .spTagClickContainer").load( sfSettings.url + 'tags-ajax&_wpnonce=' + nonce + '&targetaction=tags_from_local_db', {content:getContentFromEditor(),title:$("#spTopicTitle").val()}, function(){
				registerClickTags();
			});
			return false;
		});

		sptags_show_topic_titles.init();
	});
}(window.spj = window.spj || {}, jQuery));
