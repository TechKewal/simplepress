<?php
/*
Simple:Press
HTML Email Plugin Admin Notifications Save Routine
$LastChangedDate: 2014-10-30 01:55:14 +0000 (Thu, 30 Oct 2014) $
$Rev: 12035 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_admin_global_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('html-email');
	$option['email-css'] = SP()->saveFilters->text(trim($_POST['email-css']));
	$option['email-header'] = SP()->saveFilters->text(trim($_POST['email-header']));
	$option['email-footer'] = SP()->saveFilters->text(trim($_POST['email-footer']));
	SP()->options->update('html-email', $option);

	return __('HTML email global settings updated!', 'sp-html-email');
}
