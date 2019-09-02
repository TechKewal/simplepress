(function(spj, $, undefined) {
	function getCategories(ajaxURL, checked, spinner) {
		if(checked) {
			$('#spCatList').html('<img src="' + spinner + '" />');
			$('#spCatList').show('slide');
			$('#spCatList').load(ajaxURL);
		} else {
			$('#spCatList').hide();
			var next = $('#spCatList').next();
			if (!next.is('br')) {
				$('#spCatList').after('<br>');
			}
		}
	}

	/***********************************************
	event handlers
	***********************************************/

	break_link = {
		init : function() {
			$('.spBreakLinkTopic').click( function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					spj.breakBlogLink(mydata.url, mydata.target);
				}
			});
		}
	};

	create_linked_post = {
		init : function() {
			$('#sfbloglink').change( function() {
				var mydata = $(this).data();
				getCategories(mydata.url, this.checked, mydata.img);
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		break_link.init();
		create_linked_post.init();
	});
}(window.spj = window.spj || {}, jQuery));
