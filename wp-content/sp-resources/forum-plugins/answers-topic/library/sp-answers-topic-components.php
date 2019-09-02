<?php
/*
Simple:Press
Answers Topic Plugin support components
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_answers_topic_do_process_actions() {
    if (isset($_GET['mark-answer']) || isset($_GET['unmark-answer'])) {
        # validation
        $tid = SP()->filters->integer($_GET['topic']);
        $pid = SP()->filters->integer($_GET['post']);
        if (empty($tid) || empty ($pid)) return;
        $thisTopic = SP()->DB->table(SPTOPICS, "topic_id=$tid", 'row');
        if (empty($thisTopic)) return;
    	if (!SP()->user->thisUser->admin && !SP()->user->thisUser->moderator && $thisTopic->user_id != SP()->user->thisUser->ID) return;

        # lets see if we can mark the topic/post
        if (isset($_GET['mark-answer']) && !$thisTopic->answered) {
        	SP()->DB->execute('UPDATE '.SPTOPICS." SET answered=$pid WHERE topic_id=$tid");
			do_action('sph_mark_answer_actions', $thisTopic->user_id, $tid, true);
        }

        if (isset($_GET['unmark-answer']) && $thisTopic->answered == $pid) {
        	SP()->DB->execute('UPDATE '.SPTOPICS." SET answered=0 WHERE topic_id=$tid");
			do_action('sph_unmark_answer_actions', $thisTopic->user_id, $tid, false);
        }
    }
}
