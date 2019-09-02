<?php
/*
Simple:Press
Membership Subscribe plugin install/upgrade routine
$LastChangedDate: 2013-04-17 19:24:03 -0700 (Wed, 17 Apr 2013) $
$Rev: 10182 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_membership_subscribe_do_upgrade_check() {
    if (!SP()->plugin->is_active('membership-subscribe/sp-membership-subscribe-plugin.php')) return;

    $options = SP()->options->get('membership-subscribe');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPMEMSUBDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $options['dbversion'] = SPMEMSUBDBVERSION;
    SP()->options->update('membership-subscribe', $options);
}
