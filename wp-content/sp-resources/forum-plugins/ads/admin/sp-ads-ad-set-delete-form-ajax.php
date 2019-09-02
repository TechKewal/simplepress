<?php

/*
  Simple:Press
  ADS Plugin Admin Save Routine
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Trying to delete ad set
 * 
 * Handling request and send json response
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_set_delete_form_ajax() {
    check_admin_referer('ad-set-delete-form', 'ad-set-delete-form');
    $adSetId = !empty($_GET['id']) ? SP()->filters->integer($_GET['id']) : null;
    if (!$adSetId) {
        sp_ads_error_resp_json(__('Ad Set Not Found', 'sp-ads'));
    }
    if (SP_Ads_Database::deleteAdSet($adSetId)) {
        sp_ads_success_resp_json(__('Ad Set Deleted', 'sp-ads'));
    }
    sp_ads_error_resp_json(__('No Ad Set Deleted', 'sp-ads'));
}
