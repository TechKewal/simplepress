/* ---------------------------------
Simple:Press
Watches Plugin Admin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	function showWatchesList(id, url, imageFile) {
		var subForm = document.getElementById(id);
		var searchForm = document.getElementById('post-search-input');
		var delim;
		var thisValue;
		var filter="";
		var groups="";
		var forums="";

		if (subForm.sffilterall != null && subForm.sffilterall.checked) filter="&filter=all";
		if (subForm.sffiltergroups != null && subForm.sffiltergroups.checked) {
			filter="&filter=groups";
			var groupIds = document.getElementById('grouplist');
			if (groupIds.value == '') {
				groups = "&groups=error";
			} else {
				x = 0;
				for (i=0;i<groupIds.length;i++) {
					if (groupIds.options[i].selected) {
						if (x == 0) {
							delim = '';
						} else {
							delim = '-';
						}
						groups += delim + groupIds.options[i].value;
						x++;
					}
				}
				if (groups != null) {
					groups = "&groups=" + groups;
				} else {
					groups = "&groups=error";
				}
			}
		}

		if (subForm.sffilterforums != null && subForm.sffilterforums.checked) {
			filter="&filter=forums";
			var forumIds = document.getElementById('forumlist');
			if (forumIds.value == '') {
				forums = "&forums=error";
			} else {
				x = 0;
				for (i=0;i<forumIds.length;i++) {
					if (forumIds.options[i].selected) {
						if (x == 0) {
							delim = '';
						} else {
							delim = '-';
						}
						forums += delim + forumIds.options[i].value;
						x++;
					}
				}
				if (forums != null) {
					forums = "&forums=" + forums;
				} else {
					forums = "&forums=error";
				}
			}
		}

		var sText = '';
		if (searchForm) {
			sText = '&swsearch='+searchForm.form.elements['swsearch'].value;
		}

		urlGet = url + filter + groups + forums + encodeURI(sText);

		/* add random num to GET param to ensure its not cached */
		urlGet = urlGet + '&rnd=' +  new Date().getTime();

		if (imageFile != '') {
			document.getElementById('watchesdisplayspot').innerHTML = '<br /><br /><img src="' + imageFile + '" /><br />';
		}
		$('#watchesdisplayspot').load(urlGet);
	}

	function showWatchesGroupList(url, imageFile) {
		var target = 'selectgroup';
		$('#select-forum').hide();
		$('#select-group').show();
		if (imageFile != '') {
			document.getElementById(target).innerHTML = '<img src="' + imageFile + '" />';
		}

		/* add random num to GET param to ensure its not cached */
		url = url + '&rnd=' +  new Date().getTime();

		$('#'+target).load(url);
	}

	function showWatchesForumList(url, imageFile) {
		var target = 'selectforum';
		$('#select-group').hide();
		$('#select-forum').show();
		if (imageFile != '') {
			document.getElementById(target).innerHTML = '<img src="' + imageFile + '" />';
		}

		/* add random num to GET param to ensure its not cached */
		url = url + '&rnd=' +  new Date().getTime();

		$('#'+target).load(url);
	}

	/***********************************************
	event handlers
	***********************************************/

	sp_watches_show_watches = {
		init : function() {
			$('#sptopicwatches, #watchesdisplayspot').on('click', '.spWatchesShowWatches', function() {
				var mydata = $(this).data();
				showWatchesList(mydata.target, mydata.site, mydata.img);
			});
		}
	};

	sp_watches_filter_all = {
		init : function() {
			$('#sffilterall').click( function() {
				$('#select-forum').hide();
				$('#select-group').hide();
			});
		}
	};

	sp_watches_filter_groups = {
		init : function() {
			$('#sffiltergroups').click( function() {
				var mydata = $(this).data();
				showWatchesGroupList(mydata.site, mydata.img);
			});
		}
	};

	sp_watches_filter_forums = {
		init : function() {
			$('#sffilterforums').click( function() {
				var mydata = $(this).data();
				showWatchesForumList(mydata.site, mydata.img);
			});
		}
	};

	sp_watches_search_watches = {
		init : function() {
			$('#sptopicusers, #sptopicwatches').submit( function(event) {
				var mydata = $(this).data();
				event.preventDefault();
				showWatchesList(mydata.target, mydata.site, mydata.img);
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		$('#sfmaincontainer').on('adminformloaded', function() {
			sp_watches_show_watches.init();
			sp_watches_filter_all.init();
			sp_watches_filter_groups.init();
			sp_watches_filter_forums.init();
			sp_watches_search_watches.init();

			$('#select-forum, #select-group').on('click', '.spLayerToggle', function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.target);
			});
		});
	});
}(window.spj = window.spj || {}, jQuery));
