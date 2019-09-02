<?php
/*
Simple:Press
HTML Email Plugin Admin Subscriptions Digests Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_save_digests() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('html-email');
	$option['digests'] = isset($_POST['digests']);
	$option['digests-globals'] = isset($_POST['digests-globals']);
	$option['digests-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['digests-subject'])));
	$option['digests-header'] = SP()->saveFilters->text(trim($_POST['digests-header']));
	$option['digests-body'] = SP()->saveFilters->text(trim($_POST['digests-body']));
	$option['digests-footer'] = SP()->saveFilters->text(trim($_POST['digests-footer']));
	SP()->options->update('html-email', $option);

	return __('HTML email subscription digest emails updated!', 'sp-html-email');
}
