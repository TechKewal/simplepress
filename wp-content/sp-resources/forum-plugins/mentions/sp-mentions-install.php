<?php
/*
Simple:Press
Mentions plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_mentions_do_install() {
	SP()->activity->add_type('mentions');

	$options = SP()->options->get('mentions');
	if (empty($options)) {
		$options['notification'] = 2;
		$options['latest_number'] = 10;
		$options['dbversion'] = SPMENTIONSDBVERSION;
		SP()->options->update('mentions', $options);
	}
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_mentions_do_sp_activate() {
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# permissions reset
function sp_mentions_do_reset_permissions() {
}
