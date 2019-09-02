/* ---------------------------------
Simple:Press
Push Notifications Plugin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	function subscribeTopic(url, target, uicon, sicon, ulabel, slabel, utooltip, stooltip) {
		$.ajax({
			type: 'GET',
			url: url,
			cache: false,
			success: function(html) {
				$('#' + target).html(function(i, t) {
					var old = new RegExp(sicon, 'g');
					return t.replace(old, uicon);
				});
				$('#' + target).attr('title', utooltip);
				$('#' + target + ' span').html(function(i, t) {
					var old = new RegExp(slabel, 'g');
					return t.replace(old, ulabel);
				});
				$('.spPushNotificationsSubscribe, .spPushNotificationsUnsubscribe').off();
				$('#' + target).removeClass('spPushNotificationsSubscribe');
				$('#' + target).addClass('spPushNotificationsUnsubscribe');
				$('#' + target).attr('data-url', this.url.replace(/add-sub/, 'del-sub'));
				$('#' + target).data('url', $('#' + target).attr('data-url'));
				sppush_unsubscribe.init();
				spj.displayNotification(0, sp_subs_vars.addsubtopic);
			}
		});
	}

	function unsubscribeTopic(url, target, uicon, sicon, ulabel, slabel, utooltip, stooltip) {
		$.ajax({
			type: 'GET',
			url: url,
			cache: false,
			success: function(html) {
				$('#' + target).html(function(i, t) {
					var old = new RegExp(uicon, 'g');
					return t.replace(old, sicon);
				});
				$('#' + target).attr('title', stooltip);
				$('#' + target + ' span').html(function(i, t) {
					var old = new RegExp(ulabel, 'g');
					return t.replace(old, slabel);
				});
				$('.spPushNotificationsSubscribe, .spPushNotificationsUnsubscribe').off();
				$('#' + target).removeClass('spPushNotificationsUnsubscribe');
				$('#' + target).addClass('spPushNotificationsSubscribe');
				$('#' + target).attr('data-url', this.url.replace(/del-sub/, 'add-sub'));
				$('#' + target).data('url', $('#' + target).attr('data-url'));
				sppush_subscribe.init();
				spj.displayNotification(0, sp_subs_vars.delsubtopic);
			}
		});
	}

	/***********************************************
	event handlers
	***********************************************/

	sppush_show_subs_popup = {
		init : function() {
			$('.spPushNotificationsShowTopicSubs').click( function() {
				var mydata = $(this).data();
				spj.dialogAjax(this, mydata.site, mydata.label, mydata.width, mydata.height, mydata.align);
				$('#dialog, #spMobilePanel').one('opened', function() {
					sppush_end_subscription.init();
				});
			});
		}
	};

	sppush_end_subscription = {
		init : function() {
			$('.spPushNotificationsEndButton').click( function() {
				var mydata = $(this).data();
				$('#' + mydata.target).load(mydata.site);
				$('#' + mydata.target).fadeOut(2000, function() {
					var prev = $('#' + mydata.target).prevAll(":visible:first");
					var next = $('#' + mydata.target).nextAll(":visible:first");
					if (prev.is('a') && !next.is('div')) {
						prev.fadeOut(100);
						var list = $('#spMainContainer > .spListSection > a').nextAll(":visible:first");
						if (list.length == 0) {
							$('#spMainContainer > .spUnsubscribeAll').html('');
							$('#spMainContainer > .spListSection').html('<div class="spMessage"><p>' + sp_subs_vars.nosubs + '</p></div>');
						}
					}
				});
			});
		}
	};

	sppush_subscribe = {
		init : function() {
			$('.spPushNotificationsSubscribe').click( function() {
				var mydata = $(this).data();
				subscribeTopic(mydata.url, mydata.target, mydata.unsubicon, mydata.subicon, mydata.unsublabel, mydata.sublabel, mydata.unsubtip, mydata.subtip);
			});
		}
	};

	sppush_unsubscribe = {
		init : function() {
			$('.spPushNotificationsUnsubscribe').click( function() {
				var mydata = $(this).data();
				unsubscribeTopic(mydata.url, mydata.target, mydata.unsubicon, mydata.subicon, mydata.unsublabel, mydata.sublabel, mydata.unsubtip, mydata.subtip);
			});
		}
	};

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		sppush_show_subs_popup.init();
		sppush_subscribe.init();
		sppush_unsubscribe.init();
	});
}(window.spj = window.spj || {}, jQuery));