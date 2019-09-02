<?php
/*
Simple:Press
Admin Bar Plugin Admin Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_admin_bar_options_form() {
	if(!isset(SP()->user->thisUser->sfadminbar)) SP()->user->thisUser->sfadminbar = false;
	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Your Admin Bar Options', 'spab'), 'true', 'admin-bar-options');
			spa_paint_checkbox(__('Display the Simple:Press admin bar', 'spab'), 'adminbar', SP()->user->thisUser->sfadminbar);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
