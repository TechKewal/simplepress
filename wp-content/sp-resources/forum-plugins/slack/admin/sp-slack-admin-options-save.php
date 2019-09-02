<?php
/*
Simple:Press
slack integration Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_slack_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save options
	$options = SP()->options->get('slack');

    $options['slack-weburl'] = SP()->saveFilters->title(trim($_POST['slack-weburl']));
    $options['slack-channel'] = SP()->saveFilters->title(trim($_POST['slack-channel']));
    $options['slack-name'] = SP()->saveFilters->title(trim($_POST['slack-name']));

    $options['notifynewpost'] = isset($_POST['notifynewpost']);
    $options['notifynewuser'] = isset($_POST['notifynewuser']);

    SP()->options->update('slack', $options);

	$out = __('Slack options updated', 'sp-slack');
	return $out;
}
