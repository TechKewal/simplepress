/* ---------------------------------
Simple:Press
Admin Bar Plugin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	/* ----------------------------------
	Load up admins new post list
	-------------------------------------*/
	function getNewPostList(url, url2) {
		/* update the counts */
		spj.adminBarUpdate(url2);

		var targetdiv = 'spAdminQueueList';
		var dropdown = document.getElementById(targetdiv);

		if (dropdown.style.display != 'block') {
			$('#spBarSpinner').show();
		}

		if (dropdown.style.display == 'block') {
			$('#'+targetdiv).hide();
				$('#spAdminQueueList').trigger('closed');
		} else {
			url = url + '&rnd=' +  new Date().getTime();
			$('#'+targetdiv).load(url, function () {
				height = window.outerHeight-80;
				$('#'+targetdiv).css('max-height', height);
				$('#'+targetdiv).show('normal');
				dropdown.style.display = 'block';
				$('#spBarSpinner').hide();
				$('#spAdminQueueList').trigger('opened');
			});
		}
	}

	/* ----------------------------------
	Post moderation and unread
	-------------------------------------*/
	function moderatePost(posturl, url, canRemove, postid, forumid, topicid, poststatus, action, refreshUrl) {
		var thistopic = 'spAdminQueueThisTopic' + topicid;
		var topicrow = 'spAdminQueueTopic' + topicid;
		var modpostrowid = 'spAdminQueuePost' + topicid;
		var topics = 'tcount' + forumid;
		var posts = 'pcount' + topicid;
		var postsmod = 'pcountmod' + topicid;
		var postsord = 'pcountord' + topicid;
		var forumrow = 'spAdminQueueForum' + forumid;
		var thispost = 'spAdminQueueThisPost' + postid;
		var thispostcon = 'spAdminQueueThisPostButtons' + postid;
		var topicCount = document.getElementById(topics);

		var postcount = document.getElementById(posts);
		var postcountMod = document.getElementById(postsmod);
		var postcountOrd = document.getElementById(postsord);

		/* reduce topic count by one unless deleting a post with more than one in topic */
		if ((action != 2) || (action == 2 && postcount.value == 1)) {
			topicCount.value--;
		}

		/* if deleting a post where there is nore than one post in topic then just remove post rows. */
		var target1 = '';
		var target2 = '';
		if (action == 2 && postcount.value != 1) {
			target1 = document.getElementById(thispost);
			target2 = document.getElementById(thispostcon);
		} else {
			target1 = document.getElementById(modpostrowid);
			target2 = document.getElementById(topicrow);
		}
		var targetf = document.getElementById(forumrow);

		$(target1).fadeOut('fast');
		$(target2).fadeOut('fast');
		if (topicCount.value == 0) {
			$(targetf).fadeOut('fast');
		}

		/* Call the moderation/approval/delete code */
		url = url + '&rnd=' +  new Date().getTime();
		$.ajax({
			url: url,
			type: "GET",
			success: function(text, status) {
				/* set up count vars */
				var removeMod = new Number(0);
				var removeOrd = new Number(0);

				/* if it's delete change the action for ease */
				if (action == 2) {
					postcount.value--;
					if (poststatus != 0) {
						action = 0;
						postcountMod.value--;
						removeMod = 1;
					} else {
						action = 1;
						postcountOrd.value--;
						removeOrd = 1;
					}
				} else {
					removeMod = postcountMod.value;
					removeOrd = postcountOrd.value;
				}

				if (canRemove) {
					var mastercount = '';
					if (action == 1 || removeOrd != 0) {
						mastercount = parseInt($('#spUnread > span').html());
						if (isNaN(mastercount)) {
							mastercount = 0;
						} else {
							mastercount = (mastercount-removeOrd);
						}
						$('#spUnread > span').html(mastercount);
					}

					if (action == 0 || action == 9 || removeMod != 0) {
						if (poststatus == 1) {
							/* needs moderation */
							mastercount = $('#spNeedModeration > span').html();
							if (isNaN(mastercount)) {
								mastercount = 0;
							} else {
								mastercount = (mastercount-removeMod);
							}
							$('#spNeedModeration > span').html(mastercount);
						} else {
							/* so it is spam */
							mastercount = parseInt($('#spSpam > span').html());
							if (isNaN(mastercount)) {
								mastercount = 0;
							} else {
								mastercount = (mastercount-removeMod);
							}
							$('#spSpam > span').html(mastercount);
						}
					}
				}

				/*  have we finished them all? */
				if (parseInt($('#spUnread > span').html()) == 0 && parseInt($('#spNeedModeration > span').html()) == 0 && parseInt($('#spSpam > span').html()) == 0) {
					var mainDiv = '#spAdminQueueList';
					$(mainDiv).fadeOut(500);
				}

				if (typeof refreshUrl === 'undefined') {
					spj.displayNotification(0, text);
				}

				if (posturl != '') {
					var l = window.location.href;
					var c = l.split('#');
					var n = posturl.split('#');
					if (c[0] == n[0]) {
						window.location.reload();
					} else {
						window.location = posturl;
					}
				}
			}
		});
	}

	function saveQuickReply(theForm, saveurl, modurl, postid, forumid, topicid, poststatus, action, refreshUrl) {
		var saveBtn = document.getElementById('sfsave'+topicid);
		saveBtn.value = sp_adminbar_vars.saving;

		/* Prepare post content for query var */
		var mText = theForm.elements['postitem'+topicid].value;
		var cText = mText.replace(/\n/g, "<br />");
		cText = encodeURIComponent(cText);

		var url = saveurl;
		url+="&postitem=" + cText;
		var item;

		/* now loop through form elements to add anything else that might be added by a plugin */
		for (x=theForm.elements.length-1;x>=0;x--) {
			if (theForm.elements[x].name != 'postitem'+topicid && theForm.elements[x].name != 'newpost'+topicid) {
				if (theForm.elements[x].type == 'checkbox') {
					if (theForm.elements[x].checked==true) {
						item = '1';
					} else {
						item = '0';
					}
				} else {
					item = encodeURIComponent(theForm.elements[x].value);
				}
				url += "&"+theForm.elements[x].name+"="+item;
			}
		}
		$.ajax({
			url: url,
			type: "GET",
			success: function(text, status) {
				moderatePost('', modurl, '1', postid, forumid, topicid, poststatus, action, refreshUrl);
				spj.displayNotification(0, text);
			}
		});

		return false;
	}

	function removeSpam(actionURL, updateURL) {
		actionURL = actionURL + '&rnd=' +  new Date().getTime();
		$('#spAdminQueueMsg').load(actionURL, function() {
			$('#spAdminQueueMsg').show('fast', function() {
				$('#spAdminQueueList').hide('slow');
				spj.adminBarUpdate(updateURL);
			});
		});
	}

	/*****************************
	event handlers
	*****************************/

	new_post_list = {
		init : function() {
			$('#spShowNewPostList').click( function() {
				var mydata = $(this).data();
				getNewPostList(mydata.url, mydata.update);
				$('#spAdminQueueList').one('opened', function() {
	                $('.spModButton, .spUnreadButton, .spReplyPost').off();
					admin_bar_init();
				});
			});
		}
	};

	view_button = {
		init : function() {
			$('.spModButton, .spUnreadButton').click( function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.topicid);
			});
		}
	};

	moderate_button = {
		init : function() {
			$('.spModeratePost').click( function() {
				var mydata = $(this).data();
				moderatePost(mydata.url, mydata.site, mydata.removal, mydata.postid, mydata.forumid, mydata.topicid, mydata.status, mydata.update);
			});
		}
	};

	reply_button = {
		init : function() {
			$('.spReplyPost').click( function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.topicid);
			});
		}
	};

	delete_button = {
		init : function() {
			$('.spDeletePost').click( function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) moderatePost(mydata.url, mydata.site, mydata.removal, mydata.postid, mydata.forumid, mydata.topicid, mydata.status, mydata.update);
			});
		}
	};

	remove_button = {
		init : function() {
			$('.spRemoveSpam').click( function() {
				var mydata = $(this).data();
				removeSpam(mydata.url, mydata.update);
			});
		}
	};

	quick_reply_submit = {
		init : function() {
			$('.quickReplySubmit').submit(function(event) {
				var mydata = $(this).data();
				saveQuickReply(this, mydata.saveurl, mydata.modurl, mydata.postid, mydata.forumid, mydata.topicid, mydata.poststatus, mydata.action, mydata.refreshurl);
				event.preventDefault();
			});
		}
	};

	/***********************************************
	load event handlers on admin bar loaded
	***********************************************/

	function admin_bar_init() {
		view_button.init();
		moderate_button.init();
		reply_button.init();
		delete_button.init();
		remove_button.init();
		quick_reply_submit.init();
	}

	/***********************************************
	load event handlers on document ready
	***********************************************/

	$(document).ready(function() {
		new_post_list.init();
	});
}(window.spj = window.spj || {}, jQuery));
