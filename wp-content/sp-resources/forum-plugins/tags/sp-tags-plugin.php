<?php
/*
Simple:Press Plugin Title: Tags
Version: 2.1.0
Item Id: 3921
Plugin URI: https://simple-press.com/downloads/tags-plugin/
Description: A Simple:Press plugin for allowing topic tags
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A plugin for Simple:Press to users to apply tags to topics.
$LastChangedDate: 2016-03-11 16:44:24 -0800 (Fri, 11 Mar 2016) $
$Rev: 14047 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPTAGSDBVERSION', 5);

define('SPTAGS', 		SP_PREFIX.'sftags');
define('SPTAGSMETA', 	SP_PREFIX.'sftagmeta');
define('SPTDIR', 		SPPLUGINDIR.'tags/');
define('SPTADMINDIR', 	SPPLUGINDIR.'tags/admin/');
define('SPTLIBDIR', 	SPPLUGINDIR.'tags/library/');
define('SPTAJAXDIR', 	SPPLUGINDIR.'tags/ajax/');
define('SPTFORMSDIR', 	SPPLUGINDIR.'tags/forms/');
define('SPTSCRIPT', 	SPPLUGINURL.'tags/resources/jscript/');
define('SPTCSS', 		SPPLUGINURL.'tags/resources/css/');
define('SPTAGSTEMPDIR', SPPLUGINDIR.'tags/template-files/');
define('SPTTAGSDIR', 	SPPLUGINDIR.'tags/template-tags/');
define('SPTIMAGES',		SPPLUGINURL.'tags/resources/images/');
define('SPTIMAGESMOB',	SPPLUGINURL.'tags/resources/images/mobile/');

add_action('sph_activate_tags/sp-tags-plugin.php', 		'sp_tags_install');
add_action('sph_uninstall_tags/sp-tags-plugin.php', 	'sp_tags_uninstall');
add_action('sph_deactivate_tags/sp-tags-plugin.php',    'sp_tags_deactivate');
add_action('sph_activated', 				            'sp_tags_sp_activate');
add_action('sph_deactivated', 				            'sp_tags_sp_deactivate');
add_action('sph_uninstalled', 							'sp_tags_sp_uninstall');
add_action('sph_plugin_update_tags/sp-tags-plugin.php', 'sp_tags_upgrade_check');
add_action('init', 										'sp_tags_localization');
add_action('sph_admin_menu', 							'sp_tags_menu');
add_action('sph_add_style',								'sp_tags_add_style_icon');
add_action('sph_components_seo_right_panel', 			'sp_tags_admin_seo_options');
add_action('sph_component_seo_save', 					'sp_tags_admin_save_seo_options');
add_action('sph_admin_caps_form', 						'sp_tags_admin_cap_form', 10, 2);
add_action('sph_admin_caps_list', 						'sp_tags_admin_cap_list', 10, 2);
add_action('sph_scripts_admin_end', 					'sp_tags_load_admin_js');
add_action('sph_print_plugin_scripts', 					'sp_tags_load_js');
add_action('sph_forum_create_forum_options', 			'sp_tags_create_forum');
add_action('sph_forum_forum_create', 					'sp_tags_create_forum_save');
add_action('sph_forum_edit_forum_options', 				'sp_tags_edit_forum');
add_action('sph_forum_forum_edit', 						'sp_tags_edit_forum_save');
add_action('sph_setup_forum', 							'sp_tags_process_actions');
add_action('sph_post_create', 							'sp_tags_topic_create');
add_action('sph_print_plugin_styles', 					'sp_tags_header');
add_action('sph_merge_forums',							'sp_tags_merge_forums', 1, 2);
add_action('admin_footer',                              'sp_tags_upgrade_check');
add_action('sph_permissions_reset',                     'sp_tags_reset_permissions');
add_action('sph_get_query_vars', 						'sp_tags_get_query_vars');
add_action('sph_get_def_query_vars', 					'sp_tags_get_def_query_vars');
add_action('sph_admin_menu',                            'sp_tags_admin_menu');

add_filter('sph_plugins_active_buttons', 			'sp_tags_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-plugins', 			'sp_tags_admin_help', 10, 3);
add_filter('sph_admin_help-admin-components', 		'sp_tags_admin_help', 10, 3);
add_filter('sph_admin_caps_new', 					'sp_tags_admin_caps_new', 10, 2);
add_filter('sph_admin_caps_update', 				'sp_tags_admin_caps_update', 10, 3);
add_filter('sph_acknowledgements', 					'sp_tags_acknowledge');
add_filter('sph_meta_keywords', 					'sp_tags_meta_keywords');
add_filter('sph_add_common_tools', 					'sp_tags_topic_tool', 10, 6);
add_filter('sph_new_forum_post', 					'sp_tags_topic_post');
add_filter('sph_rewrite_rules_start', 			    'sp_tags_rewrite_rules', 10, 3);
add_filter('sph_query_vars', 					    'sp_tags_query_vars');
add_filter('sph_pageview', 						    'sp_tags_pageview');
add_filter('sph_canonical_url', 				    'sp_tags_canonical_url');
add_filter('sph_page_title', 					    'sp_tags_page_title', 10, 2);
add_filter('sph_BreadCrumbs', 				        'sp_tags_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile', 		        'sp_tags_breadcrumbMobile', 10, 2);
add_filter('sph_DefaultViewTemplate',			    'sp_tags_template_name', 10, 2);
add_filter('sph_ShowAdminLinks', 		            'sp_tags_admin_links', 10, 2);

if (isset(SP()->core->forumData['display']) && SP()->core->forumData['display']['editor']['toolbar']) {
	add_filter('sph_topic_editor_toolbar_buttons',	'sp_tags_editor_button', 10, 4);
	add_filter('sph_topic_editor_toolbar',			'sp_tags_new_topic_form', 10, 4);
} else {
	add_filter('sph_topic_editor_footer_top',		'sp_tags_new_topic_form', 10, 2);
}

add_filter('sph_forumview_query', 					'sp_tags_forum_query');
add_filter('sph_forumview_forum_record',			'sp_tags_forum_records', 10, 2);
add_filter('sph_forumview_combined_data', 			'sp_tags_forum_combined', 10, 2);
add_filter('sph_topicview_query', 					'sp_tags_forum_query');
add_filter('sph_topicview_topic_record', 			'sp_tags_topic_records', 10, 2);
add_filter('sph_SearchFormOptions', 				'sp_tags_search_form');
add_filter('sph_search_include_where', 				'sp_tags_search_where', 10, 4);
add_filter('sph_search_query', 						'sp_tags_search_query', 10, 4);
add_filter('sph_search_label', 						'sp_tags_search_label', 10, 4);
add_filter('sph_search_term', 						'sp_tags_search_term', 10, 4);
add_filter('sph_load_admin_textdomain', 			'sp_tags_load_admin');
add_filter('sph_forum_tools_forum_show',            'sp_tags_forum_tool_show');
add_filter('sph_perms_tooltips', 					'sp_tags_tooltips', 10, 2);

# Ajax handlers
add_action('wp_ajax_tags-admin',		'sp_tags_ajax_admin');
add_action('wp_ajax_nopriv_tags-admin',	'sp_tags_ajax_admin');
add_action('wp_ajax_tags-ajax',			'sp_tags_ajax');
add_action('wp_ajax_nopriv_tags-ajax',	'sp_tags_ajax');


function sp_tags_admin_menu($parent) {
    if (!SP()->auths->current_user_can('SPF Manage Tags')) return;
	add_submenu_page($parent, esc_attr(__('Tags', 'sp-tags')), esc_attr(__('Tags', 'sp-tags')), 'read', SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_tags_admin_options&save=sp_tags_admin_save_options&form=1&panel='.urlencode(__('Tags', 'sp-tags')), 'dummy');
}

function sp_tags_menu() {
	$panels = array(
                __('Options', 'sp-tags') => array('admin' => 'sp_tags_admin_options', 'save' => 'sp_tags_admin_save_options', 'form' => 1, 'id' => 'tagopt'),
                __('Manage Tags', 'sp-tags') => array('admin' => 'sp_tags_admin_manage', 'save' => '', 'form' => 0, 'id' => 'tagmanage'),
                __('Mass Edit Tags', 'sp-tags') => array('admin' => 'sp_tags_admin_edit', 'save' => '', 'form' => 0, 'id' => 'tagedit')
				);
    SP()->plugin->add_admin_panel(__('Tags', 'sp-tags'), 'SPF Manage Tags', __('Set up and manage your tags', 'sp-tags'), 'icon-Tags', $panels, 9);
}

function sp_tags_admin_links($out, $br) {
	if (SP()->auths->current_user_can('SPF Manage Tags')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_tags_admin_options&save=sp_tags_admin_save_options&form=1').'">';
		$out.= SP()->theme->paint_icon('spIcon', SPTIMAGES, "sp_ManageTags.png").$br;
		$out.= __('Tags', 'sp-tags').'</a></p>';
		$out.= sp_close_grid_cell();
	}
    return $out;
}

function sp_tags_add_style_icon() {
	echo('.spaicon-Tags:before {content: "\e10b";}');
}

function sp_tags_localization() {
	sp_plugin_localisation('sp-tags');
}

function sp_tags_uninstall() {
    require_once SPTDIR.'sp-tags-uninstall.php';
    sp_tags_do_uninstall();
}

function sp_tags_install() {
    require_once SPTDIR.'sp-tags-install.php';
    sp_tags_do_install();
}

function sp_tags_deactivate() {
    require_once SPTDIR.'sp-tags-uninstall.php';
    sp_tags_do_deactivate();
}

function sp_tags_sp_uninstall($admins) {
    require_once SPTDIR.'sp-tags-uninstall.php';
    sp_tags_do_sp_uninstall($admins);
}

function sp_tags_sp_activate() {
	require_once SPTDIR.'sp-tags-install.php';
    sp_tags_do_sp_activate();
}

function sp_tags_sp_deactivate() {
	require_once SPTDIR.'sp-tags-uninstall.php';
    sp_tags_do_sp_deactivate();
}

function sp_tags_uninstall_option($actionlink, $plugin) {
	require_once SPTDIR.'sp-tags-uninstall.php';
	$actionlink = sp_tags_do_uninstall_option($actionlink, $plugin);
	return $actionlink;
}

function sp_tags_upgrade_check() {
    require_once SPTDIR.'sp-tags-upgrade.php';
    sp_tags_do_upgrade_check();
}

function sp_tags_reset_permissions() {
    require_once SPTDIR.'sp-tags-install.php';
    sp_tags_do_reset_permissions();
}

function sp_tags_admin_options() {
    require_once SPTADMINDIR.'sp-tags-admin-options.php';
	sp_tags_admin_options_form();
}

function sp_tags_admin_save_options() {
    require_once SPTADMINDIR.'sp-tags-admin-options-save.php';
    return sp_tags_admin_options_save();
}

function sp_tags_admin_manage() {
    require_once SPTADMINDIR.'sp-tags-admin-manage.php';
	sp_tags_admin_manage_form();
}

function sp_tags_admin_edit() {
    require_once SPTADMINDIR.'sp-tags-admin-edit.php';
	sp_tags_admin_edit_form();
}

function sp_tags_admin_seo_options() {
    require_once SPTADMINDIR.'sp-tags-admin-seo-options.php';
	sp_tags_admin_seo_options_form();
}

function sp_tags_admin_save_seo_options() {
    require_once SPTADMINDIR.'sp-tags-admin-seo-options-save.php';
    return sp_tags_admin_seo_options_save();
}

function sp_tags_admin_help($file, $tag, $lang) {
    if ($tag == '[topic-tags]' || $tag == '[tags-manage]' || $tag == '[tags-edit]' || $tag == '[tags-seo]') $file = SPTADMINDIR.'sp-tags-admin-help.'.$lang;
    return $file;
}

function sp_tags_tooltips($tips, $t) {
    $tips['edit_tags'] = $t.__('Can edit topic tags', 'sp-tags');
    return $tips;
}

function sp_tags_admin_cap_form($user) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	sp_tags_do_admin_cap_form($user);
}

function sp_tags_admin_cap_list($user) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	sp_tags_do_admin_cap_list($user);
}

function sp_tags_admin_caps_new($newadmin, $user) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	$newadmin = sp_tags_do_admin_caps_new($newadmin, $user);
	return $newadmin;
}

function sp_tags_admin_caps_update($still_admin, $remove_admin, $user) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	$still_admin = sp_tags_do_admin_caps_update($still_admin, $remove_admin, $user);
	return $still_admin;
}

function sp_tags_load_js($footer) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	sp_tags_do_load_js($footer);
}

function sp_tags_load_admin_js($footer) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	sp_tags_do_load_admin_js($footer);
}

function sp_tags_acknowledge($ack) {
	$ack[] = '<a href="http://www.herewithme.fr/wordpress-plugins/simple-tags">'.__('Simple:Press tags uses some code and ideas by Amaury Balmer', 'sp-tags').'</a>';
	return $ack;
}

function sp_tags_ajax_admin() {
    require_once SPTAJAXDIR.'sp-tags-ajax-admin.php';
}

function sp_tags_ajax() {
    require_once SPTAJAXDIR.'sp-tags-ajax.php';
}

function sp_tags_create_forum() {
	spa_paint_checkbox(__('Enable tags on this forum', 'sp-tags'), 'forum_tags', 1);
}

function sp_tags_create_forum_save($forumid) {
	$usetags = (isset($_POST['forum_tags'])) ? 1 : 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET use_tags=$usetags WHERE forum_id=$forumid");
}

function sp_tags_edit_forum($forum) {
	spa_paint_checkbox(__('Enable tags on this forum', 'sp-tags'), 'forum_tags', $forum->use_tags);
}

function sp_tags_edit_forum_save($forumid) {
	if (isset($_POST['forum_tags'])) $usetags = 1; else $usetags = 0;
	SP()->DB->execute("UPDATE ".SPFORUMS." SET use_tags = ".$usetags." WHERE forum_id=".$forumid);
}

function sp_tags_meta_keywords($keywords) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	$keywords = sp_tags_do_meta_keywords($keywords);
	return $keywords;
}

function sp_tags_process_actions() {
    require_once SPTLIBDIR.'sp-tags-database.php';
	if (isset($_POST['maketagsedit'])) sp_change_topic_tags(SP()->filters->integer(SP()->filters->integer($_POST['topicid'])), SP()->filters->str($_POST['topictags']));
}

function sp_tags_topic_create($newpost) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	sp_tags_do_topic_create($newpost);
}

function sp_tags_topic_tool($out, $forum, $topic, $post, $page, $br) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	$out = sp_tags_do_topic_tool($out, $forum, $topic, $post, $page, $br);
	return $out;
}

function sp_tags_forum_tool_show($show) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	$show = sp_tags_do_forum_tool_show($show);
	return $show;
}

function sp_tags_topic_post($newpost) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	$newpost = sp_tags_do_topic_post($newpost);
	return $newpost;
}

function sp_tags_editor_button($out, $forum, $a, $toolbar) {
	global $tab;
	if($forum->use_tags) {
    	if ((SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive'))) {
			# display mobile icon
			$out.= "<button type='button' tabindex='".$tab++."' style='background:transparent;' class='spIcon spEditorBoxOpen' name='tags' id='sftags' data-box='spTagsBox'>\n";
			$out.= SP()->theme->paint_icon('spIcon', SPTIMAGESMOB, "sp_TagsToolbar.png", '');
			$out.= "</button>";

		} else {
			$out.= "<input type='button' tabindex='".$tab++."' class='spSubmit spLeft spEditorBoxOpen' title='".__('Open/Close to create Topic Tags', 'sp-tags')."' id='spTagsButton' value='".__('Tags', 'sp-tags')."' data-box='spTagsBox' />";
		}
		return $out;
	}
}

function sp_tags_new_topic_form($out, $forum) {
	require_once SPTLIBDIR.'sp-tags-components.php';
	$out = sp_tags_do_new_topic_form($out, $forum);
	return $out;
}

function sp_tags_header() {
    require_once SPTLIBDIR.'sp-tags-components.php';
	sp_tags_do_header();
}

function sp_tags_merge_forums($source, $target) {
    require_once SPTLIBDIR.'sp-tags-components.php';
	sp_tags_do_merge_forums($source, $target);
}

function sp_tags_forum_query($query) {
	$query->fields.= ', use_tags';
	return $query;
}

function sp_tags_forum_records($data, $record) {
	$data->use_tags = $record->use_tags;
	return $data;
}

function sp_tags_forum_combined($forumData, $topicList) {
    require_once SPTLIBDIR.'sp-tags-components.php';
	$forumData = sp_tags_do_forum_combined($forumData, $topicList);
	return $forumData;
}

function sp_tags_topic_records($topic, $record) {
    require_once SPTLIBDIR.'sp-tags-components.php';
	$topic = sp_tags_do_topic_records($topic, $record);
	return $topic;
}

function sp_tags_search_form($out) {
	$out.= '<input type="radio" id="sfradiotags" name="encompass" value="4"'.(!empty(SP()->rewrites->pageData['searchinclude']) && SP()->rewrites->pageData['searchinclude'] == 4 ? ' checked="checked"' : '').' /><label class="spLabel spRadio" for="sfradiotags">'.__("Tags only (single tag)", 'sp-tags').'</label>';
	return $out;
}

function sp_tags_search_where($where, $value, $type, $include) {
    global $wpdb;
	$where = SPTOPICS.'.topic_id IN (SELECT topic_id FROM '.SPTAGSMETA.' JOIN '.SPTAGS.' ON '.SPTAGSMETA.'.tag_id = '.SPTAGS.".tag_id WHERE tag_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($value))."%')";
	return $where;
}

function sp_tags_search_query($query, $value, $type, $include) {
	if ($include == 4) {
	   $query->join = '';
       $query->fields = SPTOPICS.'.topic_id';
       $query->orderby = SPTOPICS.'.topic_id';
    }
	return $query;
}

function sp_tags_search_label($label, $type, $include, $tag) {
	if ($include == 4) $label = __('Search results for tag', 'sp-tags').': '.$tag;
	return $label;
}

function sp_tags_search_term($term, $original, $type, $include) {
	if ($include == 4) $term = $original;
	return $term;
}

function sp_tags_load_admin($special) {
    $special[] = 'action=tags';
    return $special;
}

function sp_tags_get_query_vars() {
	SP()->rewrites->pageData['related-tags'] = SP()->filters->str(get_query_var('sf_related-tags'));
	if (empty(SP()->rewrites->pageData['related-tags'])) SP()->rewrites->pageData['related-tags'] = 0;
}

function sp_tags_get_def_query_vars($stuff) {
    if ($stuff[1] == 'related-tags') {
        SP()->rewrites->pageData['related-tags'] = true;
        SP()->rewrites->pageData['plugin-vars'] = true;
    }
	if (empty(SP()->rewrites->pageData['related-tags'])) SP()->rewrites->pageData['related-tags'] = 0;
}

function sp_tags_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/related-tags/?$'] = 'index.php?pagename='.$slug.'&sf_related-tags=view';
    return $rules;
}

function sp_tags_query_vars($vars) {
	$vars[] = 'sf_related-tags';
    return $vars;
}

function sp_tags_pageview($pageview) {
    if (!empty(SP()->rewrites->pageData['related-tags'])) $pageview = 'related-tags';
    return $pageview;
}

function sp_tags_canonical_url($url) {
    if (SP()->rewrites->pageData['pageview'] == 'related-tags') $url = SP()->spPermalinks->get_url('related-tags');
    return $url;
}

function sp_tags_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
    if (!empty(SP()->rewrites->pageData['related-tags'])) {
    	extract($args, EXTR_SKIP);
		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}
		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('related-tags')."'>".__('Related Tags', 'sp-tags').'</a>';
    }
    return $breadCrumbs;
}

function sp_tags_breadcrumbMobile($breadCrumbs, $args) {
    if (!empty(SP()->rewrites->pageData['related-tags'])) {
    	extract($args, EXTR_SKIP);
		$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('related-tags')."'>".__('Related Tags', 'sp-tags').'</a>';
    }
    return $breadCrumbs;
}

function sp_tags_page_title($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'related-tags') $title = __('Related Tags', 'sp-tags').$sep.$title;
    return $title;
}

function sp_tags_template_name($name, $pageview) {
	if ($pageview != 'related-tags') return $name;
	$tempName = SP()->theme->find_template(SPTAGSTEMPDIR,'spRelatedTagsView.php');
	return $tempName;
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)

function sp_TopicIndexTagsList($args='', $label='', $toolTip='') {
    require_once SPTTAGSDIR.'sp-tags-forum-tags-tag.php';
	sp_TopicIndexTagsListTag($args, $label, $toolTip);
}

function sp_TopicTagsList($args='', $label='') {
    require_once SPTTAGSDIR.'sp-tags-topic-tags-tag.php';
	sp_TopicTagsListTag($args, $label);
}

function sp_RelatedTopicsButton($args='', $label='', $toolTip='') {
    require_once SPTTAGSDIR.'sp-tags-related-topics-button-tag.php';
	sp_RelatedTopicsButtonTag($args, $label, $toolTip);
}

function sp_TagsMostUsed($limit=10, $echo=true) {
    require_once SPTTAGSDIR.'sp-tags-most-used-tags-tag.php';
	sp_TagsMostUsedTag($limit, $echo);
}

function spRelatedTopics($limit=10, $topic_id, $listtags=true, $forum=true, $echo=true) {
    require_once SPTTAGSDIR.'sp-tags-related-topics-tag.php';
	spRelatedTopicsTag($limit, $topic_id, $listtags, $forum, $echo);
}

function sp_TagCloud($limit=25, $sort='random', $size=true, $smallest=8, $largest=22, $unit='pt', $color=true, $mincolor='#000000', $maxcolor='#cccccc', $echo=true, $sep=' ') {
    require_once SPTTAGSDIR.'sp-tags-tag-cloud-tag.php';
	sp_TagCloudTag($limit, $sort, $size, $smallest, $largest, $unit, $color, $mincolor, $maxcolor, $echo, $sep);
}
