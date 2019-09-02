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
 * Trying to save ad set for user groups
 * 
 * @since 1.0
 *
 * @param int $adSetId
 */
function sp_ads_save_user_groups_form_ajax($adSetId) {
    $usergroups = (!empty($_POST['usergroups']) && is_array($_POST['usergroups'])) ? array_keys($_POST['usergroups']) : null;
    SP_Ads_Database::saveAdSetBelongs('usergroup', (int) $adSetId, $usergroups);
}
