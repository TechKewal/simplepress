/* ---------------------------------
Simple:Press
Private Messaging Plugin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	function pmRemoveUser(ruid) {
		/* remove the desired recipient from the list */
		$(ruid).remove();

		/* decrement the recipient count by one since we removed a user */
		count = $('#pmcount').val() - 1;
		$('#pmcount').val(count);

		/* cant be pm all any more so clear */
		$('#pmall').val(0);

		/* bring back add all buddies option? */
		$('#addbuddies').fadeIn(1000);
	}

	function pmAddRecipient(rid, ruid) {
		/* get some hidden input elements from the form as data to this js routine */
		var uid = $('#uid').val();
		var img1 = sp_pm_vars.removeicon;
		var img2 = sp_pm_vars.addicon;
		var img1msg = sp_pm_vars.removerecipient;
		var img2msg = sp_pm_vars.addbuddy;
		var cc = $('#pmcc').val();
		var bcc = $('#pmbcc').val();
		var max = $('#pmmax').val();
		var count = $('#pmcount').val();
		var limited = $('#pmlimited').val();
		var pmall = $('#pmall').val();

		/* if max recipient count is not reached, add the user */
		if ((max == 0 || count < max) && pmall == 0) {
			images = "<a class='spPmComposeRemoveUser' data-user='#row" + uid + "'> <img src='" + img1 + "' title='" + img1msg + "' /></a>";
			if (ruid != -1 && ruid != -2) {
				images = images + "<a class='spPmComposeAddBuddy' data-user='" + ruid + "' data-name='" + rid + "'>&nbsp;&nbsp;<img src='" + img2 + "' title='" + img2msg + "' /></a>";
			}
			if (limited) {
				images = '';
			}
			cctext = bcctext = '';
			if (cc == 1) cctext = "<option value='2'>Cc:</option>";
			if (bcc == 1) bcctext = "<option value='3'>Bcc:</option>";
			$("#spPmComposeNameList").append("<div id='row" + uid + "' style='text-align:center'><select class='spSelect' size='1' name='type[]' id='type" + uid + "'><option value='1' default='default'>To:</option>" + cctext + bcctext + "</select> <input class='spControl' type='text' size='30' name='user[]' id='user" + uid + "' value='" + rid +"' readonly='readonly' /><input type='hidden' name='userid[]' id='userid" + uid + "' value='" + ruid +"' />" + images + "</div>");
			uid = (uid - 1) + 2;
			document.getElementById('uid').value = uid;
			document.getElementById('pmcount').value = (parseInt(count) + 1);
		} else {
			if (pmall == 0) {
				/* display error message if max recipients already reached */
				spj.displayNotification(0, sp_pm_vars.toomany);
			}
		}
		$('#addbuddies').show();
	}

	spj.pmSendPmTo = function(e, recipient, threadid, name) {
		/* stop propagating other click handlers - keeps current message open */
		if (e != '') {
			e.stopPropagation();
			e.cancelBubble = true;	/* IE */
		}

		/* init some key fields */
		$('#spPmComposeNameList').html('');
		$('#pmcount').val('0');

		if (recipient) {
			/* split names and ids */
			var namelist = name.toString().split(',');
			var recipientlist = recipient.toString().split(',');

			/* add each recipient to the recipients */
		   for (x=recipientlist.length-1; x>=0; x--) {
			  pmAddRecipient(namelist[x], recipientlist[x]);
		   }
		}

		/* if provided, save thread id */
		$('#threadid').val(threadid);

		/* if provided, set the title, reply flag and slug */
		if ($('#spPmThreadTitle' + threadid).length > 0) {
			$('#pmtitle').val($('#spPmThreadTitle' + threadid).text());

			/* disable the title input so it doesnt change if its a reply or forward */
			$('#pmtitle').attr('readonly', 'readonly');
		}

		/* display the compose form */
		if ($('#spPostForm').css('display') != 'block') {
			spj.openEditor('spPostForm', 'pm');
		}

		return false;
	};

	function pmLoadThread(url, tid) {
		$.ajax(url).done(function() {
			window.location = sp_pm_vars.thread + tid;
		});
	}

	function pmMarkUnRead(e, url, id) {
		/* stop propagating other click handlers - keeps current message open */
		e.stopPropagation();
		e.cancelBubble = true;	/* IE */

		/* change msg background to unread */
		var newclass = 'spPmMessageSection spTopicPostSection spPmUnread';
		if ($('#spPmMessageSection' + id).hasClass('spOdd')) {
			newclass = newclass + ' spOdd';
		} else {
			newclass = newclass + ' spEven';
		}
		$('#spPmMessageSection' + id).attr('class', newclass);

		/* increase inbox counter */
		var pcount = parseInt($('.spPmCountUnread').html());
		pcount++;
		$('.spPmCountUnread').html(pcount);

		$('#spPmMessageIndexMarkUnread' + id).fadeOut(2000);

		$.ajax(url).done(function() {
			spj.displayNotification(0, sp_pm_vars.unread);
		});
	}

	function pmEmptyInbox(url) {
		$('#spPmBody > div').fadeOut(2000);

		var pcount = '<span class="spPmCountRead">0</span>';
		$('#spPmCount').html(pcount);

		$.ajax(url).done(function() {
			$('#spPmBody').html('<div class="spMessage">' + sp_pm_vars.nopms + '</div>');

			$('#spThreadCount').text('0');
			$('#spMessageCount').text('0');

			spj.displayNotification(0, sp_pm_vars.empty);
		});
	}

	function pmMarkInbox(url) {
		$('.spPmThreadSection').removeClass('spPmUnread').addClass('spPmRead');
		$.ajax(url).done(function() {
			spj.displayNotification(0, sp_pm_vars.markall);
		});
	}

	function pmQuotePm(e, recipient, threadid, msgid, intro, name) {
		/* stop propagating other click handlers - keeps current message open */
		e.stopPropagation();
		e.cancelBubble = true;	/* IE */

		/* address the pm */
		spj.pmSendPmTo(e, recipient, threadid, name);

		/* quote the pm but make sure editor open */
		var postcontent = $('#spPmMessageIndexContent' + msgid).html();
		setTimeout(function() {
			spj.editorInsertContent(intro, postcontent);
		}, 1000);
	}


	function pmDeleteMessage(url, mid, tid) {
		/* remove message */
		$('#spPmMessageSection' + mid).fadeOut(2000);

		/* delete message */
		$.ajax(url).done(function() {
			$('#spMessageCount').text(parseInt($('#spMessageCount').text()) - 1);
			$('#spThreadMessageCount' + tid).text(parseInt($('#spThreadMessageCount' + tid).text()) - 1);

			spj.displayNotification(0, sp_pm_vars.mdelete);

			if (parseInt($('#spThreadMessageCount' + tid).text()) == 0) {
				window.location = sp_pm_vars.inbox;
			}
		});
	}

	function pmDeleteThread(url, id, view) {
		var count = $('#spThreadMessageCount' + id).html();

		$('#spPmThreadSection' + id).fadeOut('slow', function() {
			$('#spPmThreadSection' + id).load(url, function() {
				$('#spThreadCount').text(parseInt($('#spThreadCount').text()) - 1);
				$('#spMessageCount').text(parseInt($('#spMessageCount').text()) - count);

				spj.displayNotification(0, sp_pm_vars.tdelete);

				if (view == 'thread') {
					window.location = sp_pm_vars.inbox;
				} else if (parseInt($('#spThreadCount').text()) == 0) {
					$('#spPmBody').html('<div class="spMessage">' + sp_pm_vars.nopms + '</div>');
				}
			});
		});
	}

	spj.pmValidateForm = function(theForm) {
		var reason = '';
		var uid = $('#uid').val();
		var ug = $('#sp_pm_usergroup_select').val();
		if (uid == 1 && (ug == null || ug == -1)) {
			reason += '<strong>' + ' - ' + sp_pm_vars.norecipients + '</strong><br />';
		} else if (ug == null || ug == -1) {
			var found = false;
			for (i=uid; i>0; i--) {
				var user = document.getElementById('userid' + i);
				if (user != null) {
					found = true;
					break;
				}
			}
			if (!found) reason += '<strong>' + ' - ' + sp_pm_vars.norecipients + '</strong><br />';
		}
		reason += spjValidateThis(theForm.pmtitle, " - " + sp_pm_vars.notitle);
		reason += spj.editorValidateContent(theForm.postitem, " - " + sp_pm_vars.nomessage);

		if (reason != '') {
			var msg = '<p><br />' + sp_pm_vars.incomplete + ':<br /><br />' + reason + '<br /></p>';
			spj.dialogHtml(theForm, msg, '', 300, 0, 'center');

			return false;
		}

		var saveBtn = document.getElementById('spPmSave');
		saveBtn.value = sp_pm_vars.saving;

		var text = sp_pm_vars.saving + ' - ' + sp_pm_vars.wait;
		spj.displayNotification(2, text);

		return true;
	};

	function pmAllUsers() {
		var uid = $('#uid').val();

		/* remove any current recipients */
		var rList = '';
		for (i=uid-1; i>0; i--) {
			var user = document.getElementById('userid' + i);
			if (user != null) {
				pmRemoveUser(user);
			}
		}

		/* init some key fields */
		$('#spPmComposeNameList').html('');
		$('#pmcount').val(1);

		/* add bogus label for all users */
		pmAddRecipient('All Users', -1);
		$('#pmall').val(1);

		/* remove add buddy button */
		$('#addbuddies').fadeOut(1000);

		return false;
	}

	function pmUsergroup() {
		/* add bogus label for all users */
		var ug = $('#sp_pm_usergroup_select').val();
		if (ug != -1) {
			var name = $('#sp_pm_usergroup_select option:selected').text();
			pmAddRecipient(name, -2);
		}
		$('#sp_pm_usergroup_select').val(-1);
		return false;
	}

	spj.pmAddUser = function(selUser) {
		var uid = $('#uid').val();

		/* check is user to add is already in the recipient list */
		var found = false;
		for (i=uid-1; i>0; i--) {
			var user = document.getElementById('userid' + i);
			if (user != null && user.value == selUser.value) {
				found = true;
				break;
			}
		}

		/* if buddy isnt already in the list, add buddy to the recipient list */
		if (!found) {
			pmAddRecipient(selUser.value, selUser.id);
		}

		/* clear the input box where names are typed to reset for next users */
		var tolist = document.getElementById('pmusers');
		tolist.value = '';
		selUser.id = null;
		selUser.value = '';

		return false;
	};

	function pmAddAllBuddies() {
		var uid = $('#uid').val();
		var url = $('#pmsite').val();

		/* create a '-' seperated list of recipients */
		var rList = '';
		for (i=uid-1; i>0; i--) {
			var user = document.getElementById('userid' + i);
			if (user != null) {
				if (rList == '') {
					rList = user.value;
				} else {
					rList = rList + '-' + user.value;
				}

				/* lets add to the buddy list */
				var username = document.getElementById('user' + i);
				var option = $('<option></option>').attr('value', user.value).text(username.value);
				$('#pmbudlist').append(option);
			}
		}

		/* make sure there are really folks to add */
		if ((rList == null) || (rList == '0')) {
			return;
		}

		/* call the ajax routine to add the new buddies */
		url+= '&addbuddies=' + rList;

		$.ajax(url);

		spj.displayNotification(0, sp_pm_vars.newbuddies);
	}

	function pmNewBuddy(uid) {
		var url = $('#pmsite').val();
		var rList = $('#userid' + uid).val();

		url += '&addbuddies=' + rList;
		$.ajax(url);

		spj.displayNotification(0, sp_pm_vars.newbuddy);
	}

	function pmAddBuddy(source) {
		/* grab the buddy information */
		source = document.getElementById(source);
		var uid = $('#uid').val();
		if (source.value == -1) {
			return false;
		}

		/* make sure the buddy isnt already in the recipient list */
		var found = false;
		for (i=uid-1; i>0; i--) {
			var user = document.getElementById('userid' + i);
			if (user != null && user.value == source.value) {
				found = true;
				break;
			}
		}

		/* if buddy isnt already in the list, add buddy to the recipient list */
		if (!found) {
			thisOption = new Option(source.options[source.selectedIndex].text, source.value, true, true);
			pmAddRecipient(thisOption.text, source.value);
		}

		$("#"+source.id).val(-1);

		return false;
	}

	function pmExpandAll(img) {
		$('.spPmMessageContent').css('display', 'block');
	}

	function pmCollapseAll(img) {
		$('.spPmMessageContent').css('display', 'none');
	}

	/***********************************************
	event handlers
	***********************************************/

	sp_pm_profile_delete_item = {
		init : function() {
			$('.spProfileManageBuddies, .spProfileManageAdversaries').on('click', '.spPMRemoveUser', function() {
				var mydata = $(this).data();
				$(mydata.target).fadeOut(2000, function() {
					$(mydata.target).load(mydata.url);
				});
			});
		}
	};

	sp_pm_toggle_view = {
		init : function() {
			$('#spPmContainer').on('click', '.spPMToggleView', function() {
				var mydata = $(this).data();
				spj.toggleLayer(mydata.target);
			});
		}
	};

	sp_pm_all_users = {
		init : function() {
			$('#spPmContainer').on('click', '.spPMAllUsers', function() {
				pmAllUsers();
			});
		}
	};

	sp_pm_add_all_to_buddies = {
		init : function() {
			$('#spPmContainer').on('click', '.spPMAddAllToBuddies', function() {
				  pmAddAllBuddies();
			});
		}
	};

	sp_pm_delete_thread = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmThreadDelete', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					pmDeleteThread(mydata.url, mydata.id, mydata.box);
				}
			});
		}
	};

	sp_pm_empty_inbox = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmEmptyInbox', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					pmEmptyInbox(mydata.url);
				}
			});
		}
	};

	sp_pm_mark_inbox_read = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmMarkInboxRead', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					pmMarkInbox(mydata.url);
				}
			});
		}
	};

	sp_pm_compose = {
		init : function() {
			$('#spPmContainer').on('click', '.spPMComposePm', function() {
				var mydata = $(this).data();
				spj.openEditor(mydata.form, mydata.type);
			});
		}
	};

	sp_pm_expand_messages = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmExpandAll', function() {
				pmExpandAll();
			});
		}
	};

	sp_pm_collapse_messages = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmCollapseAll', function() {
				pmCollapseAll();
			});
		}
	};

	sp_pm_delete_message = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmMessageDelete', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					pmDeleteMessage(mydata.url, mydata.msgid, mydata.threadid);
				}
			});
		}
	};

	sp_pm_quote_pm = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmQuotePm', function(event) {
				var mydata = $(this).data();
				pmQuotePm(event, mydata.ids, mydata.threadid, mydata.msgid, mydata.intro, mydata.names);
			});
		}
	};

	sp_pm_reply = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmReplyTo', function(event) {
				var mydata = $(this).data();
				spj.pmSendPmTo(event, mydata.ids, mydata.threadid, mydata.names);
			});
		}
	};

	sp_pm_mark_unread = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmMarkMessageUnread', function(event) {
				var mydata = $(this).data();
				pmMarkUnRead(event, mydata.url, mydata.msgid);
			});
		}
	};

	sp_pm_remove_user = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmComposeRemoveUser', function() {
				var mydata = $(this).data();
				pmRemoveUser(mydata.user);
			});
		}
	};

	sp_pm_add_buddy = {
		init : function() {
			$('#spPmContainer').on('click', '.spPmComposeAddBuddy', function() {
				var mydata = $(this).data();
				pmNewBuddy(mydata.user);

				/* lets add to the buddy list */
				var option = $('<option></option>').attr('value', mydata.user).text(mydata.name);
				$('#pmbudlist').append(option);
			});
		}
	};

	sp_pm_quick_links = {
		init : function() {
			$('#spPmQuickLinksThreadsSelect').change( function() {
				spj.changeUrl(this);
			});
		}
	};

	sp_pm_send_to_buddy = {
		init : function() {
			$('#spPmContainer').on('change', '.spPMComposeSendToBuddy', function() {
				var mydata = $(this).data();
				pmAddBuddy(mydata.target);
			});
		}
	};

	sp_pm_send_to_usergroup = {
		init : function() {
			$('#spPmContainer').on('change', '#sp_pm_usergroup_select', function() {
				pmUsergroup();
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		sp_pm_toggle_view.init();
		sp_pm_all_users.init();
		sp_pm_add_all_to_buddies.init();
		sp_pm_delete_thread.init();
		sp_pm_empty_inbox.init();
		sp_pm_mark_inbox_read.init();
		sp_pm_compose.init();
		sp_pm_expand_messages.init();
		sp_pm_collapse_messages.init();
		sp_pm_delete_message.init();
		sp_pm_quote_pm.init();
		sp_pm_reply.init();
		sp_pm_mark_unread.init();
		sp_pm_remove_user.init();
		sp_pm_add_buddy.init();
		sp_pm_quick_links.init();
		sp_pm_send_to_buddy.init();
		sp_pm_send_to_usergroup.init();

		$('#spProfileContent').on('profilecontentloaded', function() {
			sp_pm_profile_delete_item.init();
		});
	});
}(window.spj = window.spj || {}, jQuery));
