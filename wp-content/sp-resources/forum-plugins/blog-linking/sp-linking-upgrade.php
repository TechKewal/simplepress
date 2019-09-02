<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_linking_do_upgrade_check() {
    if (!SP()->plugin->is_active('blog-linking/sp-linking-plugin.php')) return;

    $options = SP()->options->get('sfpostlinking');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPLINKINGDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=3 WHERE auth_name='create_linked_topics'");
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=5 WHERE auth_name='break_linked_topics'");
    }

    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPLINKINGDBVERSION;
    SP()->options->update('sfpostlinking', $options);
}
