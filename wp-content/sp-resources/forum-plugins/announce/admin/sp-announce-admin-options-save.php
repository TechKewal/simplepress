<?php
/*
Simple:Press
Announce Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_announce_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save Thanks plugin options
	$data = SP()->options->get('announce');

    $data['showto'] = SP()->filters->integer($_POST['showto']);
	$data['location'] = SP()->filters->integer($_POST['location']);
    $data['message'] = SP()->saveFilters->text(trim($_POST['message']));

	SP()->options->update('announce', $data);

	$out = __('Announce options updated', 'sp-announce');
	return $out;
}
