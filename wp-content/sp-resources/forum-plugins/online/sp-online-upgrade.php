<?php
/*
Simple:Press
Who's Online plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_online_do_upgrade_check() {
    if (!SP()->plugin->is_active('online/sp-online-plugin.php')) return;

    $options = SP()->options->get('online');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPWODBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=1 WHERE auth_name='view_online_activity'");
    }


    # save data
    $options['dbversion'] = SPWODBVERSION;
    SP()->options->update('online', $options);
}
