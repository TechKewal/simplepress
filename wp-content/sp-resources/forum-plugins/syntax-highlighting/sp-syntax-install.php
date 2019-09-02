<?php
/*
Simple:Press
Syntax Highlighting plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------
# Initial activate - create necessary options etc
# ------------------------------------------------------
function sp_syntax_do_install()
{
	$syntax = SP()->options->get('sfsyntax');
	if(empty($syntax))
	{
		$syntax = array();
        $syntax['dbversion'] = 0;
		$syntax['sfsyntaxforum'] = true;
		$syntax['sfsyntaxblog']  = false;
		$syntax['sfbrushes'] = 'apache,applescript,asm,bash-script,bash,basic,clang,css,diff,html,javascript,lisp,ooc,php,python,ruby,sql';
		SP()->options->add('sfsyntax', $syntax);
	}
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

function sp_syntax_do_upgrade() {
    if (!SP()->plugin->is_active('syntax-highlighting/sp-syntax-plugin.php')) return;

    $syntax = SP()->options->get('sfsyntax');

    $db = $syntax['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPSYNTAXDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $syntax['dbversion'] = SPSYNTAXDBVERSION;
    SP()->options->update('sfsyntax', $syntax);

}
