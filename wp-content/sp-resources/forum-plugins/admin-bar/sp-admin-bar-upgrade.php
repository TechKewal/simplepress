<?php
/*
Simple:Press
Admin Bar plugin install/upgrade routine
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_admin_bar_do_upgrade_check() {
    if (!SP()->plugin->is_active('admin-bar/sp-admin-bar-plugin.php')) return;

    $options = SP()->options->get('spAdminBar');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPABDBVERSION ) return;

    # apply upgrades as needed
    if ($db < 1) {
        # empty since plugin did not used db on initial release
    }

    if ($db < 2) {
        # empty since no longer valid in 6.0+
    }

    if ($db < 3) {
        # permission for bypassing akismet checks
    	SP()->auths->add('bypass_akismet', __('Can bypass akismet check on posts', 'spab'), 1, 0, 0, 0, 3);
        SP()->auths->activate('bypass_akismet');
    }

    if ($db < 4) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    if ($db < 5) {
		# update to namespaced js
	    $up = SP()->meta->get('autoupdate', 'admin');
		$up[0]['meta_value'][0] = 'spj.adminBarUpdate';
	    SP()->meta->update('autoupdate', 'admin', $up[0]['meta_value'], $up[0]['meta_id']);
	}

	# save data
    $options['dbversion'] = SPABDBVERSION;
    SP()->options->update('spAdminBar', $options);
}
