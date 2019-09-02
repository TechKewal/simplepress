(function(spj, $, undefined) {
	/***********************************************
	event handlers
	***********************************************/
	postAsToggle = {
		init : function() {
			$('#sfPostAs').change( function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.target);
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		postAsToggle.init();
	});
}(window.spj = window.spj || {}, jQuery));
