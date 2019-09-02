<?php
/*
Simple:Press
Topic Description plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_topic_description_do_install() {
	$td = SP()->options->get('topic-description');
	if (empty($td)) {
        SP()->DB->execute('ALTER TABLE '.SPTOPICS.' ADD (topic_desc text default NULL)');

        $td['dbversion'] = SPTDDBVERSION;
        SP()->options->update('topic-description', $td);
    }
}

# sp reactivated.
function sp_topic_description_do_sp_activate() {
}
