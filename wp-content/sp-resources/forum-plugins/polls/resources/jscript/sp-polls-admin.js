/* ---------------------------------
Simple:Press
Polls Plugin Admin Javascript
------------------------------------ */
(function(spj, $, undefined) {
	function addPollAnswer() {
		answer = parseInt($('#sp-poll-answer-count').val()) + 1;
		next = parseInt($('#sp-poll-answer-next').val()) - 1;
		$('#sp_poll_answers').append('<tr><td class="sflabel" style="width:60%"><span class="sfalignleft">' + sp_polls_admin_vars.answer + ' #' + answer + ':</span></td><td><input type="hidden" value="' + next + '" id="sp-poll-answer-id" name="sp-poll-answer-id" /><input class="sfpostcontrol" type="text" value="" id="sp-poll-answer[' + next +']" name="sp-poll-answer[' + next +']" /></td></tr>');
		$('#sp_poll_answers').append('<tr><td class="sflabel" style="width:60%"><span class="sfalignleft">' + sp_polls_admin_vars.answer + ' #' + answer + ' ' + sp_polls_admin_vars.votes + ':</span></td><td><input class="sfpostcontrol" type="text" value="0" id="sp-poll-answer-votes[' + next +']" name="sp-poll-answer-votes[' + next +']" /></td></tr>');
		$('#sp-poll-answer-count').val(answer);
		$('#sp-poll-answer-next').val(next);
	}

	function logsLimit(url, target, imageFile) {
		var perpage = $('#voters_per_page').val();
		if (imageFile != '') {
			document.getElementById(target).innerHTML = '<br /><br /><img src="' + imageFile + '" /><br />';
		}
		url = url + '&voters_per_page=' + perpage;
		$('#'+target).load(url);
	}

	function logsSearch(url, target, imageFile) {
		var answer = $('#sp-poll-answer').val();
		if (imageFile != '') {
			document.getElementById(target).innerHTML = '<br /><br /><img src="' + imageFile + '" /><br />';
		}
		url = url + '&answer=' + answer;
		$('#'+target).load(url);
	}

	/***********************************************
	event handlers
	***********************************************/

	adminTool = {
		init : function() {
			$('.spPollsAdminTool').off();
			$('.spPollsAdminTool').click( function() {
				var mydata = $(this).data();
				spj.adminTool(mydata.url, mydata.target, mydata.img);
				removeAnswer.init();
				addAnswer.init();
				editCancel.init();
				logSearch.init();
				logLimit.init();
				logPage.init();
				logDelete.init();
				logDeleteAll.init();
			});
		}
	};

	removeAnswer = {
		init : function() {
			$('#sp-poll-ajax').off();
			$('#sp-poll-ajax').on('click', '.spPollsRemoveAnswer', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					spj.delRow(mydata.url, mydata.target);
					$('#' + mydata.target2).css({backgroundColor: '#ffcccc'});
					$('#' + mydata.target2).fadeOut('slow');
				}
			});
		}
	};

	addAnswer = {
		init : function() {
			$('#sp-poll-ajax').off();
			$('#sp-poll-ajax').on('click', '.spPollsAddAnswer', function() {
				addPollAnswer();
			});
		}
	};

	editCancel = {
		init : function() {
			$('#sp-poll-ajax').off();
			$('#sp-poll-ajax').on('click', '.spPollsEditCancel', function() {
				$('#sp-poll-ajax').html('');
			});
		}
	};

	logSearch = {
		init : function() {
			$('#sp-poll-ajax').off('click', '.spPollsLogSearch');
			$('#sp-poll-ajax').on('click', '.spPollsLogSearch', function() {
				var mydata = $(this).data();
				logsSearch(mydata.url, mydata.target, mydata.img);
			});
		}
	};

	logLimit = {
		init : function() {
			$('#sp-poll-ajax').off('click', '.spPollsLogLimit');
			$('#sp-poll-ajax').on('click', '.spPollsLogLimit', function() {
				var mydata = $(this).data();
				logsLimit(mydata.url, mydata.target, mydata.img);
			});
		}
	};

	logPage = {
		init : function() {
			$('#sp-poll-ajax').off('click', '.spPollsLogPage');
			$('#sp-poll-ajax').on('click', '.spPollsLogPage', function() {
				var mydata = $(this).data();
				spj.adminTool(mydata.url, mydata.target, mydata.img);
				removeAnswer.init();
				addAnswer.init();
				editCancel.init();
				logSearch.init();
				logLimit.init();
				logPage.init();
				logDelete.init();
				logDeleteAll.init();
			});
		}
	};

	logDelete = {
		init : function() {
			$('#sp-poll-ajax').off('click', '.spPollsLogDelete');
			$('#sp-poll-ajax').on('click', '.spPollsLogDelete', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					spj.delRow(mydata.url, mydata.target);
				}
			});
		}
	};

	logDeleteAll = {
		init : function() {
			$('#sp-poll-ajax').off('click', '.spPollsLogDeleteAll');
			$('#sp-poll-ajax').on('click', '.spPollsLogDeleteAll', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					spj.delRow(mydata.url, mydata.target);
					$('#sp-poll-ajax').html('');
				}
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		$('#sfmaincontainer').on('adminformloaded', function() {
			adminTool.init();
		});
	});
}(window.spj = window.spj || {}, jQuery));
