<?php
/*
Simple:Press
Ranks Info plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rank_info_do_install() {
	$options = SP()->options->get('rank-info');
	if (empty($options)) {
        $options['badge'] = true;
        $options['membership'] = true;
        $options['users'] = true;
        $options['same_rank'] = false;
        $options['special_ranks'] = true;
        $options['special_users'] = true;
        $options['same_special_rank'] = false;

        $options['dbversion'] = SPRANKSDBVERSION;
        SP()->options->update('rank-info', $options);
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated.
function sp_rank_info_do_sp_activate() {
}

# permissions reset
function sp_rank_info_do_reset_permissions() {
}
