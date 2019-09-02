<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_captcha_do_upgrade_check() {
    if (!SP()->plugin->is_active('captcha/sp-captcha-plugin.php')) return;

    $options = SP()->options->get('spCaptcha');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPCAPTCHADBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=6 WHERE auth_name='bypass_captcha'");
    }

    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPCAPTCHADBVERSION;
    SP()->options->update('spCaptcha', $options);
}
