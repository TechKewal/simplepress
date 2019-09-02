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
 * Trying to save ad keywords for ad set
 * 
 * @since 1.0
 */
function sp_ads_save_keywords_form_ajax($adSetId) {
    $keywords = (!empty($_POST['keywords']) && is_array($_POST['keywords'])) ? $_POST['keywords'] : array();
    $keywords = array_map(function($word) {
        return sp_ads_filter_word($word, SP_Ads_Database::MIN_LEN_WORD);
    }, $keywords);
    SP_Ads_Database::saveAdsetKeywords((int) $adSetId, $keywords);
}
