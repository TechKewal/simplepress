<?php
/*
Simple:Press
Post Multiple Forums plugin install/upgrade routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_post_multiple_do_install() {
	$options = SP()->options->get('post-multiple');
	if (empty($options)) {
        $options['exclude'] = array();

        $options['dbversion'] = SPMULTIDBVERSION;
        SP()->options->update('post-multiple', $options);
    }

    # add a new permission into the auths table
	SP()->auths->add('post_multiple', __('Can create a new topic in multiple forums', 'sp-post-multiple'), 1, 1, 0, 0, 3);

    # activation so make our auth active
    SP()->auths->activate('post_multiple');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_post_multiple_do_sp_activate() {
}

# permissions reset
function sp_post_multiple_do_reset_permissions() {
	SP()->auths->add('post_multiple', __('Can create a new topic in multiple forums', 'sp-post-multiple'), 1, 1, 0, 0, 3);
}
