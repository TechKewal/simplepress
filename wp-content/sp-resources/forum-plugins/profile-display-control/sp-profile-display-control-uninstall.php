<?php
/*
Simple:Press
Profile Display Control plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profile_display_control_do_uninstall() {
    SP()->options->delete('profile-display-control');
	# remove glossary entries
	sp_remove_glossary_plugin('sp-profiledisplay');
}

function sp_profile_display_control_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-profiledisplay');
}
