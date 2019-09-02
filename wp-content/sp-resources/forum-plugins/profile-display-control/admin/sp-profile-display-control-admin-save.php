<?php
/*
Simple:Press
Profile Display Control Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profile_display_control_admin_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$mess = '';

   	$pdc = SP()->options->get('profile-display-control');
    if ($pdc) {
        foreach ($pdc as $key => $option) {
            $pdc[$key]['display'] = (isset($_POST[$key])) ? true : false;
        }
    	SP()->options->add('profile-display-control', $pdc);
    }

	$mess.= '<br />'.__('Profile display controls updated!', 'sp-pdc');
	return $mess;
}
