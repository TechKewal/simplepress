<?php
/*
Simple:Press
HTML Email Plugin Admin Mentions Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_save_mentions() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('html-email');
	$option['mentions'] = isset($_POST['mentions']);
	$option['mentions-globals'] = isset($_POST['mentions-globals']);
	$option['mentions-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['mentions-subject'])));
	$option['mentions-body'] = SP()->saveFilters->text(trim($_POST['mentions-body']));
	SP()->options->update('html-email', $option);

	return __('HTML email mentions emails updated!', 'sp-html-email');
}
