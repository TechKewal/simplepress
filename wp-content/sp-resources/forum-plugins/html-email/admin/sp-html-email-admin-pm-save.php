<?php
/*
Simple:Press
HTML Email Plugin Admin New PM Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_save_newpm() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('html-email');
	$option['newpm'] = isset($_POST['newpm']);
	$option['newpm-globals'] = isset($_POST['newpm-globals']);
	$option['newpm-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['newpm-subject'])));
	$option['newpm-body'] = SP()->saveFilters->text(trim($_POST['newpm-body']));
	SP()->options->update('html-email', $option);

	return __('HTML email new private message emails updated!', 'sp-html-email');
}
