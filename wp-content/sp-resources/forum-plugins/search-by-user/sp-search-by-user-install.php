<?php
/*
Simple:Press
search by user plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_search_by_user_do_install() {
	$options = SP()->options->get('search-by-user');
	if (empty($options)) {
        $options['dbversion'] = SPSEARCHUSERDBVERSION;
        SP()->options->update('search-by-user', $options);
    }
}

# sp reactivated.
function sp_search_by_user_do_sp_activate() {
}

# permissions reset
function sp_search_by_user_do_reset_permissions() {
}
