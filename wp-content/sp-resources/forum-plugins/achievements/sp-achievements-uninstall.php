<?php
/*
achievements plugin uninstall
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_achievements_do_uninstall(){
	# remove settings
	SP()->options->delete('achievements');
}

function sp_achievements_do_sp_uninstall() {
}
