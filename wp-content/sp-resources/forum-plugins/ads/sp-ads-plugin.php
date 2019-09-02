<?php

/*
  Simple:Press Plugin Title: ADS
  Version: 1.0.0
  Item Id: 79469
  Plugin URI: https://simple-press.com/downloads/ads
  Description: A Simple:Press plugin for managing ads inside the Simple:Press forums
  Author: Simple:Press
  Original Author: Wonderkidstudio
  Author URI: https://simple-press.com
  Original Author URI: http://www.wonderkidstudio.com/
  Simple:Press Versions: 6.0.7 and above
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

define('ADS_FOLDER_NAME', basename(__DIR__));

include SPPLUGINDIR . 'ads/library/sp-ads-database.php';
include SPPLUGINDIR . 'ads/library/sp_ads_logic_showing_ad.php';

define('ADS_URL_RESOURCES', SPPLUGINURL . 'ads/resources/');

add_action('sph_activate_ads/sp-ads-plugin.php', 'sp_ads_activate_ads_plugin');
add_action('sph_deactivate_ads/sp-ads-plugin.php', 'sp_ads_deactivate_ads_plugin');
add_action('sph_uninstall_ads/sp-ads-plugin.php', 'sp_ads_uninstall_ads_plugin');

# add styles and scripts
add_action('sph_print_plugin_styles', 'spa_ads_load_css');
add_action('admin_print_styles', 'spa_ads_load_admin_css');
add_action('admin_enqueue_scripts', 'sp_ads_do_load_admin_js');

# Ajax Handlers
add_action('wp_ajax_ads-ad-set-add', 'sp_ads_ad_set_add_ajax');
add_action('wp_ajax_ads-ad-set-edit', 'sp_ads_ad_set_edit_ajax');
add_action('wp_ajax_ads-ad-set-delete', 'sp_ads_ad_set_delete_ajax');
add_action('wp_ajax_ads-ad-set-select-topics', 'sp_ads_ad_set_select_topics');

add_action('wp_ajax_ads-ad-add', 'sp_ads_ad_add_ajax');
add_action('wp_ajax_ads-ad-edit', 'sp_ads_ad_edit_ajax');
add_action('wp_ajax_ads-ad-delete', 'sp_ads_ad_delete_ajax');

add_action('wp_ajax_ads-report-ad', 'sp_ads_report_ad_ajax');

add_action('sf_admin_panels', 'sp_ads_admin_panels_menu');
add_action('sph_before_template_processing', 'sp_ads_start_logic_showing_ad');

add_filter('sph_plugins_active_buttons', 'sp_ads_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-components', 'sp_ads_admin_help', 10, 3);
add_filter('sph_admin_help-admin-plugins', 'sp_ads_admin_help', 10, 3);

/**
 * Sends json response
 * 
 * @since 1.0
 * 
 * @param mixed $value
 */
function sp_ads_json_response($value) {
    header('Content-type: application/json');
    die(json_encode($value));
}

/**
 * Sends json success response
 * 
 * @since 1.0
 * 
 * @param string $str
 * @return null
 */
function sp_ads_success_resp_json($str) {
    sp_ads_json_response(array('success' => $str));
}

/**
 * Sends json error response
 * 
 * @since 1.0
 * 
 * @param string $str
 * @return null
 */
function sp_ads_error_resp_json($str) {
    sp_ads_json_response(array('error' => $str));
}

/**
 * Calls SP_ADS_Database::doActivate
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_activate_ads_plugin() {
    // admin task glossary entries
    require_once 'sp-admin-glossary.php';
    SP_ADS_Database::doActivate();
}

/**
 * Do deactivate
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_deactivate_ads_plugin() {
    SP_ADS_Database::doDeactivate();
}

/**
 * Calls SP_ADS_Database::doUninstall
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_uninstall_ads_plugin() {
    SP_ADS_Database::doUninstall();
}

/**
 * Adds css
 * 
 * @since 1.0
 * 
 * @return null
 */
function spa_ads_load_css() {
    $style = ADS_URL_RESOURCES . 'css/sp-ads.css';
    wp_enqueue_style('spAds', $style);
}

/**
 * Adds admin css
 * 
 * @since 1.0
 * 
 * @return null
 */
