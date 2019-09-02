<?php
/*
Simple:Press
Syntax Highlighting Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------
# Save new options from admins post content panel
# ------------------------------------------------------
function sp_syntax_admin_options_save() {
	check_admin_referer('forum-adminform_content', 'forum-adminform_content');

	$sfsyntax = array();
	if (isset($_POST['sfsyntaxforum'])) { $sfsyntax['sfsyntaxforum'] = true; } else { $sfsyntax['sfsyntaxforum'] = false; }
	if (isset($_POST['sfsyntaxblog'])) { $sfsyntax['sfsyntaxblog'] = true; } else { $sfsyntax['sfsyntaxblog'] = false; }

	# clean up brushes string
	$list = explode(',', $_POST['sfbrushes']);
	$brushes = array();
	if ($list) 	{
		foreach($list as $item) {
			$brushes[] = SP()->filters->str(trim($item));
		}
		$list = implode(',', $brushes);
	}
	$sfsyntax['sfbrushes']=$list;

	SP()->options->update('sfsyntax', $sfsyntax);
}
