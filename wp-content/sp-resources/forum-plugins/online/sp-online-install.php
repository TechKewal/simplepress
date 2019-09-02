<?php
/*
Simple:Press
Who's Online plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_online_do_install() {
	$options = SP()->options->get('online');
	if (empty($options)) {
        $options['dbversion'] = SPWODBVERSION;
        SP()->options->add('online', $options);
    }

    # add a new permission into the auths table
	SP()->auths->add('view_online_activity', __('Can view the online activity', 'sp-spwo'), 1, 0, 0, 0, 2);
    SP()->auths->activate('view_online_activity');

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated.
function sp_online_do_sp_activate() {
}

function sp_online_do_reset_permissions() {
	SP()->auths->add('view_online_activity', __('Can view the online activity', 'sp-spwo'), 1, 0, 0, 0, 2);
}
