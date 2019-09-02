<?php
/*
Simple:Press
Tags Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$tags = SP()->options->get('tags');
	$tags['maxtags'] = SP()->filters->integer($_POST['maxtags']);
	SP()->options->update('tags', $tags);

	return __('Topic tags options updated', 'sp-tags');
}
