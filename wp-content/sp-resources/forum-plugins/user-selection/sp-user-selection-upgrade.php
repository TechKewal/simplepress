<?php
/*
Simple:Press
User Selection plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_user_selection_do_upgrade_check() {
    if (!SP()->plugin->is_active('user-selection/sp-user-selection-plugin.php')) return;

    $data = SP()->options->get('user-selection');

    $db = $data['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPUSELDBVERSION ) return;

    # apply upgrades as needed
    if ($db < 1) {
        $data['usedefault'] = true;
    }

    # db version upgrades
    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $data['dbversion'] = SPUSELDBVERSION;
    SP()->options->update('user-selection', $data);
}
