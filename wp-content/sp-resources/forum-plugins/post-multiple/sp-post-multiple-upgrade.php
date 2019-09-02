<?php
/*
Simple:Press
Post Multiple Forums plugin install/upgrade routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_post_multiple_do_upgrade_check() {
    if (!SP()->plugin->is_active('post-multiple/sp-post-multiple-plugin.php')) return;

    $options = SP()->options->get('post-multiple');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPMULTIDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    # db version upgrades
    if ($db < 1) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPMULTIDBVERSION;
    SP()->options->update('post-multiple', $options);
}
