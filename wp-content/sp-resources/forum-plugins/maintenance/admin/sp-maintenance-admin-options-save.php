<?php
/*
Simple:Press
Maintenance Mode Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_maintenance_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save plugin options
	$data = SP()->options->get('maintenance');

	if (isset($_POST['mmenable'])) { $data['mmenable'] = true; } else { $data['mmenable'] = false; }
    $data['mmmessage'] = SP()->saveFilters->text(trim($_POST['mmmessage']));

	SP()->options->update('maintenance', $data);

	$out = __('Maintenance mode options updated', 'sp-maintenance');
	return $out;
}
