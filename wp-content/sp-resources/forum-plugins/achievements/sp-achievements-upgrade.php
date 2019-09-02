<?php
/*
Simple:Press
achievements plugin install/upgrade routine
$LastChangedDate: 2014-01-08 20:45:40 +0000 (Wed, 08 Jan 2014) $
$Rev: 10953 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_achievements_do_upgrade_check() {
    if (!SP()->plugin->is_active('achievements/sp-achievements-plugin.php')) return;

    $data = SP()->options->get('achievements');

    $db = $data['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPACHDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $data['dbversion'] = SPACHDBVERSION;
    SP()->options->update('achievements', $data);
}
