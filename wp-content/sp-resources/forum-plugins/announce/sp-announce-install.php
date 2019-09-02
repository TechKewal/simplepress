<?php
/*
Simple:Press
Announce plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_announce_do_install() {
	$options = SP()->options->get('announce');
	if (empty($options)) {
    	$options['location'] = 1; # show before all forum content
    	$options['showto'] = 1; # show to everyone
        $options['message'] = '';

        $options['dbversion'] = SPANNOUNCEDBVERSION;
        SP()->options->update('announce', $options);
    }

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_announce_do_sp_activate() {
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# permissions reset
function sp_announce_do_reset_permissions() {
}
