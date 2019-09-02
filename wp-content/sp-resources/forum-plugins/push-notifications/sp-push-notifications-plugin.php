<?php
/*
Simple:Press Plugin Title: Push Notifications
Version: 1.0.0
Item Id: 79613
Plugin URI: https://simple-press.com/push-notifications
Description: A Simple:Press plugin for pushing subscription alerts to Pushover, Pushbullet and Web-browsers (via onesignal.com)
Author: Simple:Press
Original Author: Dima Kurtash
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A plugin for Simple: Press to users to subscribe to topics and forum.
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION

define('SPPNSUBSDBVERSION', 12);
define('SPACTIVITY_SUBS_PUSHOVER_TOPIC', 											SP()->activity->get_type('topic pushover notifications'));
define('SPACTIVITY_SUBS_PUSHOVER_FORUM', 											SP()->activity->get_type('forum pushover notifications'));
define('SPACTIVITY_SUBS_PUSHBULLET_TOPIC', 											SP()->activity->get_type('topic pushbullet notifications'));
define('SPACTIVITY_SUBS_PUSHBULLET_FORUM', 											SP()->activity->get_type('forum pushbullet notifications'));
define('SPACTIVITY_SUBS_ONESIGNAL_TOPIC',											SP()->activity->get_type('topic onesignal notifications'));
define('SPACTIVITY_SUBS_ONESIGNAL_FORUM', 											SP()->activity->get_type('forum onesignal notifications'));
define('SPPNDIR',																	SPPLUGINDIR.'push-notifications/');
define('SPPNADMINDIR',																SPPLUGINDIR.'push-notifications/admin/');
define('SPPNLIBDIR',																SPPLUGINDIR.'push-notifications/library/');
define('SPPNAJAXDIR',																SPPLUGINDIR.'push-notifications/ajax/');
define('SPPNSCRIPT',																SPPLUGINURL.'push-notifications/resources/jscript/');
define('SPPNCSS',																	SPPLUGINURL.'push-notifications/resources/css/');
define('SPPNTAGSDIR',																SPPLUGINDIR.'push-notifications/template-tags/');
define('SPPNTEMPDIR',																SPPLUGINDIR.'push-notifications/template-files/');
define('SPPNFORMSDIR',																SPPLUGINDIR.'push-notifications/forms/');
define('SPPNIMAGES',																SPPLUGINURL.'push-notifications/resources/images/');
define('SPPNIMAGESMOB',																SPPLUGINURL.'push-notifications/resources/images/mobile/');
define('SPPNSITEPROTOCOL', 															site_protocol() );

# Actions
add_action('sph_activate_push-notifications/sp-push-notifications-plugin.php',		'sp_push_notifications_install');
add_action('sph_deactivate_push-notifications/sp-push-notifications-plugin.php',	'sp_push_notifications_deactivate');
add_action('sph_uninstall_push-notifications/sp-push-notifications-plugin.php',		'sp_push_notifications_uninstall');

add_action('init',																	'sp_push_notifications_localization');
add_action('sph_print_plugin_scripts',												'sp_push_notifications_load_js');
add_action('sph_scripts_admin_end',													'sp_push_notifications_load_admin_js');
add_action('sph_admin_menu',														'sp_push_notifications_menu');
add_action('sph_setup_forum',														'sp_push_notifications_process_actions');
add_action('sph_new_forum_post',													'sp_push_notifications_new_forum_post', 1);
add_action('sph_print_plugin_styles',												'sp_push_notifications_header');
add_action('sph_toolbox_housekeeping_profile_tabs',									'sp_push_notifications_reset_profile_tabs');

# Filters
add_filter('sph_admin_help-admin-components',										'sp_push_notifications_admin_help', 10, 3);
add_filter('sph_admin_help-admin-users',											'sp_push_notifications_admin_help', 10, 3);
add_filter('sph_new_post_notifications',											'sp_push_notifications_post_notification', 10, 2);
add_filter('sph_topic_options_add',													'sp_push_notifications_topic_form_options', 10, 2);
add_filter('sph_post_options_add',													'sp_push_notifications_post_form_options', 10, 2);
add_filter('user_contactmethods', 													'sp_pushover_notification_contact_item', 10, 1 );
add_filter('user_contactmethods', 													'sp_pushbullet_notification_contact_item', 10, 1 );
add_filter('user_contactmethods', 													'sp_onesignal_notification_contact_item', 10, 1 );
add_filter('sph_plugins_active_buttons',											'sp_push_notifications_uninstall_option', 10, 2);

# Ajax Handlers
add_action('wp_ajax_subs-pushover',													'sp_push_notifications_ajax_pushover');
add_action('wp_ajax_subs-pushbullet',												'sp_push_notifications_ajax_pushbullet');
add_action('wp_ajax_subs-onesignal',												'sp_push_notifications_ajax_onesignal');
add_action('wp_enqueue_scripts', 													'sp_push_notifications_register_onesignal');
add_action('wp_ajax_update_keys',													'sp_push_notifications_ajax_keys');
add_action('wp_ajax_nopriv_update_keys',											'sp_push_notifications_ajax_keys');

function sp_push_notifications_uninstall_option($actionlink, $plugin) {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	$actionlink = sp_push_notifications_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

/**
 * Add Push Notification Tab to admin components menu
 * 
 * @since 1.0
 * 
 * Action Hook: sph_admin_menu
 * 
 * @param null
 * 
 * @return void
 */
