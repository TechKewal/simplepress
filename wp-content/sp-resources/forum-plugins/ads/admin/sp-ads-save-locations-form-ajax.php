<?php

/*
  Simple:Press
  ADS Plugin Support Routines
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Handling request
 * 
 * Trying to save locations of ad set
 * 
 * @since 1.0
 *
 * @param int $adSetId
 */
function sp_ads_save_locations_form_ajax($adSetId) {
    $locationHooks = array();
    foreach (SP_Ads_Showing_Ad::getLocationsHooks() as $hook => $_) {
        if (!empty($_POST[$hook])) {
            if (SP_Ads_Showing_Ad::isDynamicHook($hook)) {
                $inptName = $hook . '_num';
                if (!empty($_POST[$inptName])) {
                    foreach (explode(',', $_POST[$inptName]) as $v) {
                        $num = (int) $v;
                        $v && array_push($locationHooks, SP_Ads_Showing_Ad::buildDynamicHookName($hook, $num));
                    }
                }
            } else {
                array_push($locationHooks, $hook);
            }
        }
    }
    SP_Ads_Database::saveAdSetLocationHooks((int) $adSetId, $locationHooks);
}
