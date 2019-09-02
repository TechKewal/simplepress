<?php
/*
Simple:Press
Print Topic plugin install/upgrade routine
$LastChangedDate: 2014-06-07 22:32:19 +0100 (Sat, 07 Jun 2014) $
$Rev: 11528 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_print_topic_do_upgrade_check() {
    if (!SP()->plugin->is_active('print-topic/sp-print-plugin.php')) return;

    $options = SP()->options->get('print_topic');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPPTDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $options['dbversion'] = SPPTDBVERSION;
    SP()->options->update('print_topic', $options);
}
