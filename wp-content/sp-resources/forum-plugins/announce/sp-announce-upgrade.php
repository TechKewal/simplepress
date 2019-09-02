<?php
/*
Simple:Press
Announce plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_announce_do_upgrade_check() {
    if (!SP()->plugin->is_active('announce/sp-announce-plugin.php')) return;

    $options = SP()->options->get('announce');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPANNOUNCEDBVERSION ) return;

    # apply upgrades as needed
    # db version upgrades

    if ($db < 1) {
        $options['loggedinusers'] = false;
    }

    if ($db < 2) {
        if (!$options['loggedinusers']) {
            $options['showto'] = 1;
        } else {
            $options['showto'] = 2;
        }
        unset($options['loggedinusers']);
    }

    if ($db < 3) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPANNOUNCEDBVERSION;
    SP()->options->update('announce', $options);
}
