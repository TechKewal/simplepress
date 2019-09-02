<?php
/*
Simple:Press
Mentions Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_mentions_do_admin_save_options() {
	check_admin_referer('forum-adminform_members', 'forum-adminform_members');

	$options = SP()->options->get('mentions');

    $options['notification'] = SP()->filters->integer($_POST['notification']);
    $options['latest_number'] = SP()->filters->integer($_POST['latest_number']);

	SP()->options->update('mentions', $options);
}
