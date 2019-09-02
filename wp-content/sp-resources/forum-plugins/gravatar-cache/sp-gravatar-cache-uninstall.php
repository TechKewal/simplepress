<?php
/*
Simple:Press
Gravatar Cache plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_gravatar_cache_do_uninstall() {
	# remove our storage locations
	SP()->plugin->remove_storage('gravatar-cache');

	# delete options if they still exist
    SP()->options->delete('gravatar_options');
    SP()->options->delete('gravatar_expire');

    SP()->options->delete('gravcache');
}

function sp_gravatar_cache_do_sp_uninstall() {
}
