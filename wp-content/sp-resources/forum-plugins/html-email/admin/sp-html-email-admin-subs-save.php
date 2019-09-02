<?php
/*
Simple:Press
HTML Email Plugin Admin Subscriptions Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_save_subs() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('html-email');
	$option['subs'] = isset($_POST['subs']);
	$option['subs-globals'] = isset($_POST['subs-globals']);
	$option['subs-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['subs-subject'])));
	$option['subs-body'] = SP()->saveFilters->text(trim($_POST['subs-body']));
	SP()->options->update('html-email', $option);

	return __('HTML email subscription notification emails updated!', 'sp-html-email');
}
