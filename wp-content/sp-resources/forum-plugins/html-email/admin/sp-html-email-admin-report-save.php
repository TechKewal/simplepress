<?php
/*
Simple:Press
HTML Email Plugin Admin Report Post Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_save_report() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('html-email');
	$option['report'] = isset($_POST['report']);
	$option['report-globals'] = isset($_POST['report-globals']);
	$option['report-subject'] = SP()->saveFilters->escape(SP()->saveFilters->nohtml(trim($_POST['report-subject'])));
	$option['report-body'] = SP()->saveFilters->text(trim($_POST['report-body']));
	SP()->options->update('html-email', $option);

	return __('HTML email report post emails updated!', 'sp-html-email');
}
