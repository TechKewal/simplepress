<?php
/*
Simple:Press
Polls plugin ajax routine for creation functions
$LastChangedDate: 2015-12-19 00:59:26 -0800 (Sat, 19 Dec 2015) $
$Rev: 13723 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('polls-create')) die();

sp_load_plugin_styles();

$polls = SP()->options->get('polls');

# setup the form
$out = '
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			$("#spPollForm").ajaxForm(function(responseText, statusText, xhr, $form) {
				if (responseText == -1) {
					alert("'.__('Error Creating Poll.  Please check entries.', 'sp-polls').'");
				} else {
					spj.editorInsertText(\'[sp_show_poll id="\' + responseText + \'"]\');
					$("#dialog").remove();
				}
			});
			$("#sp-poll-date").datepicker({
				beforeShow: function(input, inst) {
					$("#ui-datepicker-div").addClass("sp-polls-dp");
				},
				changeMonth: true,
				changeYear: true,
				dateFormat: "MM dd, yy",
				minDate: 1,
			});
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
';

# output the poll creation form
$out.= '<div id="spMainContainer">';
$out.= '<div id="sp_poll_create">';
$out.= '<div id="sp_poll_body">';
$url = wp_nonce_url(SPAJAXURL.'polls-manage&targetaction=create-poll', 'polls-manage');
$out.= '<form method="post" action="'.$url.'" name="spPollForm" id="spPollForm">';
$out.= '<input type="hidden" value="'.SP()->filters->integer($_GET['fid']).'" name="sp-poll-forum" />';
$out.= '<div class="spEditorTitle">'.__('Poll Question', 'sp-polls').'</div>';
$out.= '<div class="sp_poll_label"><p>'.__('Question', 'sp-polls').'</p></div>';
$out.= '<div class="sp_poll_input"><input id="sp-poll-question" class="spControl" type="text" value="" name="sp-poll-question"></div>';
$out.= '<div class="spEditorTitle">'.__('Poll Answers', 'sp-polls').'</div>';
$out.= '<div id="sp_poll_answers">';
$out.= '<div id="sp_poll_answer-1">';
$out.= '<div class="sp_poll_label"><p>'.__('Answer', 'sp-polls').' #1</p></div>';
$out.= '<div class="sp_poll_input"><input id="sp-poll-answer-1" class="spControl" type="text" value="" name="sp-poll-answer[1]" /><input class="spSubmit spPollsDeleteAnswer" type="button" data-id="1" value="'.esc_attr(__('Remove Answer', 'sp-polls')).'" /></div>';
$out.= '</div>';
$out.= '<div id="sp_poll_answer-2">';
$out.= '<div class="sp_poll_label"><p>'.__('Answer', 'sp-polls').' #2</p></div>';
$out.= '<div class="sp_poll_input"><input id="sp-poll-answer-2" class="spControl" type="text" value="" name="sp-poll-answer[2]" /><input class="spSubmit spPollsDeleteAnswer" type="button" data-id="2" value="'.esc_attr(__('Remove Answer', 'sp-polls')).'" /></div>';
$out.= '</div>';
$out.= '</div>';
$out.= '<div class="sp_poll_submit"><input class="spSubmit spPollsAddAnswer" type="button" value="'.esc_attr(__('Add an Answer', 'sp-polls')).'" /></div>';
$out.= '<div id="sp_poll_date">';
$out.= '<div class="spEditorTitle">'.__('Poll Expiration (Leave Blank for No Expiration)', 'sp-polls').'</div>';
$default = (empty($polls['poll-expire'])) ? '' : date('F d, Y', strtotime("+{$polls['poll-expire']} month"));
$out.= '<div class="sp_poll_input"><input id="sp-poll-date" class="spControl" type="text" value="'.$default.'" name="sp-poll-date"></div>';
$out.= '</div>';
$out.= '<div id="sp_poll_options">';
$out.= '<div class="spEditorTitle">'.__('Maximum Number of Answers Per Vote', 'sp-polls').'</div>';
$out.= '<input type="text" class="spControl" name="sp-poll-maxq" id="sp-poll-maxq" value="1" /><br />';
if ($polls['hide-results']) {
    $out.= '<br /><div class="spEditorTitle">';
    $out.= '<input type="checkbox" class="spControl" name="sp-poll-hide" id="sp-poll-hide" checked="checked" />';
	$out.= '<label for="sp-poll-hide">'.__('Hide Poll Results Until Closed', 'sp-polls').'&nbsp;&nbsp;</label>';
	$out.= '</div>';
}
$out.= '</div>';
$out.= '<div class="sp_poll_submit">';
$out.= '<input id="sfsave" class="spSubmit" type="submit" value="'.esc_attr(__('Create Poll', 'sp-polls')).'" name="createpoll">';
$out.=	'<input type="button" class="spSubmit spCancelScript" name="cancel" value="'.esc_attr(__('Cancel', 'sp-polls')).'" />';
$out.= '</div>';
$out.= '</form>';
$out.= '</div>';
$out.= '</div>';
$out.= '</div>';

echo $out;

die();
