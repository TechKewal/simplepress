<?php
/*
Simple:Press
Search Admin Save settings
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_search_do_admin_options_save() {
	check_admin_referer('forum-adminform_display', 'forum-adminform_display');

	$options = SP()->options->get('search');

	$options['form'] = SP()->filters->str($_POST['form']);
	$options['ftab'] = SP()->filters->str($_POST['ftab']);
	$options['btab'] = SP()->filters->str($_POST['btab']);

    $post_types = get_post_types(array('public' => true));
    $ignore = array('attachment', 'revision', 'nav_menu_item');
	foreach ($post_types as $key => $value) {
      	if (!in_array($key, $ignore)) {
			$type = 'searchposttype_'.$key;
    		$options['searchposttypes'][$key] = isset($_POST[$type]);
		}
	}

	SP()->options->update('search', $options);
}
