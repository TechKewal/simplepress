<?php
/*
Simple:Press
Captcha Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_captcha_admin_options_save() {
	check_admin_referer('forum-adminform_login', 'forum-adminform_login');

    $captcha = array();
    $captcha['registration'] = isset($_POST['registration']);
	SP()->options->update('spCaptcha', $captcha);

    do_action('sph_captcha_uploads_save');

	return __('Captcha options updated!', 'sp-cap');
}
