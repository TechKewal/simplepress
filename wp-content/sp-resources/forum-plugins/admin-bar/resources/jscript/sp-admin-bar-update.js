(function(spj, $, undefined) {
	spj.adminBarUpdate = function(url) {
		target = document.getElementById('spAdminQueueCounts');
		if (target != null) {
			/* do the unread counts */
			this_url = url + '&item=unread' + '&rnd=' +  new Date().getTime();
			$('#spUnread').load(this_url);

			/* do the moderation counts */
			this_url = url + '&item=mod' + '&rnd=' +  new Date().getTime();
			$('#spNeedModeration').load(this_url);

			/* do the spam counts */
			this_url = url + '&item=spam' + '&rnd=' +  new Date().getTime();
			$('#spSpam').load(this_url);
		}
	};
}(window.spj = window.spj || {}, jQuery));
