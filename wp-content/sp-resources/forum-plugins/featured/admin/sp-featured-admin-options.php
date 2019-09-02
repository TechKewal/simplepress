<?php
/*
Simple:Press
Featured Topics and Posts Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_featured_admin_options_form() {
    $meta = SP()->meta->get('featured', 'topics');
    $topics = implode(',', $meta[0]['meta_value']);
    $meta = SP()->meta->get('featured', 'posts');
    $posts = implode(',', $meta[0]['meta_value']);

	spa_paint_options_init();
	spa_paint_open_tab(__('Featured Topics and Posts Plugin', 'sp-featured'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Featured Topics and Posts Options', 'sp-featured'), true, 'featured-lists');
				spa_paint_input(__('List of featured topic IDs', 'sp-featured'), 'topic_list', SP()->displayFilters->title($topics));
				spa_paint_input(__('List of featured post IDs', 'sp-featured'), 'post_list', SP()->displayFilters->title($posts));
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
