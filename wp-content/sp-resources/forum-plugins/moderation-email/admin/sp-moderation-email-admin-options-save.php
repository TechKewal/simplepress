<?php
/*
Simple:Press
Moderation Email Plugin Admin Options Save Routine
$LastChangedDate: 2015-04-15 20:09:47 -0700 (Wed, 15 Apr 2015) $
$Rev: 12722 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_moderation_email_admin_options_save() {
	check_admin_referer('forum-adminform_email', 'forum-adminform_email');

	$option = SP()->options->get('moderation-email');
    $option['modemail'] = isset($_POST['modemail']);
	$option['modemailsubject'] = SP()->saveFilters->title(trim($_POST['modemailsubject']));
	$option['modemailtext'] = SP()->saveFilters->title(trim($_POST['modemailtext']));
	SP()->options->update('moderation-email', $option);

	return __('Moderation-email options updated!', 'sp-moderation-email');
}
