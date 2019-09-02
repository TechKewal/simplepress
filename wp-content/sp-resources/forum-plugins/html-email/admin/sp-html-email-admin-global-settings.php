 <?php
/*
Simple:Press
HTML Email Plugin Admin Notifications Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_admin_global_form() {
	$option = SP()->options->get('html-email');

	spa_paint_options_init();
	spa_paint_open_tab(__('HTML Emails', 'sp-html-email').' - '.__('Global Settings', 'sp-html-email'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Global CSS', 'sp-html-email'), true, 'html-email-css');
				spa_paint_wide_textarea(__('CSS Rules - do not include a script tag', 'sp-html-email'), 'email-css', SP()->editFilters->text($option['email-css']), '', 7);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Global Header', 'sp-html-email'), true, 'html-email-header');
				$submessage = '<p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %SITEURL%, %LOGINURL%</strong></p>';
				spa_paint_wide_textarea(__('Global Header HTML', 'sp-html-email'), 'email-header', SP()->editFilters->text($option['email-header']), $submessage, 7);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Global Footer', 'sp-html-email'), true, 'html-email-footer');
				$submessage = '<p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %SITEURL%, %LOGINURL%</strong></p>';
				spa_paint_wide_textarea(__('Global Footer HTML', 'sp-html-email'), 'email-footer', SP()->editFilters->text($option['email-footer']), $submessage, 7);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
	spa_paint_close_container();
}