function sp_push_notifications_menu() {
	$subpanels = array(
		__('Push Notifications', 'push-notifications') => array(
				'admin' => 'sp_push_notifications_admin_members', 
				'save' => 'sp_push_notifications_admin_save_members', 
				'form' => 1, 
				'id' => 'push-notifications'
			)
		);
	SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function site_protocol(){
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' )
				 ? "https" : "http";

	if(isset($_SERVER['SERVER_PORT']) && $protocol != "https")
		if($_SERVER['SERVER_PORT'] == 443)
			$protocol = 'https';

    return $protocol;
}

function sp_push_notifications_admin_members() {
	require_once SPPNADMINDIR.'sp-push-notifications-admin-members.php';
	sp_push_notifications_admin_members_form();
}

function sp_push_notifications_admin_save_members() {
	require_once SPPNADMINDIR.'sp-push-notifications-admin-members-save.php';
	return sp_push_notifications_admin_members_save();
}

function sp_push_notifications_admin_help($file, $tag, $lang) {
	if ($tag == '[push-notifications-options]') $file = SPPNADMINDIR.'sp-push-notifications-admin-help.'.$lang;
	return $file;
}

function sp_push_notifications_header() {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	sp_push_notifications_do_header();
}

function sp_push_notifications_localization() {
	sp_plugin_localisation('sp-pushnotifications');
}

function sp_push_notifications_uninstall() {
	require_once SPPNDIR.'sp-push-notifications-uninstall.php';
	sp_push_notifications_do_uninstall();
}

function sp_push_notifications_install() {
	require_once SPPNDIR.'sp-push-notification-install.php';
	sp_push_notifications_do_install();
}

function sp_push_notifications_deactivate() {
	require_once SPPNDIR.'sp-push-notifications-uninstall.php';
	sp_push_notifications_do_deactivate();
}

function sp_push_notifications_load_admin_js($footer) {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	sp_push_notificarions_do_load_admin_js($footer);
}

function sp_push_notifications_load_js($footer) {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	sp_push_notifications_do_load_js($footer);
}

function sp_push_notifications_ajax_pushover() {

	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	require_once SPPNAJAXDIR.'sp-push-notifications-ajax-pushover.php';

}

function sp_push_notifications_ajax_pushbullet() {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	require_once SPPNAJAXDIR.'sp-push-notifications-ajax-pushbullet.php';
}

function sp_push_notifications_ajax_onesignal() {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	require_once SPPNAJAXDIR.'sp-push-notifications-ajax-onesignal.php';
}

function sp_push_notifications_ajax_keys() {
	require_once SPPNAJAXDIR.'sp-push-notifications-ajax-keys.php';
}

function sp_push_notifications_process_actions() {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	sp_push_notifications_do_process_actions();
}

function sp_push_notifications_post_notification($retmsg, $newpost) {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	$msg = sp_push_notifications_do_post_notification($retmsg, $newpost);
	return $msg;
}


/**
 * Subcscibe user to notification about new topic comment
 * 
 * @since 1.0
 * 
 * Action Hook: sph_new_forum_post
 * 
 * @param null
 * 
 * @return void
 */

function sp_push_notifications_new_forum_post($newpost) {

	require_once SPPNLIBDIR.'sp-push-notifications-database.php';
	
	$topic = SP()->filters->integer($_POST['topicid']);
	
	if (isset($_POST['pushover_topicsub'])) {
		$newpost['pushover_topicsub'] =  SP()->filters->str($_POST['pushover_topicsub']);
		sp_push_notifications_save_subscription($topic, SP()->user->thisUser->ID, false, 'pushover', SPACTIVITY_SUBS_PUSHOVER_TOPIC);
	}
	if (isset($_POST['pushover_topicsubend'])){
		$newpost['pushover_topicsubend'] =  SP()->filters->str($_POST['pushover_topicsubend']);
		sp_push_notifications_remove_subscription($topic, SP()->user->thisUser->ID, false, 'pushover', SPACTIVITY_SUBS_PUSHOVER_TOPIC);
	}	
	if (isset($_POST['pushbullet_topicsub'])){
		$newpost['pushbullet_topicsub'] =  SP()->filters->str($_POST['pushbullet_topicsub']);
		sp_push_notifications_save_subscription($topic, SP()->user->thisUser->ID, false, 'pushbullet', SPACTIVITY_SUBS_PUSHBULLET_TOPIC);
	}
	if (isset($_POST['pushbullet_topicsubend'])){
		$newpost['pushbullet_topicsubend'] =  SP()->filters->str($_POST['pushbullet_topicsubend']);
		sp_push_notifications_remove_subscription($topic, SP()->user->thisUser->ID, false, 'pushbullet', SPACTIVITY_SUBS_PUSHBULLET_TOPIC);
	}
	if (isset($_POST['onesignal_topicsub'])){
		$newpost['onesignal_topicsub'] =  SP()->filters->str($_POST['onesignal_topicsub']);
		sp_push_notifications_save_subscription($topic, SP()->user->thisUser->ID, false, 'onesignal', SPACTIVITY_SUBS_ONESIGNAL_TOPIC);
	}
	if (isset($_POST['onesignal_topicsubend'])){
		$newpost['onesignal_topicsubend'] =  SP()->filters->str($_POST['onesignal_topicsubend']);
		sp_push_notifications_remove_subscription($topic, SP()->user->thisUser->ID, false, 'onesignal', SPACTIVITY_SUBS_ONESIGNAL_TOPIC);
	}

	return $newpost;

}

function sp_push_notifications_topic_form_options($content, $thisForum) {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	$content = sp_push_notifications_do_topic_form_options($content, $thisForum);
	return $content;
}

function sp_push_notifications_post_form_options($content, $thisTopic) {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	$content = sp_push_notifications_do_post_form_options($content, $thisTopic);
	return $content;
}

function sp_push_notifications_reset_profile_tabs() {
	require_once SPPNLIBDIR.'sp-push-notifications-components.php';
	sp_push_notifications_do_reset_profile_tabs();
}


/**
 * Add new meta field for Pushover API key
 * 
 * @since 1.0
 * 
 * Action Hook: user_contactmethods
 * 
 * @param array $contact_methods the array of meta fields
 * 
 * @return array $contact_methods the array of meta fields
 */

function sp_pushover_notification_contact_item($contact_methods) {
	$contact_methods['pushover_key'] = 'Pushover User Key';
	return $contact_methods;
}

/**
 * Add new meta field for Pushbullet API key
 * 
 * @since 1.0
 * 
 * Action Hook: user_contactmethods
 * 
 * @param array $contact_methods the array of meta fields
 * 
 * @return array $contact_methods the array of meta fields
 */
function sp_pushbullet_notification_contact_item($contact_methods) {
	$contact_methods['pushbullet_key'] = 'Pushbullet User Key';
	return $contact_methods;
}

/**
 * Add new meta field for Onesignal API key
 * 
 * @since 1.0
 * 
 * Action Hook: user_contactmethods
 * 
 * @param array $contact_methods the array of meta fields
 * 
 * @return array $contact_methods the array of meta fields
 */
function sp_onesignal_notification_contact_item($contact_methods) {
	$contact_methods['onesignal_key'] = 'Onesignal User Key';
	return $contact_methods;
}

function sp_PushNotificationsSubscribeButton($args='', $subscribeLabel='', $unsubscribeLabel='', $subscribeToolTip='', $unsubscribeToolTip='', $services='', $activeType='') {
	require_once SPPNTAGSDIR.'sp-push-notifications-subscribe-button-tag.php';
	sp_PushNotificationsSubscribeButtonTag($args, $subscribeLabel, $unsubscribeLabel, $subscribeToolTip, $unsubscribeToolTip, $services, $activeType);
}

function sp_PushNotificationsSubscribeForumButton($args='', $subscribeLabel='', $unsubscribeLabel='', $subscribeToolTip='', $unsubscribeToolTip='', $activityType='', $forumType='') {
	require_once SPPNTAGSDIR.'sp-push-notifications-subscribe-forum-button-tag.php';
	sp_PushNotificationsSubscribeForumButtonTag($args, $subscribeLabel, $unsubscribeLabel, $subscribeToolTip, $unsubscribeToolTip, $activityType, $forumType);
}

function sp_ForumIndexPushNotificationsIcon($args='', $subToolTip='', $unSubToolTip='') {
	require_once SPPNTAGSDIR.'sp-push-notifications-forum-index-icon-tag.php';
	sp_ForumIndexSubscriptionIconTag($args, $subToolTip, $unSubToolTip);
}


/**
 * Register scripts for onesignal if user have permission for this service
 * 
 * @since 1.0
 * 
 * Action Hook: wp_enqueue_scripts
 * 
 * @param null
 * 
 * @return void
 */
function sp_push_notifications_register_onesignal() {

	$nots = SP()->options->get('push-notifications');
	
	if(
		SP()->auths->get('onesignal', '', get_current_user_id()) &&
		$nots['onesignal'] != ''
		){
	?>

	<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async='async'></script>
	<script>
		var OneSignal = window.OneSignal || [];
		OneSignal.push(["init", {
			appId: "<?php echo $nots['onesignal'] ?>",
			autoRegister: true, /* Set to true to automatically prompt visitors */
			notifyButton: {
				enable: false /* Set to false to hide */
			}
		}]);
	</script>

<?php }

}