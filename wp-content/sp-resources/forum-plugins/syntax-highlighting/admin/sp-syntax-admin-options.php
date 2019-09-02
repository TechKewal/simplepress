<?php
/*
Simple:Press
Syntax Highlighting Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------
# Insert new options (post content) section
# ------------------------------------------------------
function sp_syntax_admin_options_form()
{
	$sfsyntax = SP()->options->get('sfsyntax');

		spa_paint_open_panel();
			spa_paint_open_fieldset(__("Code Syntax Highlighting", "sp-syntax"), true, 'syntax-highlighting');
				spa_paint_checkbox(__("Use syntax highlighting in forum posts", "sp-syntax"), "sfsyntaxforum", $sfsyntax['sfsyntaxforum']);
				spa_paint_checkbox(__("Use syntax highlighting in blog linked posts", "sp-syntax"), "sfsyntaxblog", $sfsyntax['sfsyntaxblog']);
				spa_paint_input(__("Languages (comma separated)", "sp-syntax"), "sfbrushes", $sfsyntax['sfbrushes'], false, true);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
}
