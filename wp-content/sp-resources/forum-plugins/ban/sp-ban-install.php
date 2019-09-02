<?php
/*
Simple:Press
Ban plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_ban_do_install() {
	$options = SP()->options->get('ban');
	if (empty($options)) {
	    $init = array();
        SP()->options->add('banned_ips', $init);
        SP()->options->add('banned_ip_ranges', $init);
        SP()->options->add('banned_hostnames', $init);
        SP()->options->add('banned_agents', $init);
        SP()->options->add('banned_users', $init);

        $options['general-message'] = __('You have been banned from this forum', 'sp-ban');
        $options['user-message'] = __('You have been banned from this forum', 'sp-ban');
        $options['ug-message'] = __('Your account has been temporarily restricted and moved to a different usergroup', 'sp-ban');
        $options['restore-message'] = __('Your ban has expired and your account has been restored', 'sp-ban');
        $options['dbversion'] = SPBANDBVERSION;
        SP()->options->update('ban', $options);
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated.
function sp_ban_do_sp_activate() {
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# permissions reset
function sp_ban_do_reset_permissions() {
}
