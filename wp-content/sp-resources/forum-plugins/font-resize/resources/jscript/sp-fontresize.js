(function(spj, $, undefined) {
	$.fn.fontResize = function () {
		var fr_element = "div#spMainContainer";
		var fr_resizeSteps = 1.6;
		var fr_cookieTime = 31;
		var startSize = parseFloat($(fr_element+"").css("font-size"));
		var savedSize = spj.cookie('fontSize');
		if(savedSize > 4) {
			$(fr_element).css("font-size", savedSize + "px");
		}

		$('#spFontSize_add').css("cursor","pointer");
		$('#spFontSize_minus').css("cursor","pointer");
		$('#spFontSize_reset').css("cursor","pointer");
		$('#spFontSize_add').click(function() {
			var newSize = parseFloat($(fr_element+"").css("font-size"));
			newSize=newSize+parseFloat(fr_resizeSteps);
			$(fr_element+"").css("font-size",newSize+"px");
			spj.cookie('fontSize', newSize, {expires: parseInt(fr_cookieTime), path: '/'});
		});
		$('#spFontSize_minus').click(function() {
			var newSize = parseFloat($(fr_element+"").css("font-size"));
			newSize=newSize-fr_resizeSteps;
			$(""+fr_element+"").css("font-size",newSize+"px");
			spj.cookie('fontSize', newSize, {expires: parseInt(fr_cookieTime), path: '/'});
		});
		$('#spFontSize_reset').click(function() {
			$(""+fr_element+"").css("font-size",startSize);
			spj.cookie('fontSize', startSize, {expires: parseInt(fr_cookieTime), path: '/'});
		});
	};
}(window.spj = window.spj || {}, jQuery));
