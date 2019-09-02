<?php
/*
Simple:Press
Featured Topics and Posts Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_featured_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save options
	$topics = SP()->saveFilters->title(trim($_POST['topic_list']));
    $topics = explode(',', $topics);
    SP()->meta->add('featured', 'topics', $topics);

	$posts = SP()->saveFilters->title(trim($_POST['post_list']));
    $posts = explode(',', $posts);
    SP()->meta->add('featured', 'posts', $posts);

	$out = __('Featured topics and posts options updated', 'sp-featured');
	return $out;
}
