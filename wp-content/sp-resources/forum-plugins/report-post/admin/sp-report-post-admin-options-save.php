<?php
/*
Simple:Press
Profanity Filter Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_report_post_admin_options_save() {
	check_admin_referer('forum-adminform_email', 'forum-adminform_email');

	$option = SP()->options->get('report-post');
	$option['email-list'] = SP()->saveFilters->text(trim($_POST['email-list']));
	SP()->options->update('report-post', $option);

	return __('Report post options updated!', 'sp-report');
}
