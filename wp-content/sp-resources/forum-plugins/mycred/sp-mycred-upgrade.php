<?php
/*
Simple:Press
mycred plugin install/upgrade routine
$LastChangedDate: 2014-01-08 20:45:40 +0000 (Wed, 08 Jan 2014) $
$Rev: 10953 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_mycred_do_upgrade_check() {
    if (!SP()->plugin->is_active('mycred/sp-mycred-plugin.php')) return;

    $data = SP()->options->get('mycred');

    $db = $data['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPCREDDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $data['dbversion'] = SPCREDDBVERSION;
    SP()->options->update('mycred', $data);
}
