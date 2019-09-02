/* ---------------------------------
Simple:Press
Watches Plugin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	function addWatchTopic(url, target, sicon, wicon, slabel, wlabel, stooltip, wtooltip) {
		$.ajax({
			type: 'GET',
			url: url,
			cache: false,
			success: function(html) {
				$('#' + target).html(function(i, t) {
					var old = new RegExp(wicon, 'g');
					return t.replace(old, sicon);
				});
				$('#' + target).attr('title', stooltip);
				$('#' + target + ' span').html(function(i, t) {
					var old = new RegExp(wlabel, 'g');
					return t.replace(old, slabel);
				});
				$('.spWatchesStartWatching, .spWatchesStopWatching').off();
				$('#' + target).removeClass('spWatchesStartWatching');
				$('#' + target).addClass('spWatchesStopWatching');
				$('#' + target).attr('data-url', this.url.replace(/watch-add/, 'watch-del'));
				$('#' + target).data('url', $('#' + target).attr('data-url'));
				spwatches_stop_watching.init();
				spj.displayNotification(0, sp_watches_vars.addwatchtopic);
			}
		});
	}

	function removeWatchTopic(url, target, sicon, wicon, slabel, wlabel, stooltip, wtooltip) {
		$.ajax({
			type: 'GET',
			url: url,
			cache: false,
			success: function(html) {
				$('#' + target).html(function(i, t) {
					var old = new RegExp(sicon, 'g');
					return t.replace(old, wicon);
				});
				$('#' + target).attr('title', wtooltip);
				$('#' + target + ' span').html(function(i, t) {
					var old = new RegExp(slabel, 'g');
					return t.replace(old, wlabel);
				});
				$('.spWatchesStartWatching, .spWatchesStopWatching').off();
				$('#' + target).removeClass('spWatchesStopWatching');
				$('#' + target).addClass('spWatchesStartWatching');
				$('#' + target).attr('data-url', this.url.replace(/watch-del/, 'watch-add'));
				$('#' + target).data('url', $('#' + target).attr('data-url'));
				spwatches_start_watching.init();
				spj.displayNotification(0, sp_watches_vars.delwatchtopic);
			}
		});
	}

	/***********************************************
	event handlers
	***********************************************/

	spwatches_show_topics_popup = {
		init : function() {
			$('.spWatchesShowTopics').click( function() {
				var mydata = $(this).data();
				spj.dialogAjax(this, mydata.site, mydata.label, mydata.width, mydata.height, mydata.align);
				$('#dialog, #spMobilePanel').one('opened', function() {
					spwatches_end_watch.init();
				});
			});
		}
	};

	spwatches_end_watch = {
		init : function() {
			$('.spWatchesEndButton').click( function() {
				var mydata = $(this).data();
				$('#' + mydata.target).load(mydata.site);
				$('#' + mydata.target).fadeOut(2000, function() {
					var prev = $('#' + mydata.target).prevAll(":visible:first");
					var next = $('#' + mydata.target).nextAll(":visible:first");
					if (prev.is('a') && !next.is('div')) {
						prev.fadeOut(100);
						var list = $('#spMainContainer > .spListSection > a').nextAll(":visible:first");
						if (list.length == 0) {
							$('#spMainContainer > .spStopWatchingAll').html('');
							$('#spMainContainer > .spListSection').html('<div class="spMessage"><p>' + sp_watches_vars.nowatches + '</p></div>');
						}
					}
				});
			});
		}
	};

	spwatches_stop_watching = {
		init : function() {
			$('.spWatchesStopWatching').on('click', function() {
				var mydata = $(this).data();
				removeWatchTopic(mydata.url, mydata.target, mydata.stopicon, mydata.watchicon, mydata.stoplabel, mydata.watchlabel, mydata.stoptip, mydata.watchtip);
			});
		}
	};

	spwatches_start_watching = {
		init : function() {
			$('.spWatchesStartWatching').on('click', function() {
				var mydata = $(this).data();
				addWatchTopic(mydata.url, mydata.target, mydata.stopicon, mydata.watchicon, mydata.stoplabel, mydata.watchlabel, mydata.stoptip, mydata.watchtip);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		spwatches_show_topics_popup.init();
		spwatches_stop_watching.init();
		spwatches_start_watching.init();
	});
}(window.spj = window.spj || {}, jQuery));
