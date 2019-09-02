<?php

/*
  Simple:Press
  Topic ADS plugin ajax routine for management functions
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

require_once SPPLUGINDIR . '/ads/admin/sp-ads-list-table.php';

/**
 * Shows reporting table
 * 
 * @since 1.0
 * 
 */
function sp_ads_reporting_ajax() {
    $adSetId = !empty($_REQUEST['ad_set_id']) ? SP()->filters->integer($_REQUEST['ad_set_id']) : null;
    $dtFrom = !empty($_REQUEST['date_from']) ? date_create($_REQUEST['date_from']) : null;
    $dtTo = !empty($_REQUEST['date_to']) ? date_create($_REQUEST['date_to']) : null;
    $items = SP_Ads_Database::getAds($adSetId, $dtFrom, $dtTo);
    if (!$items) {
        echo __('Not Found', 'sp-ads');
    } else {
        sp_ads_list_table($items);
    }
}