function spa_ads_load_admin_css() {
    wp_enqueue_style('select2'
            , 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css'
            , array('spAdminStyle'));
    $style = ADS_URL_RESOURCES . 'css/sp-ads-admin.css';
    wp_enqueue_style('spAdsAdmin', $style, array('select2'), time());
}

/**
 * Adds admin js
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_do_load_admin_js() {
    wp_enqueue_media();
    wp_enqueue_script('select2'
            , 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js'
            , array(), false, true);
    $script = ADS_URL_RESOURCES . 'js/sp-ads-admin.js';
    wp_enqueue_script('spadsadmin', $script, array('jquery'), time(), true);
    wp_localize_script('spadsadmin', 'SP_ADS', array(
        'ajaxurl' => str_replace('&amp;', '&', wp_nonce_url(SPAJAXURL . 'plugins-loader', 'plugins-loader')),
        'SPADMINIMAGES' => SPADMINIMAGES,
    ));
}

/**
 * Calls method SP_Ads_Showing_Ad::init
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_start_logic_showing_ad() {
    SP_Ads_Showing_Ad::init();
}

/**
 * Adds links of ads plugin in admin manage plugin
 * 
 * @since 1.0
 * 
 * @param string $actionlink
 * @param string $plugin
 * @return string
 */
function sp_ads_uninstall_option($actionlink, $plugin) {
    if ($plugin == 'ads/sp-ads-plugin.php') {
        # uninstall
        $url = SPADMINPLUGINS . '&amp;action=uninstall&amp;plugin=' . $plugin . '&amp;sfnonce=' . wp_create_nonce('forum-adminform_plugins');
        $actionlink .= "&nbsp;&nbsp;<a href='$url' title='" . __('Uninstall this plugin', 'sp-ads') . "'>" . __('Uninstall', 'sp-ads') . '</a>';
    }
    return $actionlink;
}

/**
 * Adds links of ads plugin in admin menu
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_admin_panels_menu() {
    global $sfadminpanels, $sfactivepanels;
    $forms = array(
        __('Ad Sets list', 'sp-ads') => array(
            'plugin&admin=sp_ads_ad_set_list' => 'ad-sets-list'),
    );
    $sfadminpanels[] = array(
        __('Ad Sets', 'sp-ads'),
        'SPF Manage Plugins',
        'admin.php?page=simple-press%2Fadmin%2Fpanel-components%2Fspa-components.php&tab=plugin&admin=sp_ads_ad_set_list&form=0', // '#sp-open-panel',
        'AD Sets',
        'icon-Ads',
        wp_nonce_url(SPAJAXURL . 'plugins-loader', 'plugins-loader'),
        $forms,
        true);
    return $sfadminpanels;
}

/**
 * Creates url of add ad set
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_url_ajax_ad_set_add() {
    return wp_nonce_url(SPAJAXURL . 'ads-ad-set-add', 'ads-ad-set-add');
}

/**
 * Creates url of edit ad set
 * 
 * @since 1.0
 * 
 * @param int $adSetId
 * @return string
 */
function sp_ads_url_ajax_ad_set_edit($adSetId) {
    return wp_nonce_url(SPAJAXURL . 'ads-ad-set-edit&amp;id=' . (int) $adSetId, 'ads-ad-set-edit');
}

/**
 * Creates url of delete ad set
 * 
 * @since 1.0
 * 
 * @param mixed int|array $adSetId
 * @return string
 */
function sp_ads_url_ajax_ad_set_delete($adSetId) {
    return wp_nonce_url(SPAJAXURL . 'ads-ad-set-delete&amp;id=' . implode(',', (array) $adSetId), 'ads-ad-set-delete');
}

/**
 * Creates url of select topics
 * 
 * @since 1.0
 * 
 * @param int $forumId
 * @return string
 */
function sp_ads_url_ajax_select2_topics($forumId) {
    return wp_nonce_url(SPAJAXURL . 'ads-ad-set-select-topics&amp;forumId=' . (int) $forumId, 'ads-ad-set-select-topics');
}

