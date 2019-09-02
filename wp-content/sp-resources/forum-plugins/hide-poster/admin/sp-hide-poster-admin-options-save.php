<?php
/*
Simple:Press
Hide Posters Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_hide_poster_admin_options_save() {
	check_admin_referer('forum-adminform_content', 'forum-adminform_content');

	$options = SP()->options->get('hide-poster');
    $options['default_enable'] = isset($_POST['default_enable']);
	SP()->options->update('hide-poster', $options);
}
