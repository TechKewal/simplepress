<?php
/*
Simple:Press Plugin Title: HTML Emails
Version: 2.1.0
Item Id: 3925
Plugin URI: https://simple-press.com/downloads/html-emails-plugin/
Description: A Simple:Press plugin for sending Simple:Press emails in HTML
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-11-13 20:42:11 -0600 (Tue, 13 Nov 2018) $
$Rev: 15818 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPHTMLEMAILDBVERSION', 5);

define('SPHTMLEMAILDIR', 		SPPLUGINDIR.'html-email/');
define('SPHTMLEMAILADMINDIR',   SPPLUGINDIR.'html-email/admin/');
define('SPHTMLEMAILLIBDIR', 	SPPLUGINDIR.'html-email/library/');
define('SPHTMLEMAILLIBURL', 	SPPLUGINURL.'html-email/library/');
define('SPHTMLEMAILIMAGES', 	SPPLUGINURL.'html-email/resources/images/');

add_action('init', 										             'sp_html_email_localization');
add_action('sph_activate_html-email/sp-html-email-plugin.php',       'sp_html_email_install');
add_action('sph_deactivate_html-email/sp-html-email-plugin.php',     'sp_html_email_deactivate');
add_action('sph_uninstall_html-email/sp-html-email-plugin.php',      'sp_html_email_uninstall');
add_action('sph_activated', 				                         'sp_html_email_sp_activate');
add_action('sph_deactivated', 				                         'sp_html_email_sp_deactivate');
add_action('sph_uninstalled', 								         'sp_html_email_sp_uninstall');
add_action('sph_plugin_update_html-email/sp-html-email-plugin.php',  'sp_html_email_upgrade_check');
add_action('admin_footer',                                           'sp_html_email_upgrade_check');
add_action('sph_permissions_reset', 						         'sp_html_email_reset_permissions');
add_action('sph_admin_menu', 									     'sp_html_email_menu');
add_action('sph_admin_menu',                                         'sp_html_email_admin_menu');
add_action('sph_add_style',											 'sp_html_email_add_style_icon');
add_action('sph_admin_caps_form', 					     	         'sp_html_email_admin_cap_form', 10, 2);
add_action('sph_admin_caps_list', 						             'sp_html_email_admin_cap_list', 10, 2);

add_filter('sph_plugins_active_buttons',                'sp_html_email_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-plugins', 	            'sp_html_email_admin_help', 10, 3);
add_filter('sph_email_subject',                         'sp_html_email_admin_notification_subject', 10, 2);
add_filter('sph_admin_email',                           'sp_html_email_admin_notification_body', 10, 3);
add_filter('sph_admin_new_user_email_subject',          'sp_html_email_new_user_admin_subject', 10, 2);
add_filter('sph_admin_new_user_email_msg',              'sp_html_email_new_user_admin_body', 10, 2);
add_filter('sph_user_new_user_email_subject',           'sp_html_email_new_user_subject', 10, 2);
add_filter('sph_user_new_user_email_msg',               'sp_html_email_new_user_body', 10, 2);
add_filter('retrieve_password_title',                   'sp_html_email_pw_change_subject');
add_filter('retrieve_password_message',                 'sp_html_email_pw_change_body', 10, 2);
add_filter('sph_mentions_email_subject',                'sp_html_email_mentions_subject', 10, 4);
add_filter('sph_mentions_email_msg',                    'sp_html_email_mentions_body', 10, 4);
add_filter('sph_report_post_email_subject',             'sp_html_email_report_subject', 10, 3);
add_filter('sph_report_post_email_msg',                 'sp_html_email_report_body', 10, 4);
add_filter('sph_pm_email_subject',                      'sp_html_email_newpm_subject', 10, 4);
add_filter('sph_pm_email_notification',                 'sp_html_email_newpm_body', 10, 5);
add_filter('sph_subscriptions_email_subject',           'sp_html_email_subs_subject', 10, 3);
add_filter('sph_subscriptions_notification_email',      'sp_html_email_subs_body', 10, 3);
add_filter('sph_subscriptions_digest_email_subject',    'sp_html_email_digests_subject', 10, 3);
add_filter('sph_subscriptions_digest_entry',            'sp_html_email_digests_entry', 10, 4);
add_filter('sph_subscriptions_digest_email',            'sp_html_email_digests_body', 10, 3);
add_filter('sph_subscriptions_digest_header',           '__return_empty_string');
add_filter('sph_subscriptions_digest_footer',           '__return_empty_string');
add_filter('sph_admin_caps_new', 			            'sp_html_email_admin_caps_new', 10, 2);
add_filter('sph_admin_caps_update',                     'sp_html_email_admin_caps_update', 10, 3);
add_filter('sph_ShowAdminLinks', 		                'sp_html_email_admin_links', 10, 2);

function sp_html_email_localization() {
	sp_plugin_localisation('sp-html-email');
}

function sp_html_email_install() {
    require_once SPHTMLEMAILDIR.'sp-html-email-install.php';
    sp_html_email_do_install();
}

function sp_html_email_deactivate() {
    require_once SPHTMLEMAILDIR.'sp-html-email-uninstall.php';
    sp_html_email_do_deactivate();
}

function sp_html_email_uninstall() {
    require_once SPHTMLEMAILDIR.'sp-html-email-uninstall.php';
    sp_html_email_do_uninstall();
}

function sp_html_email_sp_activate() {
	require_once SPHTMLEMAILDIR.'sp-html-email-install.php';
    sp_html_email_do_sp_activate();
}

function sp_html_email_sp_deactivate() {
	require_once SPHTMLEMAILDIR.'sp-html-email-uninstall.php';
    sp_html_email_do_sp_deactivate();
}

function sp_html_email_sp_uninstall() {
	require_once SPHTMLEMAILDIR.'sp-html-email-uninstall.php';
    sp_html_email_do_sp_uninstall();
}

function sp_html_email_upgrade_check() {
    require_once SPHTMLEMAILDIR.'sp-html-email-upgrade.php';
    sp_html_email_do_upgrade_check();
}

function sp_html_email_uninstall_option($actionlink, $plugin) {
    require_once SPHTMLEMAILDIR.'sp-html-email-uninstall.php';
    $actionlink = sp_html_email_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_html_email_reset_permissions() {
    require_once SPHTMLEMAILDIR.'sp-html-email-install.php';
    sp_html_email_do_reset_permissions();
}

function sp_html_email_admin_menu($parent) {
    if (!SP()->auths->current_user_can('SPF Manage Emails')) return;
	add_submenu_page($parent, esc_attr(__('HTML Emails', 'sp-html-email')), esc_attr(__('HTML Emails', 'sp-html-email')), 'read', SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_global_settings&save=sp_html_email_admin_save_global&form=1&panel='.urlencode(__('HTML Emails', 'sp-html-email')), 'dummy');
}

function sp_html_email_add_style_icon() {
	echo('.spaicon-HTMLEmails:before {content: "\e113";}');
}

function sp_html_email_menu() {
	$option = SP()->options->get('html-email');
	$panels = array(
                __('HTML Global Settings', 'sp-html-email') => array('admin' => 'sp_html_email_global_settings', 'save' => 'sp_html_email_admin_save_global', 'form' => 1, 'id' => 'htmlglobals'),
                __('Admin New Posts', 'sp-html-email') => array('admin' => 'sp_html_email_admin_posts', 'save' => 'sp_html_email_admin_save_posts', 'form' => 1, 'id' => 'htmladminposts'),
                __('New Users', 'sp-html-email') => array('admin' => 'sp_html_email_admin_new_user', 'save' => 'sp_html_email_admin_save_new_user', 'form' => 1, 'id' => 'htmlnewuser'),
                __('PW Changed', 'sp-html-email') => array('admin' => 'sp_html_email_admin_pw_change', 'save' => 'sp_html_email_admin_save_pw_change', 'form' => 1, 'id' => 'htmlpwchange')
				);
    if (SP()->plugin->is_active('mentions/sp-mentions-plugin.php')) {
        $panels[__('Mentions', 'sp-html-email')] = array('admin' => 'sp_html_email_admin_mentions', 'save' => 'sp_html_email_admin_save_mentions', 'form' => 1, 'id' => 'htmlmentions');
    }
    if (SP()->plugin->is_active('report-post/sp-report-post-plugin.php')) {
        $panels[__('Report Post', 'sp-html-email')] = array('admin' => 'sp_html_email_admin_report', 'save' => 'sp_html_email_admin_save_report', 'form' => 1, 'id' => 'htmlreport');
    }
    if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) {
        $panels[__('New PM', 'sp-html-email')] = array('admin' => 'sp_html_email_admin_newpm', 'save' => 'sp_html_email_admin_save_newpm', 'form' => 1, 'id' => 'htmlnewpm');
    }
    if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php')) {
        $panels[__('Subscriptions', 'sp-html-email')] = array('admin' => 'sp_html_email_admin_subs', 'save' => 'sp_html_email_admin_save_subs', 'form' => 1, 'id' => 'htmlsubs');
    	$subs = SP()->options->get('subscriptions');
        if ($subs['digestsub']) $panels[__('Digests', 'sp-html-email')] = array('admin' => 'sp_html_email_admin_digests', 'save' => 'sp_html_email_admin_save_digests', 'form' => 1, 'id' => 'htmldigests');
    }

    SP()->plugin->add_admin_panel(__('HTML Emails', 'sp-html-email'), 'SPF Manage Emails', __('Options for HTML Emails', 'sp-html-email'), 'icon-HTMLEmails', $panels, 7);
}

function sp_html_email_admin_links($out, $br) {
	if (SP()->auths->current_user_can('SPF Manage Emails')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_global_settings&save=sp_html_email_admin_save_global&form=1').'">';
		$out.= SP()->theme->paint_icon('spIcon', SPHTMLEMAILIMAGES, "sp_ManageEmails.png").$br;
		$out.= __('HTML Emails', 'sp-html-email').'</a></p>';
		$out.= sp_close_grid_cell();
	}
    return $out;
}

function sp_html_email_global_settings() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-global-settings.php';
	sp_html_email_admin_global_form();
}

function sp_html_email_admin_save_global() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-global-save.php';
    return sp_html_email_admin_global_save();
}

function sp_html_email_admin_posts() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-posts.php';
	sp_html_email_admin_posts_form();
}

function sp_html_email_admin_save_posts() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-posts-save.php';
    return sp_html_email_admin_posts_save();
}

function sp_html_email_admin_new_user() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-new-user.php';
	sp_html_email_admin_new_user_form();
}

function sp_html_email_admin_save_new_user() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-new-user-save.php';
    return sp_html_email_admin_new_user_save();
}

function sp_html_email_admin_pw_change() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-pw-change.php';
	sp_html_email_do_admin_pw_change();
}

function sp_html_email_admin_save_pw_change() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-pw-change-save.php';
    return sp_html_email_do_admin_save_pw_change();
}

function sp_html_email_admin_mentions() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-mentions.php';
	sp_html_email_do_admin_mentions();
}

function sp_html_email_admin_save_mentions() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-mentions-save.php';
    return sp_html_email_do_admin_save_mentions();
}

function sp_html_email_admin_report() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-report.php';
	sp_html_email_do_admin_report();
}

function sp_html_email_admin_save_report() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-report-save.php';
    return sp_html_email_do_admin_save_report();
}

function sp_html_email_admin_newpm() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-pm.php';
	sp_html_email_do_admin_newpm();
}

function sp_html_email_admin_save_newpm() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-pm-save.php';
    return sp_html_email_do_admin_save_newpm();
}

function sp_html_email_admin_subs() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-subs.php';
	sp_html_email_do_admin_subs();
}

function sp_html_email_admin_save_subs() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-subs-save.php';
    return sp_html_email_do_admin_save_subs();
}

function sp_html_email_admin_digests() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-digests.php';
	sp_html_email_do_admin_digests();
}

function sp_html_email_admin_save_digests() {
    require_once SPHTMLEMAILADMINDIR.'sp-html-email-admin-digests-save.php';
    return sp_html_email_do_admin_save_digests();
}

function sp_html_email_admin_help($file, $tag, $lang) {
    if ($tag == '[html-email-css]' || $tag == '[html-email-header]' || $tag == '[html-email-footer]' ||
    	$tag == '[html-email-admin-notification]' || $tag == '[html-email-admin-notifications]' ||
        $tag == '[html-email-new-user]' || $tag == '[html-email-new-users]' || $tag == '[html-email-new-users-admin]' ||
        $tag == '[html-email-pw-change]' || $tag == '[html-email-pw-changes]' || $tag == '[html-email-pw-changes-admin]' ||
        $tag == '[html-email-mention]' || $tag == '[html-email-mentions]' ||
        $tag == '[html-email-report]' || $tag == '[html-email-reports]' ||
        $tag == '[html-email-newpm]' || $tag == '[html-email-newpms]' ||
        $tag == '[html-email-sub]' || $tag == '[html-email-subs]' ||
        $tag == '[html-email-digest]' || $tag == '[html-email-digests]') {
        $file = SPHTMLEMAILADMINDIR.'sp-html-email-admin-help.'.$lang;
    }
    return $file;
}

function sp_html_before_email() {
    add_filter('wp_mail_content_type', 'sp_html_email_content_type');
    add_action('sph_email_send_after', 'sp_html_after_email');
}

function sp_html_email_content_type() {
    return 'text/html';
}

function sp_html_after_email() {
    remove_filter('wp_mail_content_type', 'sp_html_email_content_type');
}

function sp_html_email_admin_notification_subject($subject, $newpost) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_admin_notification_subject($subject, $newpost);
    return $subject;
}

function sp_html_email_admin_notification_body($msg, $newpost, $id) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_admin_notification_body($msg, $newpost, $id);
    return $msg;
}

function sp_html_email_new_user_admin_subject($subject, $id) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_new_user_admin_subject($subject, $id);
    return $subject;
}

function sp_html_email_new_user_admin_body($msg, $id) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_new_user_admin_body($msg, $id);
    return $msg;
}

function sp_html_email_new_user_subject($subject, $id) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_new_user_subject($subject, $id);
    return $subject;
}

function sp_html_email_new_user_body($msg, $id) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_new_user_body($msg, $id);
    return $msg;
}

function sp_html_email_pw_change_subject($subject) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_pw_change_subject($subject);
    return $subject;
}

function sp_html_email_pw_change_body($msg, $key) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_pw_change_body($msg, $key);
    return $msg;
}

function sp_html_email_mentions_subject($subject, $newpost, $id, $who) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_mentions_subject($subject, $newpost, $id, $who);
    return $subject;
}

function sp_html_email_mentions_body($msg, $newpost, $id, $who) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_mentions_body($msg, $newpost, $id, $who);
    return $msg;
}

function sp_html_email_report_subject($subject, $postid, $reporter) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_report_subject($subject, $postid, $reporter);
    return $subject;
}

function sp_html_email_report_body($msg, $postid, $report, $reporter) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_report_body($msg, $postid, $report, $reporter);
    return $msg;
}

function sp_html_email_newpm_subject($subject, $email, $sender, $newpm) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_newpm_subject($subject, $email, $sender, $newpm);
    return $subject;
}

function sp_html_email_newpm_body($msg, $email, $title, $sender, $newpm) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_newpm_body($msg, $email, $title, $sender, $newpm);
    return $msg;
}

function sp_html_email_subs_subject($subject, $newpost, $user) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_subs_subject($subject, $newpost, $user);
    return $subject;
}

function sp_html_email_subs_body($msg, $newpost, $user) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_subs_body($msg, $newpost, $user);
    return $msg;
}

function sp_html_email_digests_subject($subject, $userid, $topic) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $subject = sp_html_email_do_digests_subject($subject, $userid, $topic);
    return $subject;
}

function sp_html_email_digests_entry($msg, $topic, $userid, $count) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_digests_entry($msg, $topic, $userid, $count);
    return $msg;
}

function sp_html_email_digests_body($msg, $userid, $topic) {
    require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
    $msg = sp_html_email_do_digests_body($msg, $userid, $topic);
    return $msg;
}

function sp_html_email_admin_cap_form($user) {
	require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
	sp_html_email_do_admin_cap_form($user);
}

function sp_html_email_admin_cap_list($user) {
	require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
	sp_html_email_do_admin_cap_list($user);
}

function sp_html_email_admin_caps_new($newadmin, $user) {
	require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
	$newadmin = sp_html_email_do_admin_caps_new($newadmin, $user);
	return $newadmin;
}

function sp_html_email_admin_caps_update($still_admin, $remove_admin, $user) {
	require_once SPHTMLEMAILLIBDIR.'sp-html-email-components.php';
	$still_admin = sp_html_email_do_admin_caps_update($still_admin, $remove_admin, $user);
	return $still_admin;
}

# -----------------------------------------------------------------
# handle password changed email to admin here since we have to overload a pluggable function
$option = SP()->options->get('html-email');
if ($option['pw-change'] && !function_exists('wp_password_change_notification')) :
    function wp_password_change_notification($user) {
    	# send a copy of password change notification to the admin
    	# but check to see if it's the admin whose password we're changing, and skip this
    	if (0 !== strcasecmp($user->user_email, get_option('admin_email'))) {
            $option = SP()->options->get('html-email');

        	$subject = SP()->displayFilters->title($option['pw-change-admin-subject']);
    		$subject = str_replace('%USERNAME%', $user->user_login, $subject);
    		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);

        	$body = SP()->displayFilters->text($option['pw-change-admin-body']);
    		$body = str_replace('%USERNAME%', $user->user_login, $body);
    		$body = str_replace('%USEREMAIL%', $user->user_email, $body);
    		$body = str_replace('%USERIP%', sp_get_ip(), $body);
    		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);

            # tie into wp hook for email type to change to html (function will then remove this hook)
            add_filter('wp_mail_content_type', 'sp_html_email_content_type');

    		wp_mail(get_option('admin_email'), $subject, $body);
    	}
    }
endif;
