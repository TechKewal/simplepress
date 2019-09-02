<?php
/*
Simple:Press
Policy Docs plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the report post plugin uninstall only
function sp_policy_doc_do_uninstall() {
	# remove our storage locations
	SP()->plugin->remove_storage('policies');

    # delete our option table
    SP()->options->delete('policy-doc');

    # remove meta
	$msg = SP()->meta->get('registration', 'policy');
	if (!empty($msg)) SP()->meta->delete($msg[0]['meta_id']);
	$msg = SP()->meta->get('privacy', 'policy');
	if (!empty($msg)) SP()->meta->delete($msg[0]['meta_id']);

	# remove glossary entries
	sp_remove_glossary_plugin('sp-policies');

    # make sure permalink include pm stuff
    SP()->spPermalinks->update_permalink(true);
}

function sp_policy_doc_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-policies');
}

function sp_policy_doc_do_sp_uninstall() {
}
