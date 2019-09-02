<?php
/*
Simple:Press
HTML Email Plugin Admin New User Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_admin_new_user_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('html-email');
	$option['new-users'] = isset($_POST['new-users']);
	$option['new-users-globals'] = isset($_POST['new-users-globals']);
	$option['new-user-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['new-user-subject'])));
	$option['new-user-body'] = SP()->saveFilters->text(trim($_POST['new-user-body']));
	$option['new-user-admin-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['new-user-admin-subject'])));
	$option['new-user-admin-body'] = SP()->saveFilters->text(trim($_POST['new-user-admin-body']));
	SP()->options->update('html-email', $option);

	return __('HTML email new user emails updated!', 'sp-html-email');
}
