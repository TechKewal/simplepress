(function(spj, $, undefined) {
	spwarnings_remove_warnings = {
		init : function() {
			$('.spWarningsRemoveWarning, .spWarningsRemoveSuspension, .spWarningsRemoveBan').click( function() {
				var mydata = $(this).data();
				$.ajax(mydata.site);
				$('#dialog').dialog('close');
				spj.displayNotification(0, mydata.msg);
			});
		}
	};

	/***********************************************
	load event handlers on forum tools dialog opened
	***********************************************/

	$('#dialog, #spMobilePanel').on('forum_tools_init', function() {
		spwarnings_remove_warnings.init();
	});
}(window.spj = window.spj || {}, jQuery));
