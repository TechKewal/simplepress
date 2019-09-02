<?php
/*
Simple:Press
Captcha plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_captcha_do_install() {
	$captcha = SP()->options->get('spCaptcha');
	if (empty($captcha)) {
        $captcha['registration'] = true;
        $captcha['dbversion'] = SPCAPTCHADBVERSION;
        SP()->options->update('spCaptcha', $captcha);
    }

    # add a new permission into the auths table
	SP()->auths->add('bypass_captcha', __('Can bypass the post captcha check', 'sp-cap'), 1, 0, 0, 0, 6);
    SP()->auths->activate('bypass_captcha');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

function sp_captcha_do_permissions_reset() {
	SP()->auths->add('bypass_captcha', __('Can bypass the post captcha check', 'sp-cap'), 1, 0, 0, 0, 6);
}
