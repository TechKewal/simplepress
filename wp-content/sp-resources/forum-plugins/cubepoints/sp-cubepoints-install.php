<?php
/*
cubepoints plugin install
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_cubepoints_do_install() {
	# fetch settings, to test if they have been installed yet
	$settings = SP()->options->get('cubepoints');
	if (empty($settings)) {
		# no settings found, fresh install - setup default settings
		$settings = array();
		$settings['points_topic'] = 10;
		$settings['points_post'] = 5;
		$settings['points_rate_post'] = 2;
		$settings['points_post_rated'] = 1;
		$settings['points_create_poll'] = 5;
		$settings['points_vote_poll'] = 3;
		$settings['points_poll_voted'] = 1;
		$settings['points_cap'] = 0;
		$settings['logging'] = true;
		$settings['admins'] = true;
		$settings['moderators'] = true;
		$settings['points_delete'] = true;
        $settings['dbversion'] = SPCUBEDBVERSION;
		SP()->options->add('cubepoints', $settings);
	}
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}
