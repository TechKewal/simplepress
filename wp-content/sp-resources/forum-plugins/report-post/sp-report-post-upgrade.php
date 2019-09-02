<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_report_post_do_upgrade_check() {
    if (!SP()->plugin->is_active('report-post/sp-report-post-plugin.php')) return;

    $options = SP()->options->get('report-post');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPRPDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=7 WHERE auth_name='report_posts'");
    }

    # db version upgrades
    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPRPDBVERSION;
    SP()->options->update('report-post', $options);
}
