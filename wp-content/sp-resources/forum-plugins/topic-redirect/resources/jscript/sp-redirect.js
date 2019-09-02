/***********************************************
event handlers
***********************************************/
(function(spj, $, undefined) {
	sp_redirect_toggle = {
		init : function() {
			$('#spRedirect').change( function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.target);
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		sp_redirect_toggle.init();
	});
}(window.spj = window.spj || {}, jQuery));
