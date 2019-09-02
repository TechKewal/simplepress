/* ---------------------------------
Simple:Press
Subscriptions Plugin Admin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	function showSubsList(id, url, imageFile) {
		var subForm = document.getElementById(id);
		var searchForm = document.getElementById('post-search-input');
		var delim;
		var thisValue;
		var filter = '';
		var groups = '';
		var forums = '';

		if (subForm.sffilterall != null && subForm.sffilterall.checked) filter="&filter=all";
		if (subForm.sffiltergroups != null && subForm.sffiltergroups.checked) {
			filter = '&filter=groups';
			var groupIds = document.getElementById('grouplist');
			if (groupIds.value == '') {
				groups = '&groups=error';
			} else {
				x = 0;
				for (i=0; i<groupIds.length; i++) {
					if (groupIds.options[i].selected) {
						if (x == 0) {
							delim = '';
						} else {
							delim = '-';
						}
						groups+= delim + groupIds.options[i].value;
						x++;
					}
				}
				if (groups != null) {
					groups = '&groups=' + groups;
				} else {
					groups = '&groups=error';
				}
			}
		}

		if (subForm.sffilterforums != null && subForm.sffilterforums.checked) {
			filter = '&filter=forums';
			var forumIds = document.getElementById('forumlist');
			if (forumIds.value == '') {
				forums = '&forums=error';
			} else {
				x = 0;
				for (i=0; i<forumIds.length; i++) {
					if (forumIds.options[i].selected) 				{
						if (x == 0) {
							delim = '';
						} else {
							delim = '-';
						}
						forums+= delim + forumIds.options[i].value;
						x++;
					}
				}
				if (forums != null) {
					forums = '&forums=' + forums;
				} else {
					forums = '&forums=error';
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
			document.getElementById('subsdisplayspot').innerHTML = '<br /><br /><img src="' + imageFile + '" /><br />';
		}
		$('#subsdisplayspot').load(urlGet);
	}

	function showSubsGroupList(url, imageFile) {
		var target = 'selectgroup';
		$('#sub-select-forum').hide();
		$('#sub-select-group').show();
		if (imageFile != '') {
			document.getElementById(target).innerHTML = '<img src="' + imageFile + '" />';
		}

		/* add random num to GET param to ensure its not cached */
		url = url + '&rnd=' +  new Date().getTime();

		$('#'+target).load(url);
	}

	function showSubsForumList(url, imageFile) {
		var target = 'selectforum';
		$('#sub-select-group').hide();
		$('#sub-select-forum').show();
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

	sp_subs_show_subs = {
		init : function() {
			$('#sfmaincontainer').on('click', '.spSubsShowSubs', function(event) {
				var mydata = $(this).data();
				event.preventDefault();
				showSubsList(mydata.target, mydata.site, mydata.img);
			});
		}
	};

	sp_subs_filter_all = {
		init : function() {
			$('#sffilterall').click( function() {
				$('#sub-select-forum').hide();
				$('#sub-select-group').hide();
			});
		}
	};

	sp_subs_filter_groups = {
		init : function() {
			$('#sffiltergroups').click( function() {
				var mydata = $(this).data();
				showSubsGroupList(mydata.site, mydata.img);
			});
		}
	};

	sp_subs_filter_forums = {
		init : function() {
			$('#sffilterforums').click( function() {
				var mydata = $(this).data();
				showSubsForumList(mydata.site, mydata.img);
			});
		}
	};

	sp_subs_search_subs = {
		init : function() {
			$('#sptopicusers, #sptopicsubs, #spforumsubs, #spdigestusers').submit( function(event) {
				var mydata = $(this).data();
				event.preventDefault();
				showSubsList(mydata.target, mydata.site, mydata.img);
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		$('#sfmaincontainer').on('adminformloaded', function() {
			sp_subs_show_subs.init();
			sp_subs_filter_all.init();
			sp_subs_filter_groups.init();
			sp_subs_filter_forums.init();
			sp_subs_search_subs.init();

			$('#sub-select-forum, #sub-select-group').on('click', '.spLayerToggle', function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.target);
			});
		});
	});
}(window.spj = window.spj || {}, jQuery));
