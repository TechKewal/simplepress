(function(spj, $, undefined) {
	spprint_go_back = {
		init : function() {
			$('.spPrintTopicGoBack').click( function() {
				history.go(-1);
			});
		}
	};

	spprint_print_topic = {
		init : function() {
			$('.spPrintTopicPrint').click( function() {
				$("#spMainContainer").printThis();
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		spprint_go_back.init();
		spprint_print_topic.init();
	});
}(window.spj = window.spj || {}, jQuery));
