<?php
/*
Simple:Press
Membership Subscribe plugin install/upgrade routine
$LastChangedDate: 2013-04-17 19:24:03 -0700 (Wed, 17 Apr 2013) $
$Rev: 10182 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_membership_subscribe_do_install() {
	$options = SP()->options->get('membership-subscribe');
	if (empty($options)) {
        $options['dbversion'] = SPMEMSUBDBVERSION;
        SP()->options->update('membership-subscribe', $options);
    }
}

# sp reactivated.
function sp_membership_subscribe_do_sp_activate() {
}

# permissions reset
function sp_membership_subscribe_do_reset_permissions() {
}
