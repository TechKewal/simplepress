<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_policy_doc_do_upgrade_check() {
    if (!SP()->plugin->is_active('policy-doc/sp-policy-doc-plugin.php')) return;

    $options = SP()->options->get('policy-doc');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPPDDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
        # set autoload flag to true
		$msg = SP()->meta->get('registration', 'policy');
		if (!empty($msg[0])) SP()->meta->update('registration', 'policy', $msg[0]['meta_value'], $msg[0]['meta_id']);
		$msg = SP()->meta->get('privacy', 'policy');
		if (!empty($msg[0])) SP()->meta->update('privacy', 'policy', $msg[0]['meta_value'], $msg[0]['meta_id']);
    }

    # db version upgrades
    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPPDDBVERSION;
    SP()->options->update('policy-doc', $options);
}