/**
 * Loads and calls function sp_ads_add_ad_set_list_form
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_ad_set_list() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-set-list-form.php';
    return sp_ads_ad_set_list_form();
}

/**
 * Loads and calls function
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_ad_set_add() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-set-add-form.php';
    return sp_ads_ad_set_add_form();
}

/**
 * Loads and calls function sp_ads_ad_set_add_form_ajax
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_ad_set_add_ajax() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-set-add-form-ajax.php';
    sp_ads_ad_set_add_form_ajax();
}

/**
 * Loads and calls function sp_ads_ad_set_edit_form
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_ad_set_edit() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-set-edit-form.php';
    return sp_ads_ad_set_edit_form();
}

/**
 * Loads and calls function sp_ads_ad_set_edit_form_ajax
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_ad_set_edit_ajax() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-set-edit-form-ajax.php';
    sp_ads_ad_set_edit_form_ajax();
}

/**
 * Loads and calls function sp_ads_delete_ad_form_ajax
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_ad_set_delete_ajax() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-set-delete-form-ajax.php';
    sp_ads_ad_set_delete_form_ajax();
}

/**
 * Handling request and send json response
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_set_select_topics() {
    sp_ads_json_response(array('results' => SP_Ads_Database::searchTopics(@$_GET['forumId'], @$_GET['q'])));
}

/**
 * Loads and calls function sp_ads_list_form
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_list() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-list-form.php';
    return sp_ads_list_form();
}

/**
 * Creates url of add ad
 * 
 * @since 1.0
 * 
 * @param int $adSetId
 * @return string
 */
function sp_ads_url_ajax_ad_add($adSetId) {
    return wp_nonce_url(SPAJAXURL . 'ads-ad-add&amp;id=' . (int) $adSetId, 'ads-ad-add');
}

/**
 * Creates url of edit ad
 * 
 * @since 1.0
 * 
 * @param int $adId
 * @return string
 */
function sp_ads_url_ajax_ad_edit($adId) {
    return wp_nonce_url(SPAJAXURL . 'ads-ad-edit&amp;id=' . (int) $adId, 'ads-ad-edit');
}

/**
 * Creates url of delete ads
 * 
 * @since 1.0
 * 
 * @param mixed int|array $adId
 * @return string
 */
function sp_ads_url_ajax_ad_delete($adId) {
    return wp_nonce_url(SPAJAXURL . 'ads-ad-delete&amp;id=' . implode(',', (array) $adId), 'ads-ad-delete');
}

/**
 * Loads and calls function sp_ads_ad_add_form
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_ad_add() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-add-form.php';
    return sp_ads_ad_add_form();
}

/**
 * Loads and calls function sp_ads_ad_add_form_ajax
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_ad_add_ajax() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-add-form-ajax.php';
    sp_ads_ad_add_form_ajax();
}

/**
 * Loads and calls function sp_ads_ad_edit_form
 * 
 * @since 1.0
 * 
 * @return string
 */
function sp_ads_ad_edit() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-edit-form.php';
    return sp_ads_ad_edit_form();
}

/**
 * Loads and calls function sp_ads_ad_edit_form_ajax
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_ad_edit_ajax() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-edit-form-ajax.php';
    sp_ads_ad_edit_form_ajax();
}

/**
 * Loads and calls function sp_ads_ad_delete_form_ajax
 * 
 * @since 1.0
 * 
 * @return null
 */
function sp_ads_ad_delete_ajax() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-ad-delete-form-ajax.php';
    sp_ads_ad_delete_form_ajax();
}

/**
 * Returns word from string
 * 
 * @since 1.0
 * 
 * @param string $word
 * @param int $minWordLen [optional]
 * @return string|null
 */
function sp_ads_filter_word($word, $minWordLen = 1) {
    if (preg_match(sprintf('/[\d\w]{%d,}+/ui', (int) $minWordLen), trim($word), $m)) {
        return $m[0];
    }
}

/**
 * Returns the path to the help file
 * 
 * @since 1.0
 * 
 * @param string $file
 * @param string $tag
 * @param string $lang
 * @return string|null
 */
function sp_ads_admin_help($file, $tag, $lang) {
    if (strpos($tag, 'ads-') === 1) {
        return SPPLUGINDIR . '/ads/admin/sp-ads-admin-help.' . $lang;
    }
}

/**
 * Loads and calls function sp_ads_reporting_table
 * 
 * @since 1.0
 * 
 */
function sp_ads_report_ad_ajax() {
    require_once SPPLUGINDIR . '/ads/admin/sp-ads-reporting.php';
    die(sp_ads_reporting_ajax());
}
