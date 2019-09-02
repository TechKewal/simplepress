<?php

/*
  Simple:Press
  ADS Plugin Admin Save Routine
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-selected-form-ajax.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-date-ranges-form-ajax.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-locations-form-ajax.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-user-groups-form-ajax.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-keywords-form-ajax.php';

/**
 * Trying to save an ad set
 * 
 * Handling request and send json response
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_set_edit_form_ajax() {
    check_admin_referer('ad-set-edit', 'ad-set-edit');
    $adSetId = !empty($_GET['id']) ? SP()->filters->integer($_GET['id']) : null;
    if (!$adSetId) {
        sp_ads_error_resp_json(__('Ad Set id Not Found', 'sp-ads'));
    }
    $name = isset($_POST['name']) ? SP()->filters->str($_POST['name']) : '';
    if (!mb_strlen($name)) {
        sp_ads_error_resp_json(__('No Ad Set Updated', 'sp-ads') . ': ' . __('empty ad set name', 'sp-ads'));
    }
    if (!SP_Ads_Database::isUniqueAdSetName($name, $adSetId)) {
        sp_ads_error_resp_json(__('No Ad Set Updated', 'sp-ads') . ': ' . __('ad set with this name already exists', 'sp-ads'));
    }
    if (SP_Ads_Database::updateAdSet($adSetId, $name, !empty($_POST['is_active']), !empty($_POST['combine']))) {
        # save selected forums, topics, posts
        sp_ads_save_selected_forums_form_ajax($adSetId);
        # save date ranges
        sp_ads_save_date_ranges_form_ajax($adSetId);
        # save locations
        sp_ads_save_locations_form_ajax($adSetId);
        # save user groups
        sp_ads_save_user_groups_form_ajax($adSetId);
        # save keywords
        sp_ads_save_keywords_form_ajax($adSetId);

        sp_ads_success_resp_json(__('Ad Set Updated', 'sp-ads'));
    }
    sp_ads_error_resp_json(__('No Ad Set Updated', 'sp-ads'));
}
