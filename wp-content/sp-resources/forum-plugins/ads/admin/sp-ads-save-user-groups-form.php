<?php

/*
  Simple:Press
  ADS plugin ajax routine for management functions
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Shows part of form
 * 
 * @since 1.0
 *
 * @param int $adSetId [optional]
 */
function sp_ads_save_user_groups_form($adSetId = null) {
    $adUserGroupsIds = SP_Ads_Database::getAdSetBelongs('usergroup', (int) $adSetId);
    spa_paint_open_fieldset(__('User Groups', 'sp-ads'), true, 'ads-user-group');
    foreach (SP_Ads_Database::getUserGroups() as $ug) {
        spa_paint_checkbox($ug->usergroup_name, "usergroups[{$ug->usergroup_id}]", in_array($ug->usergroup_id, $adUserGroupsIds));
    }
    spa_paint_close_fieldset();
    echo '<div class="sfform-panel-spacer"></div>';
}
