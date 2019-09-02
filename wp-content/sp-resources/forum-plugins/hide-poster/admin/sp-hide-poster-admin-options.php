<?php
/*
Simple:Press
Hide Posters Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_hide_poster_admin_options_form() {
	$options = SP()->options->get('hide-poster');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Hide Poster Info', 'sp-hide-poster'), true, 'hide-poster');
			spa_paint_checkbox(__("Enable hide poster info by default on new topic form (if enabled in forum)", 'sp-hide-poster'), 'default_enable', $options['default_enable']);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
