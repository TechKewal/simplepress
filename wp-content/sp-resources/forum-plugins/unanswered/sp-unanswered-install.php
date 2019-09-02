<?php
/*
Simple:Press
Unanswered plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_unanswered_do_install() {
	$options = SP()->options->get('unanswered');
	if (empty($options)) {
        $options['dbversion'] = SPUADBVERSION;
        SP()->options->update('unanswered', $options);
    }

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated.
function sp_unanswered_do_sp_activate() {
}
