<?php
/*
Simple:Press
Maintenance Mode Plugin Admin Options Form
$LastChangedDate: 2018-10-23 14:41:19 -0500 (Tue, 23 Oct 2018) $
$Rev: 15766 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_maintenance_admin_options_form() {
	$data = SP()->options->get('maintenance');

	spa_paint_options_init();
	spa_paint_open_tab(__('Maintenance Mode', 'sp-maintenance'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Maintenance Mode Options', 'sp-maintenance'), true, 'maintenance-options');
			     spa_paint_checkbox(__('Enable maintenance mode', 'sp-maintenance'), 'mmenable', $data['mmenable']);
					$submessage = '<br />'.__('Enter the maintenance mode message you would like to display to forum visitors when the maintenance mode is enabled.', 'sp-maintenance');
					spa_paint_wide_textarea(__('Maintenance mode message', 'sp-maintenance'), 'mmmessage', SP()->displayFilters->text($data['mmmessage']), $submessage, 5);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
