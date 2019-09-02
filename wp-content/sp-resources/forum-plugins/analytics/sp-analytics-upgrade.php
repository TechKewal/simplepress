<?php
/*
Simple:Press
Analytics plugin install/upgrade routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_analytics_do_upgrade_check() {
    if (!SP()->plugin->is_active('analytics/sp-analytics-plugin.php')) return;

    $options = SP()->options->get('analytics');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SP_ANALYTICS_DB_VERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SP_ANALYTICS_DB_VERSION;
    SP()->options->update( 'analytics', $options );
}
