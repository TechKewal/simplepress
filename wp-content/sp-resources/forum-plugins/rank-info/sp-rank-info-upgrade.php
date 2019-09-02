<?php
/*
Simple:Press
Ranks Info plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rank_info_do_upgrade_check() {
    if (!SP()->plugin->is_active('rank-info/sp-rank-info-plugin.php')) return;

    $options = SP()->options->get('rank-info');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPRANKSDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPRANKSDBVERSION;
    SP()->options->update('rank-info', $options);
}