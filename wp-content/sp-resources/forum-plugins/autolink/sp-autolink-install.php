<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_autolink_do_install() {
	$options = SP()->options->get('autolink');
	if (empty($options)) {
        $options = array();
        $options['noboundary'] = false;
        $options['dbversion'] = SPAUTODBVERSION;
        SP()->options->update('autolink', $options);
	}
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_autolink_do_sp_activate() {
}

# permissions reset
function sp_autolink_do_reset_permissions() {
}
