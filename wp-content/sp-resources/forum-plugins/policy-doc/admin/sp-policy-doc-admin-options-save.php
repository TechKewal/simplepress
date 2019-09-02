<?php
/*
Simple:Press
Policy Docs Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_policy_doc_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$policy = SP()->options->get('policy-doc');
    $policy['regcheck'] = isset($_POST['regcheck']);
    $policy['regform'] = isset($_POST['regform']);
	if(isset($_POST['regfile'])) $policy['regfile'] = SP()->saveFilters->filename(trim($_POST['regfile']));
	if(isset($_POST['privfile'])) $policy['privfile'] = SP()->saveFilters->filename(trim($_POST['privfile']));

	# Registration text - if set update, otherwise its empty, so remove
	if (isset($_POST['regpolicy']) && $_POST['regpolicy'] != '') {
		SP()->meta->add('registration', 'policy', SP()->saveFilters->text(trim($_POST['regpolicy'])));
	} else {
		$msg = SP()->meta->get('registration', 'policy');
		if (!empty($msg[0])) SP()->meta->delete($msg[0]['meta_id']);
	}

	# Prvacy text - if set update, otherwise its empty, so remove
	if (isset($_POST['privpolicy']) && $_POST['privpolicy'] != '') {
		SP()->meta->add('privacy', 'policy', SP()->saveFilters->text(trim($_POST['privpolicy'])));
	} else {
		$msg = SP()->meta->get('privacy', 'policy');
		if (!empty($msg[0])) SP()->meta->delete($msg[0]['meta_id']);
	}
	SP()->options->update('policy-doc', $policy);

	return __('Policy documents options updated!', 'sp-policy');
}
