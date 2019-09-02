<?php
/*
Simple:Press
Cubepoints plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_cubepoints_do_upgrade_check() {
    if (!SP()->plugin->is_active('cubepoints/sp-cubepoints-plugin.php')) return;

    $data = SP()->options->get('cubepoints');

    $db = $data['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPCUBEDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    if ($db < 1) {
        $data['points_cap'] = 0;
    }

    if ($db < 2) {
        $data['admins'] = 0;
        $data['moderators'] = 0;
    }

    if ($db < 3) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $data['dbversion'] = SPCUBEDBVERSION;
    SP()->options->update('cubepoints', $data);
}
