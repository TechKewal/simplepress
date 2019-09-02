<?php
/*
Simple:Press
User Selection Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_user_selection_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save plugin options
    $data = SP()->options->get('user-selection');

	if (isset($_POST['usedefault'])) { $data['usedefault'] = true; } else { $data['usedefault'] = false; }

    if (!empty($_POST['names'])) {
        $data['names'] = array();
        foreach ($_POST['names'] as $file => $name) {
            $data['names'][$file] = SP()->saveFilters->title($name);
        }
    }

	SP()->options->update('user-selection', $data);

	$out = __('User languages updated', 'sp-usel');
	return $out;
}
