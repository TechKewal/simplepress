/***********************************************
event handlers
***********************************************/

(function(spj, $, undefined) {
	sp_sbu_toggle = {
		init : function() {
			$('#spCheckUser').change( function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.target);
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		sp_sbu_toggle.init();
	});
}(window.spj = window.spj || {}, jQuery));
