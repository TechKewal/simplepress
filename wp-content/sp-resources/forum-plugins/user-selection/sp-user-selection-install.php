<?php
/*
User Selection plugin install
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_user_selection_do_install() {
	# fetch settings, to test if they have been installed yet
	$settings = SP()->options->get('user-selection');
	if (empty($settings)) {
		# no settings found, fresh install - setup default settings
		$settings = array();
        $settings['usedefault'] = true;
        $settings['dbversion'] = SPUSELDBVERSION;
		SP()->options->add('user-selection', $settings);
	}
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}
