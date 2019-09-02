<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_answers_topic_do_install() {
	$options = SP()->options->get('answers-topic');
	if (empty($options)) {
    	$options = array();

        SP()->DB->execute('ALTER TABLE '.SPTOPICS.' ADD (answered bigint(20) NOT NULL default "0")');

        $options['dbversion'] = SPANSWERSDBVERSION;
        SP()->options->update('answers-topic', $options);
    }
}

# sp reactivated.
function sp_answers_topic_do_sp_activate() {
}

# permissions reset
function sp_answers_topic_do_reset_permissions() {
}
