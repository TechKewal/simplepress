<?php
/*
Simple:Press
Policy Docs plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_policy_doc_do_install() {
	$policy = SP()->options->get('policy-doc');
	if (empty($policy)) {
		$policy = array();
		$oldpolicy = SP()->options->get('sfpolicy');
		if (empty($oldpolicy)) {
    		# storage location
			$newpath = SP()->plugin->add_storage('forum-policies', 'policies');

			# options
            $policy['dbversion'] = SPPDDBVERSION;

			$policy['regcheck'] = false;
			$policy['regform'] = true;
			$policy['regfile'] = '';
			$policy['privfile'] = '';
		} else {
			# upgrade from core
			$policy = array();
			$policy['regcheck'] = $oldpolicy['sfregcheck'];
			$policy['regform'] = $oldpolicy['sfregtext'];
			$policy['regfile'] = $oldpolicy['sfregfile'];
			$policy['privfile'] = $oldpolicy['sfprivfile'];

            $policy['dbversion'] = SPPDDBVERSION;
			SP()->options->delete('sfpolicy');
		}

		SP()->options->add('policy-doc', $policy);
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
