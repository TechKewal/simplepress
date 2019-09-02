<?php

/*
  Simple:Press
  ADS Plugin Support Routines
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Trying to save date ranges of ad set
 * 
 * Handling request
 * 
 * @since 1.0
 * 
 * @param int $adSetId
 */
function sp_ads_save_date_ranges_form_ajax($adSetId) {
    if ($adSetId) {
        $dtRanges = array();
        if (!empty($_POST['dt_from'])) {
            foreach ((array) $_POST['dt_from'] as $dateRangeId => $dtFrom) {
                if ($dtFrom = date_create($dtFrom)) {
                    if (!empty($_POST['dt_to'][$dateRangeId]) && ($dtTo = date_create($_POST['dt_to'][$dateRangeId]))) {
                        $dtRanges[$dateRangeId] = array($dtFrom, $dtTo);
                    }
                }
            }
        }
        SP_Ads_Database::saveAdSetDateRanges((int) $adSetId, $dtRanges);
    }
}
