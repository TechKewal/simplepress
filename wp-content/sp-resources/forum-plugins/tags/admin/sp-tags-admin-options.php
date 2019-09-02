<?php
/*
Simple:Press
Tags Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_admin_options_form() {
	$tags = array();
	$tags = SP()->options->get('tags');

	spa_paint_options_init();
	spa_paint_open_tab(__('Topic Tags', 'sp-tags').' - '.__('Topic Tags', 'sp-tags'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Topic Tags Options', 'sp-tags'), true, 'topic-tags');
				spa_paint_input(__('Max number of tags per topic (0 = unlimited)', 'sp-tags'), 'maxtags', $tags['maxtags'], false, false);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
