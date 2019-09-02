/* ---------------------------------
Simple:Press
Polls Plugin Javascript
------------------------------------ */

(function(spj, $, undefined) {
	var answerCount = 3;

	function addPollAnswer() {
		$('#sp_poll_answers').append('<div id="sp_poll_answer-' + answerCount + '"><div class="sp_poll_label"><p>' + sp_polls_vars.answer + ' #' + answerCount + '</p></div><div class="sp_poll_input"><input id="sp-poll-answer-' + answerCount + '" class="spControl" type="text" value="" name="sp-poll-answer[' + answerCount + ']" /><input class="spSubmit spPollsDeleteAnswer" type="button" data-id="' + answerCount + '" value="' + sp_polls_vars.remove + '" /></div></div>');
		deleteAnswer.init();
		answerCount++;
	}

	function delPollAnswer(aid) {
		$('#sp_poll_answer-' + aid).remove();
	}

	spj.pollValidate = function(formData, jqForm, options) {
		var form = jqForm[0];

		/* handle jquey form versions where the data elements are not appended to the form */
		var last = formData.length - 1;
		if (formData[last].name != 'maxAnswers') {
			newFormElement = new Object();
			newFormElement.name = 'maxAnswers';
			newFormElement.value = this.data.maxAnswers;
			formData.push(newFormElement);
			last = formData.length - 1;
		}

		var count = 0;
		for (var i = 0; i < formData.length; i++) {
			if (formData[i].name == 'sp-poll-answer[]' || formData[i].name == 'sp-poll-answer') {
				count++;
			}
		}

		if (count > formData[last].value) {
			alert(sp_polls_vars.toomany);
			return false;
		} else if (count < 1) {
			alert(sp_polls_vars.missing);
			return false;
		}
		$('#sp-poll-' + formData[0].value).html('<br /><img src="' + this.data.image + '" /><br />');
		return true;
	};

	/***********************************************
	event handlers
	***********************************************/

	pollsOpenDialog = {
		init : function() {
			$('.spPollsOpenDialog').click( function() {
				var mydata = $(this).data();
				spj.dialogAjax(this, mydata.site, mydata.label, mydata.width, mydata.height, mydata.align);
				$('#dialog, #spMobilePanel').one('opened', function() {
					deleteAnswer.init();
					addAnswer.init();
					cancelScript.init();
				});
			});
		}
	};

	deleteAnswer = {
		init : function() {
			$('.spPollsDeleteAnswer').click( function() {
				var mydata = $(this).data();
				delPollAnswer(mydata.id);
			});
		}
	};

	addAnswer = {
		init : function() {
			$('.spPollsAddAnswer').click( function() {
				addPollAnswer();
			});
		}
	};

	pollsShowTool = {
		init : function() {
			$('.sp-poll').on('click', '.spPollsShowPoll', function() {
				var mydata = $(this).data();
				spj.loadTool(mydata.url, mydata.target, mydata.img);
			});
		}
	};

	cancelScript = {
		init: function() {
			$('.spCancelScript').click(function(event) {
				event.preventDefault();
				spj.cancelScript();
			});
		}
	};

	/***********************************************
	load event handlers on forum tools dialog opened
	***********************************************/

	$(document).ready(function() {
		pollsOpenDialog.init();
		pollsShowTool.init();
	});
}(window.spj = window.spj || {}, jQuery));
