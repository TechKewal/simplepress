<?php
/*
Simple:Press
Post As plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_post_as_do_install() {
	$options = SP()->options->get('post-as');
	if (empty($options)) {
        $options['dbversion'] = SPPOSTASDBVERSION;
        SP()->options->update('post-as', $options);
    }

    # add a new permission into the auths table
	SP()->auths->add('post_as_user', __('Can create a forum post as different user', 'sp-as-post'), 1, 1, 0, 0, 3);

    # activation so make our auth active
    SP()->auths->activate('post_as_user');
}

# sp reactivated.
function sp_post_as_do_sp_activate() {
}

# permissions reset
function sp_post_as_do_reset_permissions() {
	SP()->auths->add('post_as_user', __('Can create a forum post as different user', 'sp-as-post'), 1, 1, 0, 0, 3);
}
