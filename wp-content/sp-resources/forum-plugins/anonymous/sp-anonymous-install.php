<?php
/*
Simple:Press
Post Anonymously plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_anonymous_do_install() {
	SP()->activity->add_type('anonymous poster');

	$options = SP()->options->get('anonymous');
	if (empty($options)) {
        $options['dbversion'] = SPANONYMOUSDBVERSION;
        SP()->options->update('anonymous', $options);
    }

    # add a new permission into the auths table
 	SP()->auths->add('post_anonymous', __('Can post anonymously', 'sp-anonymous'), 1, 1, 0, 0, 3);
    SP()->auths->activate('post_anonymous');
}

# sp reactivated.
function sp_anonymous_do_sp_activate() {
 	SP()->auths->add('post_anonymous', __('Can post anonymously', 'sp-anonymous'), 1, 1, 0, 0, 3);
}

# permissions reset
function sp_anonymous_do_reset_permissions() {
 	SP()->auths->add('post_anonymous', __('Can post anonymously', 'sp-anonymous'), 1, 1, 0, 0, 3);
}
