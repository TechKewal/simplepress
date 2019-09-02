(function(spj, $, undefined) {
	function toggleRegister(cBox) {
		var button = document.getElementById('regbutton');
		if (cBox.checked == true) {
			button.disabled = false;
		} else {
			button.disabled = true;
		}
	}

	/*****************************
	event handlers
	*****************************/

	redirect = {
		init : function() {
			$('.spPolicyRedirect').click( function() {
				var mydata = $(this).data();
				spj.redirect(mydata.url);
			});
		}
	};

	toggle = {
		init : function() {
			$('#sf-accept').click( function() {
				toggleRegister(this);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		redirect.init();
		toggle.init();
	});
}(window.spj = window.spj || {}, jQuery));
