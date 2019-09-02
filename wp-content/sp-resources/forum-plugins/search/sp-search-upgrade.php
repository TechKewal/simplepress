<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_search_do_upgrade_check() {
    if (!SP()->plugin->is_active('search/sp-search-plugin.php')) return;

    $options = SP()->options->get('search');
    if (empty($options)) return;

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPSEARCHDBVERSION ) return;

    # apply upgrades as needed
    # db version upgrades
    if ($db < 1) {
		$options['searchposttypes']['post'] = true;
		$options['searchposttypes']['page'] = true;
    }

    if ($db < 2) {
        SP()->auths->delete('blogsearch');
    }

    # db version upgrades
    if ($db < 3) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPSEARCHDBVERSION;
    SP()->options->update('search', $options);
}
