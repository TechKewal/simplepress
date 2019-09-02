<?php
/*
Simple:Press
Buddypress Plugin Admin Options Form
$LastChangedDate: 2018-08-26 16:55:40 -0500 (Sun, 26 Aug 2018) $
$Rev: 15725 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_buddypress_admin_options_form() {
	$bpdata = SP()->options->get('buddypress');

	spa_paint_options_init();
	spa_paint_open_tab(__('BuddyPress Plugin', 'sp-buddypress'));
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Profile Options', 'sp-buddypress'), true, 'buddypress-options');
			     spa_paint_checkbox(__('Integrate key forum profile elements on BuddyPress profile', 'sp-buddypress'), 'integrateprofile', $bpdata['integrateprofile']);
			     if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php')) spa_paint_checkbox(__('Integrate subscriptions management on BuddyPress profile', 'sp-buddypress'), 'integratesubs', $bpdata['integratesubs']);
			     if (SP()->plugin->is_active('watches/sp-watches-plugin.php')) spa_paint_checkbox(__('Integrate watches management on BuddyPress profile', 'sp-buddypress'), 'integratewatches', $bpdata['integratewatches']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Activity Stream', 'sp-buddypress'), true, 'buddypress-activity');
				$values = array(__('Do not add activity for forum posts', 'sp-buddypress'),
                                __('New forum topics only', 'sp-buddypress'),
                                __('All forum posts', 'sp-buddypress'));
				spa_paint_radiogroup(__('Add BuddyPress activity for', 'sp-buddypress'), 'activity', $values, $bpdata['activity'], false, true);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Avatars', 'sp-buddypress'), true, 'buddypress-avatars');
				$values = array(__('Keep avatars separate', 'sp-buddypress'),
                                __('Use BuddyPress avatars for both', 'sp-buddypress'),
                                __('Use Simple:Press avatars for both', 'sp-buddypress'));
				spa_paint_radiogroup(__('BuddyPress / Simple:Press avatar handling', 'sp-buddypress'), 'avatar', $values, $bpdata['avatar'], false, true);
						echo '<div class="sfoptionerror">';
						echo __('Warning:  Do not set BuddyPress avatar handling to use Simple:Press avatars if you have WP Avatars being displayed in the Forum.', 'sp-buddypress');
                        echo ' '.__('You can check this on the Forum - Profiles - Avatar Options admin panel.  In the avatar priorities, if WP Avatars are in use (higher than SP Devault Avatars), then WP avatars will be used.', 'sp-buddypress');
                        echo ' '.__('This could cause an infinite loop of WP/SP/BP trying to repalce avatars and eventually an out of memory error', 'sp-buddypress');
						echo '</div>';
   				spa_paint_checkbox(__('Use BuddyPress full size avatar', 'sp-buddypress'), 'bpavatarsize', $bpdata['bpavatarsize']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

	   spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('WP Admin Bar / BuddyBar Forum Links', 'sp-buddypress'), true, 'buddypress-links');
				spa_paint_checkbox(__('Show forum links in the BuddyPress bars', 'sp-buddypress'), 'uselinks', $bpdata['uselinks']);
                if ($bpdata['uselinks']) {
    				spa_paint_checkbox(__('Add forum new posts link', 'sp-buddypress'), 'newlink', $bpdata['newlink']);
				    if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) spa_paint_checkbox(__('Add forum PM inbox link', 'sp-buddypress'), 'inboxlink', $bpdata['inboxlink']);
    				if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php')) spa_paint_checkbox(__('Add forum subscriptions link', 'sp-buddypress'), 'subslink', $bpdata['subslink']);
    				if (SP()->plugin->is_active('watches/sp-watches-plugin.php')) spa_paint_checkbox(__('Add forum watches link', 'sp-buddypress'), 'watcheslink', $bpdata['watcheslink']);
    				spa_paint_checkbox(__('Add forum profile link', 'sp-buddypress'), 'profilelink', $bpdata['profilelink']);
    				spa_paint_checkbox(__('Add link to topics started', 'sp-buddypress'), 'startedlink', $bpdata['startedlink']);
    				spa_paint_checkbox(__('Add link to topics posted in', 'sp-buddypress'), 'postedlink', $bpdata['postedlink']);
                }
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('WP Admin Bar Notifications', 'sp-buddypress'), true, 'buddypress-notifications');
				spa_paint_checkbox(__('Show forum notifications in the BuddyPress bars', 'sp-buddypress'), 'usenotifications', $bpdata['usenotifications']);
                if ($bpdata['usenotifications']) {
    				spa_paint_checkbox(__('Add forum new posts notifications', 'sp-buddypress'), 'newnotifications', $bpdata['newnotifications']);
				    if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) spa_paint_checkbox(__('Add forum new PM notifications', 'sp-buddypress'), 'inboxnotifications', $bpdata['inboxnotifications']);
    				if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php')) spa_paint_checkbox(__('Add forum subscriptions notifications', 'sp-buddypress'), 'subsnotifications', $bpdata['subsnotifications']);
    				if (SP()->plugin->is_active('watches/sp-watches-plugin.php')) spa_paint_checkbox(__('Add forum watches notifications', 'sp-buddypress'), 'watchesnotifications', $bpdata['watchesnotifications']);
                }
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_close_container();
}
