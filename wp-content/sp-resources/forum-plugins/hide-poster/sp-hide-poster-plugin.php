<?php
/*
Simple:Press Plugin Title: Hide Posters
Version: 2.1.0
Item Id: 3956
Plugin URI: https://simple-press.com/downloads/hide-posters-plugin/
Description: A Simple:Press plugin for allowing you to hide who made posts in a topoic with the Hide Posters option enabled.  Useful for conducting contests where you want to hide identies until after a decision.
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-11-04 10:38:45 -0600 (Sun, 04 Nov 2018) $
$Rev: 15805 $

Please note, this plugin requires a manual modification to your sp theme.  You will want to replace all of the user post info section of your topic view template file with a custom message.
In your SP theme, template file spTopicView.php, look for something like:

		# Column 1 of the post row
		# ----------------------------------------------------------------------
		sp_ColumnStart('tagClass=spUserSection spLeft&width=15%&height=50px');

It will be the column 1 of the post row.  You will want to modify to check for hiding user post info and replace it with custom message.  In the end, should look something like:

		# Column 1 of the post row
		# ----------------------------------------------------------------------
		sp_ColumnStart('tagClass=spUserSection spLeft&width=15%&height=50px');
            if (SP()->forum->view->thisTopic->forum_hide_posters && SP()->forum->view->thisTopic->topic_hide_posters) {
                echo '<div class="spCenter spPostUserStatus">User Information Hidden</div>';
            } else {
				sp_PostIndexUserDate('tagClass=spPostUserDate spCenter');
				sp_UserAvatar('tagClass=spPostUserAvatar spCenter&context=user', SP()->forum->view->thisPostUser);
				sp_PostIndexUserName('tagClass=spPostUserName spCenter');
				sp_PostIndexUserLocation('tagClass=spPostUserLocation spCenter');
				sp_PostIndexUserRank('tagClass=spPostUserRank spCenter');
				sp_PostIndexUserSpecialRank('tagClass=spPostUserSpecialRank spCenter');
				sp_PostIndexUserPosts('tagClass=spPostUserPosts spCenter', __sp('Forum Posts: %COUNT%'));
				if (function_exists('sp_PostIndexCubePoints')) sp_PostIndexCubePoints('tagClass=spPostUserCubePoints spCenter', __sp('CubePoints'));
				sp_PostIndexUserRegistered('tagClass=spPostUserRegistered spCenter', __sp('Member Since:<br /> %DATE%'));
				sp_PostIndexUserStatus('tagClass=spCenter spPostUserStatus', __sp('Online'), __sp('Offline'));
				sp_SectionStart('tagClass=spCenter', 'user-identities');
					sp_PostIndexUserWebsite('', __sp('Visit my website'));
					sp_PostIndexUserTwitter('', __sp('Follow me on Twitter'));
					sp_PostIndexUserFacebook('', __sp('Connect with me on Facebook'));
					sp_PostIndexUserMySpace('', __sp('See MySpace'));
					sp_PostIndexUserLinkedIn('', __sp('My LinkedIn network'));
					sp_PostIndexUserYouTube('', __sp('View my YouTube channel'));
					sp_PostIndexUserGooglePlus('', __sp('Interact with me on Google Plus'));
				sp_SectionEnd('', 'user-identities');
            }
		sp_ColumnEnd();

though you may have more or less template funtcions within the 'else' section of code and you column widths might be different.
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPHIDEDBVERSION', 0);

define('SPHIDEDIR', 		SPPLUGINDIR.'hide-poster/');
define('SPHIDEADMINDIR', 	SPPLUGINDIR.'hide-poster/admin/');
define('SPHIDELIBDIR', 	    SPPLUGINDIR.'hide-poster/library/');
define('SPHIDELIBURL', 	    SPPLUGINURL.'hide-poster/library/');
define('SPHIDEIMAGES', 	    SPPLUGINURL.'hide-poster/resources/images/');

add_action('init', 										                'sp_hide_poster_localization');
add_action('sph_activate_hide-poster/sp-hide-poster-plugin.php',        'sp_hide_poster_install');
add_action('sph_deactivate_hide-poster/sp-hide-poster-plugin.php',      'sp_hide_poster_deactivate');
add_action('sph_uninstall_hide-poster/sp-hide-poster-plugin.php',       'sp_hide_poster_uninstall');
add_action('sph_activated', 				                            'sp_hide_poster_sp_activate');
add_action('sph_deactivated', 				                            'sp_hide_poster_sp_deactivate');
add_action('sph_uninstalled', 								            'sp_hide_poster_sp_uninstall');
add_action('sph_plugin_update_hide-poster/sp-hide-poster-plugin.php',   'sp_hide_poster_upgrade_check');
add_action('admin_footer',                                              'sp_hide_poster_upgrade_check');
add_action('sph_permissions_reset', 						            'sp_hide_poster_reset_permissions');
add_action('sph_forum_create_forum_options', 				       	    'sp_hide_poster_create_forum');
add_action('sph_forum_forum_create', 							        'sp_hide_poster_create_forum_save');
add_action('sph_forum_edit_forum_options', 						        'sp_hide_poster_edit_forum');
add_action('sph_forum_forum_edit', 								        'sp_hide_poster_edit_forum_save');
add_action('sph_post_create', 							                'sp_hide_poster_save_post', 5);
add_action('sph_setup_forum', 							    	        'sp_hide_poster_process_actions');
add_action('sph_options_content_left_panel', 							'sp_hide_poster_admin_options');
add_action('sph_option_content_save', 									'sp_hide_poster_admin_save_options');

add_filter('sph_plugins_active_buttons',        'sp_hide_poster_uninstall_option', 10, 2);
add_filter('sph_groupview_query', 			    'sp_hide_poster_group_query');
add_filter('sph_groupview_group_records', 	    'sp_hide_poster_group_forum_records', 10, 2);
add_filter('sph_groupview_stats_query', 	    'sp_hide_poster_stats_query');
add_filter('sph_groupview_stats_records', 	    'sp_hide_poster_topic_records', 10, 2);
add_filter('sph_forumview_query', 			    'sp_hide_poster_forum_topic_query');
add_filter('sph_forumview_forum_record', 	    'sp_hide_poster_group_forum_records', 10, 2);
add_filter('sph_forumview_topic_records', 	    'sp_hide_poster_topic_records', 10, 2);
add_filter('sph_forumview_subforums_query', 	'sp_hide_poster_group_query');
add_filter('sph_forumview_subforum_records', 	'sp_hide_poster_topic_records', 10, 2);
add_filter('sph_topicview_query', 			    'sp_hide_poster_forum_topic_query');
add_filter('sph_topicview_topic_record', 	    'sp_hide_poster_topic_records', 10, 2);
add_filter('sph_topic_options_add', 	        'sp_hide_poster_topic_form_options', 10, 2);
add_filter('sph_topic_list_query',              'sp_hide_poster_forum_topic_query', 10, 2);
add_filter('sph_topic_list_record',             'sp_hide_poster_list_records', 10, 2);
add_filter('sph_post_list_query',               'sp_hide_poster_forum_topic_query', 10, 2);
add_filter('sph_post_list_record',              'sp_hide_poster_list_records', 10, 2);
add_filter('sph_search_query',                  'sp_hide_poster_search_query', 10, 4);
add_filter('sph_feed_item',                     'sp_hide_poster_feeds', 10, 2);
add_filter('sph_ForumIndexLastPost_args',       'sp_hide_poster_name');
add_filter('sph_SubForumIndexLastPost_args',    'sp_hide_poster_name');
add_filter('sph_TopicIndexFirstPost_args',      'sp_hide_poster_name');
add_filter('sph_TopicIndexLastPost_args',       'sp_hide_poster_name');
add_filter('sph_ListLastPost_args',             'sp_hide_poster_name');
add_filter('sph_ListFirstPost_args',            'sp_hide_poster_name');
add_filter('sph_Avatar',                        'sp_hide_poster_avatar');
add_filter('sph_ShareThisTopicIndexTag',        'sp_hide_poster_topic_tags');
add_filter('sph_ShareThisTopicTag',             'sp_hide_poster_topic_tags');
add_filter('sph_PostIndexReportPost',           'sp_hide_poster_topic_tags');
add_filter('sph_PostIndexQuote',                'sp_hide_poster_topic_tags');
add_filter('sph_PmSendPmButton',                'sp_hide_poster_topic_tags');
add_filter('sph_PostIndexUserSignature',        'sp_hide_poster_topic_tags');
add_filter('sph_PostThank',                     'sp_hide_poster_topic_tags');
add_filter('sph_PostThanksList',                'sp_hide_poster_topic_tags');
add_filter('sph_AnswersTopicAnswer',            'sp_hide_poster_topic_tags');
add_filter('sph_AnswersTopicAnswersButton',     'sp_hide_poster_topic_tags');
add_filter('sph_AnswersTopicSeeAnswer',         'sp_hide_poster_topic_tags');
add_filter('sph_add_common_tools', 		        'sp_hide_poster_common_tool', 10, 6);
add_filter('sph_add_topic_tool', 		        'sp_hide_poster_forum_tool', 10, 5);
add_filter('sph_admin_help-admin-options', 		'sp_hide_poster_admin_help', 10, 3);
add_filter('sph_buddypress_new_activity', 		'sp_hide_poster_bp_activity', 10, 2);
add_filter('sph_perms_tooltips', 				'sp_hide_poster_tooltips', 10, 2);

function sp_hide_poster_localization() {
	sp_plugin_localisation('sp-hide-poster');
}

function sp_hide_poster_install() {
    require_once SPHIDEDIR.'sp-hide-poster-install.php';
    sp_hide_poster_do_install();
}

function sp_hide_poster_deactivate() {
    require_once SPHIDEDIR.'sp-hide-poster-uninstall.php';
    sp_hide_poster_do_deactivate();
}

function sp_hide_poster_uninstall() {
    require_once SPHIDEDIR.'sp-hide-poster-uninstall.php';
    sp_hide_poster_do_uninstall();
}

function sp_hide_poster_sp_activate() {
	require_once SPHIDEDIR.'sp-hide-poster-install.php';
    sp_hide_poster_do_sp_activate();
}

function sp_hide_poster_sp_deactivate() {
	require_once SPHIDEDIR.'sp-hide-poster-uninstall.php';
    sp_hide_poster_do_sp_deactivate();
}

function sp_hide_poster_sp_uninstall() {
	require_once SPHIDEDIR.'sp-hide-poster-uninstall.php';
    sp_hide_poster_do_sp_uninstall();
}

function sp_hide_poster_upgrade_check() {
    require_once SPHIDEDIR.'sp-hide-poster-upgrade.php';
    sp_hide_poster_do_upgrade_check();
}

function sp_hide_poster_uninstall_option($actionlink, $plugin) {
    require_once SPHIDEDIR.'sp-hide-poster-uninstall.php';
    $actionlink = sp_hide_poster_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_hide_poster_reset_permissions() {
    require_once SPHIDEDIR.'sp-hide-poster-install.php';
    sp_hide_poster_do_reset_permissions();
}

function sp_hide_poster_create_forum() {
    require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	sp_hide_poster_do_create_forum();
}

function sp_hide_poster_create_forum_save($forumid) {
	if (isset($_POST['forum_hide_posters'])) $hide = 1; else $hide = 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET forum_hide_posters=$hide WHERE forum_id=$forumid");
    SP()->auths->reset_cache();
}

function sp_hide_poster_edit_forum($forum) {
    require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	sp_hide_poster_do_edit_forum($forum);
}

function sp_hide_poster_edit_forum_save($forumid) {
	if (isset($_POST['forum_hide_posters'])) $hide = 1; else $hide = 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET forum_hide_posters=$hide WHERE forum_id=$forumid");
    SP()->auths->reset_cache();
}

function sp_hide_poster_group_query($query) {
	$query->fields.= ', forum_hide_posters';
	return $query;
}

function sp_hide_poster_stats_query($query) {
	$query->fields.= ', topic_hide_posters';
	return $query;
}

function sp_hide_poster_forum_topic_query($query) {
	$query->fields.= ', forum_hide_posters, topic_hide_posters';
	return $query;
}

function sp_hide_poster_search_query($query, $searchterm, $searchtype, $searchinclude) {
    if ($searchtype == 4 || $searchtype == 5) {
        $query->where.= ' AND topic_hide_posters=0';
    	$query->where = str_replace('user_id' , SPPOSTS.'.user_id', $query->where);
		$query->join[] = SPTOPICS.' ON '.SPTOPICS.'.topic_id = '.SPPOSTS.'.topic_id';
    }
	return $query;
}

function sp_hide_poster_group_forum_records($data, $record) {
	$data->forum_hide_posters = $record->forum_hide_posters;
	return $data;
}

function sp_hide_poster_topic_records($data, $record) {
	if (isset($record->forum_hide_posters)) $data->forum_hide_posters = $record->forum_hide_posters;
	if (isset($record->topic_hide_posters)) $data->topic_hide_posters = $record->topic_hide_posters;
	return $data;
}

function sp_hide_poster_list_records($data, $record) {
	$data->topic_hide_posters = $record->topic_hide_posters;
    if ($record->topic_hide_posters) $data->display_name = __('Name Hidden', 'sp-hide-poster');
	return $data;
}

function sp_hide_poster_topic_form_options($content, $thisForum) {
    require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	$content = sp_hide_poster_do_topic_form_options($content, $thisForum);
	return $content;
}

function sp_hide_poster_save_post($newpost) {
    require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
    sp_hide_poster_do_save_post($newpost);
}

function sp_hide_poster_forum_tool($out, $topic, $forum, $page, $br) {
	require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	$out = sp_hide_poster_do_forum_tool($out, $forum, $topic, $page, $br);
    return $out;
}

function sp_hide_poster_common_tool($out, $forum, $topic, $post, $page, $br) {
    remove_filter('sph_add_topic_tool', 'sp_hide_poster_forum_tool', 10, 5);
	require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	$out = sp_hide_poster_do_forum_tool($out, $forum, $topic, $page, $br);
    return $out;
}

function sp_hide_poster_process_actions() {
	require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	sp_hide_poster_do_process_actions();
}

function sp_hide_poster_feeds($item, $postList) {
	require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	$item = sp_hide_poster_do_feeds($item, $postList);
    return $item;
}

function sp_hide_poster_name($a) {
	require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	$a = sp_hide_poster_do_name($a);
    return $a;
}

function sp_hide_poster_avatar($avatarData) {
	require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	$avatarData = sp_hide_poster_do_avatar($avatarData);
    return $avatarData;
}

function sp_hide_poster_topic_tags($out) {
	require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	$out = sp_hide_poster_do_topic_tags($out);
    return $out;
}

function sp_hide_poster_admin_options() {
    require_once SPHIDEADMINDIR.'sp-hide-poster-admin-options.php';
	sp_hide_poster_admin_options_form();
}

function sp_hide_poster_admin_save_options() {
    require_once SPHIDEADMINDIR.'sp-hide-poster-admin-options-save.php';
    return sp_hide_poster_admin_options_save();
}

function sp_hide_poster_admin_help($file, $tag, $lang) {
    if ($tag == '[hide-poster]') $file = SPHIDEADMINDIR.'sp-hide-poster-admin-help.'.$lang;
    return $file;
}

function sp_hide_poster_bp_activity($do, $newpost) {
	require_once SPHIDELIBDIR.'sp-hide-poster-components.php';
	$do = sp_hide_poster_do_bp_activity($do, $newpost);
    return $do;
}

function sp_hide_poster_tooltips($tips, $t) {
    $tips['hide_posters'] = $t.__('Can enable/disable hide posters for a topic', 'sp-pm');
    return $tips;
}
