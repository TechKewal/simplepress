<?php
/*
Simple:Press
Report Post plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_report_post_do_install() {
	$option = SP()->options->get('report-post');
	if (empty($option)) {
		$option['email-list'] = get_option('admin_email');
		$option['dbversion'] = SPRPDBVERSION;
		SP()->options->add('report-post', $option);
    }

    # add a new permission into the auths table
	SP()->auths->add('report_posts', __('Can report a post to administrators', 'sp-report'), 1, 0, 0, 0, 1);

    # activation so make our auth active
    SP()->auths->activate('report_posts');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function sp_report_post_do_reset_permissions() {
	SP()->auths->add('report_posts', __('Can report a post to administrators', 'sp-report'), 1, 0, 0, 0, 1);
}
