<?php
/*
Simple:Press
Mentions Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_mentions_do_admin_options() {
	$options = SP()->options->get('mentions');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Mentions', 'sp-mentions'), true, 'mentions-options');
			$values = array(__('None', 'sp-mentions'), __('Notification', 'sp-mentions'), __('Private Message (requires PM plugin)', 'sp-mentions'), __('Email', 'sp-mentions'));
			spa_paint_radiogroup(__('Select type of notification to users when mentioned', 'sp-mentions'), 'notification', $values, $options['notification'], false, true);
            spa_paint_input(__('Number of recent mentions to remember/display (used in recent mentions template tags)', 'sp-mentions'), 'latest_number', $options['latest_number']);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
