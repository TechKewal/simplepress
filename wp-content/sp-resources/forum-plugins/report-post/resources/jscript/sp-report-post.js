/* ---------------------------------
Simple:Press
Report Post Plugin Javascript
------------------------------------ */

/*****************************
event handlers
*****************************/

(function(spj, $, undefined) {
	sprp_report_post = {
		init : function() {
			$('.spReportPostReturn').click( function() {
				var mydata = $(this).data();
				spj.redirect(mydata.url);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		sprp_report_post.init();
	});
}(window.spj = window.spj || {}, jQuery));
