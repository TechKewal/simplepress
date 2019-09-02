<?php
/*
Simple:Press
Topic Status plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_topicstatus_do_uninstall() {
	# Remove all data
	$records = SP()->meta->get('topic-status-set');
	if ($records) {
		foreach ($records as $r) {
			SP()->meta->delete($r['meta_id']);
		}
	}

	SP()->DB->execute('ALTER TABLE '.SPFORUMS.' DROP topic_status_set');
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP topic_status_flag');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-topicstatus');

	# remove the auths
	SP()->auths->delete('change_topic_status');
}

function sp_topicstatus_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-topicstatus');

	# deactivation so make our auths not active
    SP()->auths->deactivate('change_topic_status');
}
