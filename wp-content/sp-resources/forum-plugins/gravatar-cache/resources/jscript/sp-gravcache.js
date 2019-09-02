/* ---------------------------------
Simple:Press
Gravatar Cache Plugin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	function resetGravatar(url) {
		$('#gravreset').load(url, function() {
			$('#gravreset').hide();
		});
	}

	/*****************************
	event handlers
	*****************************/

	cache_reset = {
		init : function() {
			$('#gravreset').click( function() {
				var mydata = $(this).data();
				resetGravatar(mydata.url);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$('#spProfileContent').on('profilecontentloaded', function() {
		cache_reset.init();
	});
}(window.spj = window.spj || {}, jQuery));
