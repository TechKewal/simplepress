<?php
/*
Simple:Press
Name plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_answers_topic_do_uninstall() {
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP answered');

    # delete our option
    SP()->options->delete('answers-topic');
}

function sp_answers_topic_do_deactivate() {
}

function sp_answers_topic_do_sp_deactivate() {
}

function sp_answers_topic_do_sp_uninstall() {
}

function sp_answers_topic_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'answers-topic/sp-answers-topic-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-answers-topic')."'>".__('Uninstall', 'sp-answers-topic').'</a>';
    }
	return $actionlink;
}
