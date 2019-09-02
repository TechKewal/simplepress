<?php
/*
Simple:Press
HTML Email Plugin Admin PW Change Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_save_pw_change() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');
	$option = SP()->options->get('html-email');
	$option['pw-change'] = isset($_POST['pw-change']);
	$option['pw-change-globals'] = isset($_POST['pw-change-globals']);
	$option['pw-change-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['pw-change-subject'])));
	$option['pw-change-body'] = SP()->saveFilters->text(trim($_POST['pw-change-body']));
	$option['pw-change-admin-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['pw-change-admin-subject'])));
	$option['pw-change-admin-body'] = SP()->saveFilters->text(trim($_POST['pw-change-admin-body']));
	SP()->options->update('html-email', $option);

	return __('HTML email password changed emails updated!', 'sp-html-email');
}
