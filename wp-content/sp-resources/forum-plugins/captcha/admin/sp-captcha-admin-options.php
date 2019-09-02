 <?php
/*
Simple:Press
Captcha Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_captcha_admin_options_form() {
	$captcha = SP()->options->get('spCaptcha');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Captcha on User Registration', 'sp-cap'), true, 'captcha');
			spa_paint_checkbox(__('Add Captcha form to WP registration/signup form', 'sp-cap'), 'registration', $captcha['registration']);
		spa_paint_close_fieldset();
	spa_paint_close_panel();

	do_action('sph_captcha_options_panel');
}
