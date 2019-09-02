<?php
/*
Simple:Press
v plugin install/upgrade routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/**
 * Run once analytics plugin install
 * 
 * @global object $wp_roles
 */
function sp_analytics_do_install() {
	$options = SP()->options->get('analytics');
	if (empty($options)) {
        
        $options['dbversion'] = SP_ANALYTICS_DB_VERSION;
        SP()->options->update('analytics', $options);
    }


    global $wp_roles;
    $wp_roles->add_cap('administrator', 'SPF Manage Analytics', false);

    # do we need to give activater Manage Analytics capability
    if (!SP()->auths->current_user_can('SPF Manage Analytics')) {
		$user = new WP_User(SP()->user->thisUser->ID);
		$user->add_cap('SPF Manage Analytics');
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_analytics_do_sp_activate() {
}