<?php
/*
Simple:Press Plugin Title: File Uploader with Plupload
Version: 5.1.0
Item Id: 3905
Plugin URI: https://simple-press.com/downloads/file-uploader-plugin/
Description: A Simple:Press plugin for uploading images, media and files in posts
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-11-04 10:24:32 -0600 (Sun, 04 Nov 2018) $
$Rev: 15804 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPPLUPDBVERSION', 9);

define('SPPOSTATTACHMENTS', SP_PREFIX.'sfpostattachments');

define('SPPLUPDIR', 		SPPLUGINDIR.'plupload/');
define('SPPLUPADMINDIR', 	SPPLUGINDIR.'plupload/admin/');
define('SPPLUPLIBDIR', 		SPPLUGINDIR.'plupload/library/');
define('SPPLUPLIBURL', 		SPPLUGINURL.'plupload/library/');
define('SPPLUPAJAXDIR', 	SPPLUGINDIR.'plupload/ajax/');
define('SPPLUPFORMSDIR', 	SPPLUGINDIR.'plupload/forms/');
define('SPPLUPCSS', 		SPPLUGINURL.'plupload/resources/css/');
define('SPPLUPSCRIPT', 		SPPLUGINURL.'plupload/resources/jscript/');
define('SPPLUPI18NDIR',     SPPLUGINDIR.'plupload/resources/jscript/i18n/');
define('SPPLUPI18NURL',     SPPLUGINURL.'plupload/resources/jscript/i18n/');
define('SPPLUPIMAGES',		SPPLUGINURL.'plupload/resources/images/');
define('SPPLUPIMAGESMOB',	SPPLUGINURL.'plupload/resources/images/mobile/');

add_action('init', 												'sp_plupload_localization');
add_action('sph_activate_plupload/sp-plupload-plugin.php', 		'sp_plupload_install');
add_action('sph_deactivate_plupload/sp-plupload-plugin.php', 	'sp_plupload_deactivate');
add_action('sph_uninstall_plupload/sp-plupload-plugin.php', 	'sp_plupload_uninstall');
add_action('sph_activated', 				                    'sp_plupload_sp_activate');
add_action('sph_deactivated', 				                    'sp_plupload_sp_deactivate');
add_action('sph_uninstalled', 								    'sp_plupload_sp_uninstall');
add_action('sph_print_plugin_scripts', 							'sp_plupload_load_js');
add_action('sph_print_plugin_styles',							'sp_plupload_head');
add_action('sph_admin_menu', 									'sp_plupload_menu');
add_action('sph_integration_storage_panel_location', 			'sp_plupload_storage_location');
add_action('sph_integration_storage_save', 						'sp_plupload_storage_save');
add_action('sph_post_create', 									'sp_plupload_post_create');
add_action('sph_post_edit_after_save', 							'sp_plupload_post_create');
add_action('sph_setup_forum', 								    'sp_plupload_process_actions');
add_action('sph_topic_delete', 									'sp_plupload_topic_delete', 10, 2);
add_action('sph_post_delete', 									'sp_plupload_post_delete');
add_action('admin_footer',                                      'sp_plupload_upgrade_check');
add_action('sph_plugin_update_plupload/sp-plupload-plugin.php', 'sp_plupload_upgrade_check');
add_action('sph_admin_panel_header',                            'sp_plupload_gd_check');
add_action('sph_permissions_reset',                             'sp_plupload_reset_permissions');
add_action('sph_toolbox_housekeeping_profile_tabs',             'sp_plupload_reset_profile_tabs');

add_filter('sph_plugins_active_buttons', 	    'sp_plupload_uninstall_option', 10, 2);

if (isset(SP()->core->forumData['display']) && SP()->core->forumData['display']['editor']['toolbar']) {
	add_filter('sph_topic_editor_toolbar_buttons',	'sp_plupload_editor_button', 5, 4);
	add_filter('sph_post_editor_toolbar_buttons',	'sp_plupload_editor_button', 5, 6);
}
add_filter('sph_pm_editor_toolbar_buttons',	'sp_plupload_pm_editor_button', 5, 2);

add_filter('sph_topic_editor_footer_top',	    'sp_plupload_uploader_form', 10, 3);
add_filter('sph_post_editor_footer_top',	    'sp_plupload_uploader_form', 10, 3);
add_filter('sph_post_edit_footer_top',	        'sp_plupload_uploader_edit_form', 10, 4);
add_filter('sph_pm_editor_toolbar',	            'sp_plupload_uploader_pm_form', 10, 2);

add_filter('sph_SignaturesFormBottom',		    'sp_plupload_uploader_sig_form', 10, 2);
add_filter('sph_integration_tooltips', 		    'sp_plupload_tooltip');
add_filter('sph_forumview_combined_data', 	    'sp_plupload_forumview_query', 10, 2);
add_filter('sph_TopicIndexStatusIconsLast',     'sp_plupload_status_icon');
add_filter('sph_topicview_combined_data', 	    'sp_plupload_post_records', 10, 2);
add_filter('sph_PostIndexContent', 			    'sp_plupload_show_attachments');
add_filter('sph_add_post_tool', 	            'sp_plupload_post_tool', 10, 10);
add_filter('sph_acknowledgements',			    'sp_plupload_acknowledgement');
add_filter('sph_admin_help-admin-components',   'sp_plupload_admin_help', 10, 3);
add_filter('sph_perms_tooltips', 		 	    'sp_plupload_perm_tooltip', 10, 2);
add_filter('sph_display_image_data', 		 	'sp_plupload_filter_images', 10, 8);
add_filter('sph_find_attachment',				'sp_plupload_find_attachment');
add_filter('sph_save_images_filter',		    'sp_plupload_post_save', 10, 2);

# do we need the profile photo uploader?
$uploads = SP()->options->get('spPlupload');
if ($uploads['useforphotos']) {
    add_filter('sph_ProfilePhotosLoop',	            'sp_plupload_uploader_photos_form', 10, 2);
    add_filter('sph_profile_save_thisForm',         'sp_plupload_photos_this_form');
    add_filter('sph_ProfileFormSave_photos-upload', 'sp_plupload_photos_upload', 10, 3);
}

# personal provacy data export
add_filter('sp_privacy_profile_section_data', 		'sp_privacy_plupload_listing', 10, 2);

# Ajax Handlers
add_action('wp_ajax_plupload-manage',				'sp_plupload_ajax_manage');
add_action('wp_ajax_nopriv_plupload-manage',		'sp_plupload_ajax_manage');
add_action('wp_ajax_plupload-attachments',			'sp_plupload_ajax_attachments');
add_action('wp_ajax_nopriv_plupload-attachments',	'sp_plupload_ajax_attachments');
add_action('wp_ajax_plupload',						'sp_plupload_upload');
add_action('wp_ajax_nopriv_plupload',				'sp_plupload_upload');
add_action('wp_ajax_plupload-remove',				'sp_plupload_remove');
add_action('wp_ajax_nopriv_plupload-remove',		'sp_plupload_remove');


function sp_plupload_menu() {
    $subpanels = array(
                __('File Uploads', 'sp-plup') => array('admin' => 'sp_plupload_admin_options', 'save' => 'sp_plupload_admin_save_options', 'form' => 1, 'id' => 'plupopt')
                );
    SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_plupload_admin_options() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    require_once SPPLUPADMINDIR.'sp-plupload-admin-options.php';
	sp_plupload_admin_options_form();
}

function sp_plupload_admin_save_options() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    require_once SPPLUPADMINDIR.'sp-plupload-admin-options-save.php';
    return sp_plupload_admin_options_save();
}

function sp_plupload_localization() {
	sp_plugin_localisation('sp-plup');
}

function sp_plupload_admin_help($file, $tag, $lang) {
    if ($tag == '[image-files]' || $tag == '[media-files]' || $tag == '[other-files]' || $tag == '[upload-settings]' || $tag == '[prohibited-files]') $file = SPPLUPADMINDIR.'sp-plupload-admin-help.'.$lang;
    return $file;
}

function sp_plupload_install() {
    require_once SPPLUPDIR.'sp-plupload-install.php';
    sp_plupload_do_install();
}

function sp_plupload_uninstall() {
    require_once SPPLUPDIR.'sp-plupload-uninstall.php';
    sp_plupload_do_uninstall();
}

function sp_plupload_deactivate() {
    require_once SPPLUPDIR.'sp-plupload-uninstall.php';
    sp_plupload_do_deactivate();
}

function sp_plupload_sp_activate() {
	require_once SPPLUPDIR.'sp-plupload-install.php';
    sp_plupload_do_sp_activate();
}

function sp_plupload_sp_deactivate() {
	require_once SPPLUPDIR.'sp-plupload-uninstall.php';
    sp_plupload_do_sp_deactivate();
}

function sp_plupload_sp_uninstall() {
	require_once SPPLUPDIR.'sp-plupload-uninstall.php';
    sp_plupload_do_sp_uninstall();
}

function sp_plupload_uninstall_option($actionlink, $plugin) {
    require_once SPPLUPDIR.'sp-plupload-uninstall.php';
    $actionlink = sp_plupload_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_plupload_upgrade_check() {
    require_once SPPLUPDIR.'sp-plupload-upgrade.php';
    sp_plupload_do_upgrade_check();
}

function sp_plupload_reset_permissions() {
    require_once SPPLUPDIR.'sp-plupload-install.php';
    sp_plupload_do_reset_permissions();
}

function sp_plupload_tooltip($tooltips) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    $tooltips = sp_plupload_do_tooltip($tooltips);
	return $tooltips;
}

function sp_plupload_perm_tooltip($tooltips, $t) {
    $tooltips['manage_attachments'] = $t.__('Can manage their uploaded attachments in their Simple:Press user profile', 'sp-plup');
    $tooltips['download_attachments'] = $t.__('Can download attachments for other file type uploads', 'sp-plup');
    $tooltips['upload_images'] = $t.__('Can upload image files to be used in any topic in the forum', 'sp-plup');
    $tooltips['upload_media'] = $t.__('Can upload other media files to be used in any topic in the forum', 'sp-plup');
    $tooltips['upload_files'] = $t.__('Can uopload other types of file - such as documents - to be used in any topic in the forum', 'sp-plup');
    $tooltips['upload_signatures'] = $t.__('Can upload a signature to be used in their posts made in the forum', 'sp-plup');
	return $tooltips;
}

function sp_plupload_head() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    sp_plupload_do_head();
}

function sp_plupload_load_js($footer) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    sp_plupload_do_load_js($footer);
}

function sp_plupload_forumview_query($forums, $topics) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    $forums = sp_plupload_do_forumview_query($forums, $topics);
	return $forums;
}

function sp_plupload_storage_location() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    sp_plupload_do_storage_location();
}

function sp_plupload_storage_save() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    sp_plupload_do_storage_save();
}

function sp_plupload_upload() {
	require_once SPPLUPLIBDIR.'sp-plupload-upload.php';
}

function sp_plupload_remove() {
	require_once SPPLUPLIBDIR.'sp-plupload-remove.php';
}

function sp_plupload_pm_editor_button($out, $a) {
    if (SP()->auths->get('upload_images') || SP()->auths->get('upload_media') || SP()->auths->get('upload_files')) {
    	if (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) {
			# display mobile icon
			$out.= "<button type='button' style='background:transparent;' class='spIcon spPlupEditorButton' name='spUploadsButton' id='spUploadsButton'>\n";
			$out.= SP()->theme->paint_icon('spIcon', SPPLUPIMAGESMOB, "sp_PlupAttachmentEditor.png", '');
			$out.= "</button>";

		} else {
			$buttonText = apply_filters('sph_editor_toolbar_attachment_button', __('Attachments', 'sp-plup'));
			$out.= "<input type='button' class='spSubmit spLeft spPlupEditorButton' title='".__('Open/Close to Upload Attachments', 'sp-plup')."' id='spUploadsButton' value='".$buttonText."'/>";
		}
	}
    return $out;
}

function sp_plupload_editor_button($out, $forum, $a, $toolbar, $postid='0', $type='post') {
	global $tab;
    if (SP()->auths->get('upload_images', $forum->forum_id) || SP()->auths->get('upload_media', $forum->forum_id) || SP()->auths->get('upload_files', $forum->forum_id)) {
    	if (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) {
			# display mobile icon
			$out.= "<button type='button' tabindex='".$tab++."' style='background:transparent;' class='spIcon spPlupEditorButton' name='spUploadsButton' id='spUploadsButton'>\n";
			$out.= SP()->theme->paint_icon('spIcon', SPPLUPIMAGESMOB, "sp_PlupAttachmentEditor.png", '');
			$out.= "</button>";

		} else {
			$buttonText = apply_filters('sph_editor_toolbar_attachment_button', __('Attachments', 'sp-plup'));
			$out.= "<input type='button' tabindex='".$tab++."' class='spSubmit spLeft spPlupEditorButton' title='".__('Open/Close to Upload Attachments', 'sp-plup')."' id='spUploadsButton' value='".$buttonText."'/>";
            if ($type == 'edit') {
              	$attachments = SP()->DB->table(SPPOSTATTACHMENTS, "post_id=$postid");
                if (!empty($attachments)) $out.= '<input type="button" tabindex="'.$tab++.'" class="spSubmit spLeft spPlupEditorRemoveButton" value="'.__('Remove Attachments', 'sp-plup').'" data-target="sp_uploader_attachments">';
            }
		}
	}
    return $out;
}

function sp_plupload_uploader_form($out, $object, $a) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	require_once SPPLUPLIBDIR.'sp-plupload-forms.php';
	$out = sp_plupload_do_uploader_form($out, $object);
	return $out;
}

function sp_plupload_uploader_edit_form($out, $object, $pid, $a) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	require_once SPPLUPLIBDIR.'sp-plupload-forms.php';
	$out = sp_plupload_do_uploader_form($out, $object, '', $pid, 'edit');
	return $out;
}

function sp_plupload_uploader_pm_form($out, $a) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	require_once SPPLUPLIBDIR.'sp-plupload-forms.php';
	$out = sp_plupload_do_uploader_form($out, '');
	return $out;
}

function sp_plupload_uploader_sig_form($out, $uid) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	require_once SPPLUPLIBDIR.'sp-plupload-forms.php';
	$out = sp_plupload_do_uploader_form($out, '', $uid, '', 'sig');
	return $out;
}

function sp_plupload_uploader_photos_form($out, $uid) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	require_once SPPLUPLIBDIR.'sp-plupload-forms.php';
	$out = sp_plupload_do_uploader_profile_form($uid);
	return $out;
}

function sp_plupload_photos_this_form($thisForm) {
    if ($thisForm == 'edit-photos') $thisForm = 'photos-upload';
    return $thisForm;
}

function sp_plupload_photos_upload($message, $thisUser, $thisForm) {
    if ($thisForm = 'photos-upload') {
	   require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	   $message = sp_plupload_do_uploader_photos_save($message, $thisUser, $thisForm);
    }
	return $message;
}

function sp_plupload_post_create($newpost) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    sp_plupload_do_post_create($newpost);
}

function sp_plupload_status_icon($out) {
	require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	$out = sp_plupload_do_status_icon($out);
	return $out;
}

function sp_plupload_post_records($topics, $post_ids) {
	require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	$topics = sp_plupload_do_post_records($topics, $post_ids);
	return $topics;
}

function sp_plupload_show_attachments($out) {
	require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	$out = sp_plupload_do_show_attachments($out);
	return $out;
}

function sp_plupload_post_tool($out, $post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname, $br) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	$out = sp_plupload_do_post_tool($out, $forum, $topic, $post, $br);
    return $out;
}

function sp_plupload_ajax_manage() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    require_once SPPLUPAJAXDIR.'sp-plupload-ajax-manage.php';
}

function sp_plupload_ajax_attachments() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    require_once SPPLUPAJAXDIR.'sp-plupload-attachments-display.php';
}

function sp_plupload_process_actions() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	sp_plupload_do_process_actions();
}

function sp_plupload_acknowledgement($ack) {
	$ack[] = '<a href="http://plupload.com/">'.__("Image uploader library by Plupload", "sp-plup").'</a>';
	return $ack;
}

function sp_plupload_topic_delete($posts, $topicid) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	sp_plupload_do_topic_delete($posts, $topicid);
}

function sp_plupload_post_delete($post) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	sp_plupload_do_post_delete($post);
}

function sp_plupload_gd_check() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	sp_plupload_do_gd_check();
}

function sp_plupload_reset_profile_tabs() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    sp_plupload_do_reset_profile_tabs();
}

function sp_plupload_filter_images($image_array, $src, $width, $height, $title, $alt, $style, $class) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	$image_array = sp_plupload_do_filter_images($image_array, $src, $width, $height, $title, $alt, $style, $class);
    return $image_array;
}

function sp_plupload_find_attachment($link) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
	return sp_og_find_attachment($link);
}

function sp_plupload_post_save($content, $original) {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';
    return sp_plupload_do_post_save($content, $original);
}

function sp_privacy_plupload_listing($exportItems, $spUserData) {
    require_once SPPLUPLIBDIR.'sp-plupload-export.php';
	return sp_privacy_do_plupload_listing($exportItems, $spUserData);
}
