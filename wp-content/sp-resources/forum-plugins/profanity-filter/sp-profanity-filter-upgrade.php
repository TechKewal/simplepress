<?php
/*
Simple:Press
Profanity plugin install/upgrade routine
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profanity_filter_do_upgrade_check() {
    if (!SP()->plugin->is_active('profanity-filter/sp-profanity-filter-plugin.php')) return;

	$filter = SP()->options->get('profanity-filter');

    $db = empty($filter['dbversion']) ? 0 : $filter['dbversion'];

    # quick bail check
    if ($db == SPPFDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
        $filter['replaceall'] = false;
    }

    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $filter['dbversion'] = SPPFDBVERSION;
    SP()->options->update('profanity-filter', $filter);
}
