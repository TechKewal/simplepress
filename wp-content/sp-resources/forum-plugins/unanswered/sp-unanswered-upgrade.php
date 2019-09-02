<?php
/*
Simple:Press
Unanswered plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_unanswered_do_upgrade_check() {
    if (!SP()->plugin->is_active('unanswered/sp-unanswered-plugin.php')) return;

    $options = SP()->options->get('unanswered');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPUADBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $options['dbversion'] = SPUADBVERSION;
    SP()->options->update('unanswered', $options);
}
