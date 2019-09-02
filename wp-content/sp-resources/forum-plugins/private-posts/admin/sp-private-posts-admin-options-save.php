<?php
/*
Simple:Press
Private Posts Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_private_posts_do_admin_save_options() {
	check_admin_referer('forum-adminform_content', 'forum-adminform_content');

	# Save options
	$options = SP()->options->get('private-posts');

    $options['uninstall'] = SP()->filters->integer($_POST['private_parts_uninstall']);
    $options['private-text'] = SP()->saveFilters->text(trim($_POST['private-text']));

    SP()->options->update('private-posts', $options);
}
