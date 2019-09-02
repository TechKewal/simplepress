<?php
/*
Simple:Press
Identities plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_identities_do_install() {
	$options = SP()->options->get('identities');
	if (empty($options)) {
		# storage location
		$newpath = SP()->plugin->add_storage('forum-identities', 'identities');

        $options['dbversion'] = SPIDENTDBVERSION;
        SP()->options->update('identities', $options);
    }
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated
function sp_identities_do_sp_activate() {
}
