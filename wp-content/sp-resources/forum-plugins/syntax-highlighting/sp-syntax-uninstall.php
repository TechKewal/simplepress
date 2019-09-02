<?php
/*
Simple:Press
Syntax Highlighting plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------
# Uninstall so remove any data
# ------------------------------------------------------
function sp_syntax_do_uninstall() {
	SP()->options->delete('sfsyntax');
	# remove glossary entries
	sp_remove_glossary_plugin('sp-syntax');
}

function sp_syntax_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-syntax');
}
