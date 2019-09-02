<?php
/*
Simple:Press
Tags Plugin Admin SEO Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_admin_seo_options_form() {
	$tags = SP()->options->get('tags');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Topic Tags SEO Options', 'sp-tags'), true, 'tags-seo');
			spa_paint_checkbox(__('Override custom meta keywords with topic tags (if using) on topic pages', 'sp-tags'), 'tagwords', $tags['tagwords']);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
