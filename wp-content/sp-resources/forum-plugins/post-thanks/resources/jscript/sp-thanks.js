/* ---------------------------------
Simple:Press
Post Thanks Bar Plugin Javascript
------------------------------------ */
(function(spj, $, undefined) {
	function thankPost(url, id, thanked, img, iclass) {
		/* update thanks list*/
		if ($('#spThanksList' + id).css('display') == 'none'){
		   $('#spThanksList' + id).show('fast');
		}

		var this_url = url + '&targetaction=thanks' + '&rnd=' +  new Date().getTime();
		$('#spThanksList' + id).load(this_url);

		/* change thanks button */
		this_url = url + '&targetaction=thanked' + '&string=' + encodeURI(thanked)+ '&image=' + img + '&iclass=' + encodeURI(iclass) + '&rnd=' +  new Date().getTime();
		$('#spThanks' + id).load(this_url);
	}

	/*****************************
	event handlers
	*****************************/

	spthanks_thank_post = {
		init : function() {
			$('.spThankPost').click( function() {
				var mydata = $(this).data();
				thankPost(mydata.url, mydata.postid, mydata.thanked, mydata.img, mydata.iclass);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		spthanks_thank_post.init();
	});
}(window.spj = window.spj || {}, jQuery));