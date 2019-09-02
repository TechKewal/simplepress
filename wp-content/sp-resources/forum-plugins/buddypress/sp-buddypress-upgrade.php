<?php
/*
Simple:Press
Buddypress plugin install/upgrade routine
$Rev: 15725 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_buddypress_do_upgrade_check() {
    if (!SP()->plugin->is_active('buddypress/sp-buddypress-plugin.php')) return;

    $options = SP()->options->get('buddypress');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPBUDDYPRESSDBVERSION ) return;

    # apply upgrades as needed
    if ($db < 1) {
    }

    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPBUDDYPRESSDBVERSION;
    SP()->options->update('buddypress', $options);
}
