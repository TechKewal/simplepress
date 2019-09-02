<?php
/*
Simple:Press
Warning and Suspensions Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_warnings_suspensions_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save options
	$data = SP()->options->get('warnings-suspensions');

	if (isset($_POST['profile'])) { $data['profile'] = true; } else { $data['profile'] = false; }

	$data['notify'] = SP()->filters->integer($_POST['notify']);

    $data['warn_title'] = SP()->saveFilters->title(trim($_POST['warn_title']));
    $data['warn_message'] = SP()->saveFilters->text(trim($_POST['warn_message']));
    $data['warn_profile'] = SP()->saveFilters->text(trim($_POST['warn_profile']));
    $data['suspension_title'] = SP()->saveFilters->title(trim($_POST['suspension_title']));
    $data['suspension_message'] = SP()->saveFilters->text(trim($_POST['suspension_message']));
    $data['suspension_profile'] = SP()->saveFilters->text(trim($_POST['suspension_profile']));
    $data['ban_title'] = SP()->saveFilters->title(trim($_POST['ban_title']));
    $data['ban_message'] = SP()->saveFilters->text(trim($_POST['ban_message']));
    $data['ban_profile'] = SP()->saveFilters->text(trim($_POST['ban_profile']));

	SP()->options->update('warnings-suspensions', $data);

	$mess = __('Warnings and suspension options updated', 'sp-warnings-suspensions');
	return $mess;
}
