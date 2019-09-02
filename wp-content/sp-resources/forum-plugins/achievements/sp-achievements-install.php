<?php
/*
achievements plugin install
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_achievements_do_install() {
	# fetch settings, to test if they have been installed yet
	$settings = SP()->options->get('achievements');
	if (empty($settings)) {
		# no settings found, fresh install - setup default settings
        $settings['dbversion'] = SPACHDBVERSION;
		SP()->options->add('achievements', $settings);
	}
}
