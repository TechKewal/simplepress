<?php
/*
Simple:Press
Tags Plugin Admin SEO Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_admin_seo_options_save() {
	check_admin_referer('forum-adminform_seo', 'forum-adminform_seo');

	$tags = SP()->options->get('tags');
	if (isset($_POST['tagwords'])) { $tags['tagwords'] = true; } else { $tags['tagwords'] = false; }
	SP()->options->update('tags', $tags);

	return __('Topic tags SEO options updated', 'sp-tags');
}
