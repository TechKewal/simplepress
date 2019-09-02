<?php
/*
Simple:Press
timezone registration plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_timezone_do_install() {
	$options = SP()->options->get('timezone');
	if (empty($options)) {
        $options['dbversion'] = SPTIMEZONEDBVERSION;

        SP()->options->update('timezone', $options);
    }
}

# sp reactivated.
function sp_timezone_do_sp_activate() {
}

# permissions reset
function sp_timezone_do_reset_permissions() {
}
