<?php
/*
Simple:Press
Print Topic plugin install routine
$LastChangedDate: 2013-03-18 13:47:54 +0000 (Mon, 18 Mar 2013) $
$Rev: 10093 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_print_topic_do_install() {
	$options = SP()->options->get('print_topic');
	if (empty($options)) {
        $options['dbversion'] = SPPTDBVERSION;
        SP()->options->update('print_topic', $options);
    }

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated.
function sp_print_topic_do_sp_activate() {
}
