<?php
/*
Simple:Press
Buddypress plugin install/upgrade routine
$Rev: 15725 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_buddypress_do_install() {
	$options = SP()->options->get('buddypress');
	if (empty($options)) {
    	$options['activity'] = 3; # show activity for all posts
    	$options['avatar'] = 1; # keep separate

    	$options['bpavatarsize'] = false;

    	$options['integrateprofile'] = true;
    	$options['integratesubs'] = true;
    	$options['integratewatches'] = true;

    	$options['uselinks'] = true;
    	$options['newlink'] = true;
    	$options['inboxlink'] = true;
    	$options['subslink'] = true;
    	$options['watcheslink'] = true;
    	$options['profilelink'] = true;
    	$options['startedlink'] = true;
    	$options['postedlink'] = true;

    	$options['usenotifications'] = true;
    	$options['newnotifications'] = true;
    	$options['inboxnotifications'] = true;
    	$options['subsnotifications'] = true;
    	$options['watchesnotifications'] = true;

        $options['dbversion'] = SPBUDDYPRESSDBVERSION;
        SP()->options->update('buddypress', $options);
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated.
function sp_buddypress_do_sp_activate() {
}

# permissions reset
function sp_buddypress_do_reset_permissions() {
}
