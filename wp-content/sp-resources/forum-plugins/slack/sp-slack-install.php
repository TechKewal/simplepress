<?php
/*
Simple:Press
slack integration plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_slack_do_install() {
	$options = SP()->options->get('slack');
	if (empty($options)) {
        $options['slack-weburl'] = '';
        $options['slack-channel'] = '';
        $options['slack-name'] = '';
        $options['notifynewpost'] = true;
        $options['notifynewuser'] = true;

        $options['dbversion'] = SPSLACKDBVERSION;

        SP()->options->update('slack', $options);
    }
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_slack_do_sp_activate() {
}

# permissions reset
function sp_slack_do_reset_permissions() {
}
