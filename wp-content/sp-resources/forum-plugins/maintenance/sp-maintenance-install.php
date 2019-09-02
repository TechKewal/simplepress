<?php
/*
Simple:Press
Maintenance Mode plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_maintenance_do_install() {
	$options = SP()->options->get('maintenance');
	if (empty($options)) {
		$options['mmenable'] = false;
        $options['mmmessage'] = __('The forum is currently offline in maintenance mode.  The forum will return shortly.  Thanks for your understanding.', 'sp-maintenance');

        $options['dbversion'] = SPMAINTENANCEDBVERSION;
        SP()->options->update('maintenance', $options);
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated.
function sp_maintenance_do_sp_activate() {
}

# permissions reset
function sp_maintenance_do_reset_permissions() {
}
