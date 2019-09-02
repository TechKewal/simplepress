<?php
/*
Simple:Press
Profile Display Control Plugin Support Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profile_display_control_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'profile-display-control/sp-profile-display-control-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-pdc')."'>".__('Uninstall', 'sp-pdc').'</a>';
        $url = SPADMINPROFILE.'&amp;tab=plugin&amp;admin=sp_profile_display_control_admin&amp;save=sp_profile_display_control_update&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-pdc')."'>".__('Options', 'sp-pdc').'</a>';
    }
	return $actionlink;
}

function sp_profile_display_control_do_profile_start() {
	$pdc = SP()->options->get('profile-display-control');
    if ($pdc) {
        foreach ($pdc as $key => $option) {
            if (!$option['display']) add_filter($option['filter'], 'sp_profile_display_control_remove_content', 10, 3);
        }
    }
}

function sp_profile_display_control_do_profile_save() {
	$pdc = SP()->options->get('profile-display-control');
    if ($pdc) {
        foreach ($pdc as $key => $option) {
            if (!$option['display'] && !empty($option['save'])) add_filter($option['save'], 'sp_profile_display_control_remove_save', 10, 3);
        }
    }
}

function sp_profile_display_control_do_add_item($key, $display, $title, $filter, $save) {
	$pdc = SP()->options->get('profile-display-control');
	if (empty($pdc) || isset($pdc[$key])) return;
    $pdc[$key]['display'] = $display;
    $pdc[$key]['title'] = $title;
    $pdc[$key]['filter'] = $filter;
    $pdc[$key]['save'] = $save;
    SP()->options->add('profile-display-control', $pdc);
}

function sp_profile_display_control_do_remove_item($key) {
	$pdc = SP()->options->get('profile-display-control');
    unset($pdc[$key]);
    SP()->options->add('profile-display-control', $pdc);
}
