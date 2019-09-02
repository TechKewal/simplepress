<?php
/*
Simple:Press
Threading Plugin Admin Options Save Routine
$LastChangedDate: 2015-04-16 04:09:47 +0100 (Thu, 16 Apr 2015) $
$Rev: 12722 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_threading_admin_options_save() {
	check_admin_referer('forum-adminform_global', 'forum-adminform_global');

	$thread = SP()->options->get('threading');
	$thread['maxlevel'] = SP()->filters->integer($_POST['maxlevel']);
	SP()->options->update('threading', $thread);
}
