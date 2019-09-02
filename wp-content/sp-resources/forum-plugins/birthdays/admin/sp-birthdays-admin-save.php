<?php
/*
Simple:Press
Birthdays Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_birthdays_admin_options_save() {
	check_admin_referer('forum-adminform_display', 'forum-adminform_display');

	$options = SP()->options->get('birthdays');
	$options['days'] = (isset($_POST['days'])) ? SP()->filters->integer($_POST['days']) : 1;
	SP()->options->update('birthdays', $options);
}
