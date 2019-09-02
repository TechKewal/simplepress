<?php
/*
Simple:Press
Captcha plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the captcha plugin uninstall only
function sp_captcha_do_uninstall() {
    # delete our option
    SP()->options->delete('spCaptcha');

    SP()->auths->delete('bypass_captcha');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-captcha');
}
