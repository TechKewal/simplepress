<?php

/*
  Simple:Press
  ADS Plugin Admin Save Routine
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Trying to delete ad
 * 
 * Handling request and send json response
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_delete_form_ajax() {
    check_admin_referer('ad-delete-form', 'ad-delete-form');
    $adId = !empty($_GET['id']) ? SP()->filters->integer($_GET['id']) : null;
    if (!$adId) {
        sp_ads_error_resp_json(__('Ad Not Found', 'sp-ads'));
    }
    if (SP_Ads_Database::deleteAd($adId)) {
        sp_ads_success_resp_json(__('Ad Deleted', 'sp-ads'));
    }
    sp_ads_error_resp_json(__('No Ad Deleted', 'sp-ads'));
}
