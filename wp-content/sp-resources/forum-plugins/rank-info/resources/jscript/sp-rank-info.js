/* ---------------------------------
Simple:Press
Rank Info Plugin Javascript
------------------------------------ */

/*****************************
event handlers
*****************************/

(function(spj, $, undefined) {
	sprank_view_members = {
		init : function() {
			$('.spRankInfoView').click( function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.id);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		sprank_view_members.init();
	});
}(window.spj = window.spj || {}, jQuery));
