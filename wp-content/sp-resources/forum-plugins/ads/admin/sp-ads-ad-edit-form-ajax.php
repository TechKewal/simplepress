<?php

/*
  Simple:Press
  ADS Plugin Admin Save Routine
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Trying to save an ad
 * 
 * Handling request and send json response
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_edit_form_ajax() {
    check_admin_referer('edit-ad', 'edit-ad');
    $adId = !empty($_GET['id']) ? SP()->filters->integer($_GET['id']) : null;
    if (!$adId || (!$ad = SP_Ads_Database::getAdById($adId))) {
        sp_ads_error_resp_json(__('Ad Not Found', 'sp-ads'));
    }
    $name = isset($_POST['name']) ? SP()->filters->str($_POST['name']) : '';
    if (!mb_strlen($name)) {
        sp_ads_error_resp_json(__('No Ad Updated', 'sp-ads') . ': ' . __('empty ad name', 'sp-ads'));
    }
    if (!SP_Ads_Database::isUniqueAdName($name, $ad->ad_set_id, $adId)) {
        sp_ads_error_resp_json(__('No Ad Updated', 'sp-ads') . ': ' . __('ad with this name already exists', 'sp-ads'));
    }
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    if (!mb_strlen($content)) {
        sp_ads_error_resp_json(__('No Ad Updated', 'sp-ads') . ': ' . __('empty ad content', 'sp-ads'));
    }
    $maxViews = !empty($_POST['max_views']) ? SP()->filters->integer($_POST['max_views']) : '';
    $size = !empty($_POST['size']) ? SP()->filters->str($_POST['size']) : null;
    $isActive = !empty($_POST['is_active']);
    $scriptAllowed = !empty($_POST['script_allowed']);
    if (SP_Ads_Database::updateAd($adId, $name, $content, $maxViews, $size, $isActive, $scriptAllowed)) {
        sp_ads_success_resp_json(__('Ad Updated', 'sp-ads'));
    }
    sp_ads_error_resp_json(__('No Ad Updated', 'sp-ads'));
}
