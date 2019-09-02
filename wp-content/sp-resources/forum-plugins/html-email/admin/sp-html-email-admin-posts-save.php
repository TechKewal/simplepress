<?php
/*
Simple:Press
HTML Email Plugin Admin Notifications Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_admin_posts_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('html-email');
	$option['admin-notifications'] = isset($_POST['admin-notifications']);
	$option['admin-notifications-globals'] = isset($_POST['admin-notifications-globals']);
	$option['admin-notification-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['admin-notification-subject'])));
	$option['admin-notification-body'] = SP()->saveFilters->text(trim($_POST['admin-notification-body']));
	$option['admin-notification-modtext'] = SP()->saveFilters->text(trim($_POST['admin-notification-modtext']));

	SP()->options->update('html-email', $option);

	return __('HTML email admin notification emails updated!', 'sp-html-email');
}
