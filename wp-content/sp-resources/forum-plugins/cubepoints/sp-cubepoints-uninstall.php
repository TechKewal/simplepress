<?php
/*
cubepoints plugin uninstall
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_cubepoints_do_uninstall(){
	# remove settings
	SP()->options->delete('cubepoints');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-cubepoints');
}

function sp_cubepoints_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-cubepoints');
}

function sp_cubepoints_do_sp_uninstall() {
}
