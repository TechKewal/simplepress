<?php
/*
Simple:Press
Topic Description plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_topic_description_do_upgrade_check() {
    if (!SP()->plugin->is_active('topic-description/sp-topic-description-plugin.php')) return;

    $td = SP()->options->get('topic-description');

    $db = $td['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPTDDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $td['dbversion'] = SPTDDBVERSION;
    SP()->options->update('topic-description', $td);
}
