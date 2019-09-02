<?php
/*
Simple:Press
Birthdays Plugin Admin Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_birthdays_admin_options_form() {
    $options = array();
	$options = SP()->options->get('birthdays');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Birthdays Window', 'sp-gsm'), true, 'birthdays');
            spa_paint_input(__('Number of days to show upcoming birthdays', 'sp-birthdays'), 'days', $options['days']);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
