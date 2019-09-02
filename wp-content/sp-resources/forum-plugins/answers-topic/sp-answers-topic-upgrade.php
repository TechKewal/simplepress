<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_answers_topic_do_upgrade_check() {
    if (!SP()->plugin->is_active('answers-topic/sp-answers-topic-plugin.php')) return;

    $options = SP()->options->get('answers-topic');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPANSWERSDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $options['dbversion'] = SPANSWERSDBVERSION;
    SP()->options->update('answers-topic', $options);
}
