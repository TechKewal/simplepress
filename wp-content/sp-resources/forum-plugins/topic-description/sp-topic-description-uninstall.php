<?php
/*
Simple:Press
Topic Description plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_topic_description_do_uninstall() {
    # delete our option
    SP()->options->delete('topic-description');

	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP topic_desc');
}

function sp_topic_description_do_deactivate() {
}

function sp_topic_description_do_sp_deactivate() {
}

function sp_topic_description_do_sp_uninstall() {
}

function sp_topic_description_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'topic-description/sp-topic-description-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-topic-description')."'>".__('Uninstall', 'sp-topic-description').'</a>';
    }
	return $actionlink;
}
