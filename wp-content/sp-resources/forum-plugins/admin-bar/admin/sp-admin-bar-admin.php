<?php
/*
Simple:Press
Admin Bar Plugin Admin Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_admin_bar_admin_form() {
	$options = SP()->options->get('spAdminBar');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Admin Bar Options', 'spab'), 'true', 'admin-bar');
			spa_paint_checkbox(__('Display the admins postbag in the dashboard', 'spab'), "dashboardposts", $options['dashboardposts']);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}

# ------------------------------------------------------
# Insert new options (post content) section
# ------------------------------------------------------
function sp_akismet_admin_options_form() {
	$spAkismet = SP()->options->get('spAkismet');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Akismet', 'spab'), true, 'akismet');
		if (function_exists('akismet_http_post')) {
			$values = array(__('Do not use Akismet', 'spab'), __('Place Akismet marked spam posts into moderation', 'spab'), __('Do not save Akismet marked spam posts', 'spab'));
			spa_paint_radiogroup(__('Select Akismet Option', 'spab'), 'spAkismet', $values, $spAkismet, '', false, true);
		} else {
			_e('Akismet is not currently active on this site', 'spab');
		}
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
