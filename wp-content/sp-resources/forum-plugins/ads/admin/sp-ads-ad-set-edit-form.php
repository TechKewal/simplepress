<?php
/*
  Simple:Press
  Topic ADS plugin ajax routine for management functions
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-selected-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-date-ranges-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-locations-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-user-groups-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-keywords-form.php';

/**
 * 
 * Shows ad set edit form
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_set_edit_form() {
    if (empty($_GET['id'])) {
        sp_ads_error_resp_json(__('Empty ad set id', 'sp-ads'));
    }
    $adSet = SP_Ads_Database::getAdSetById($_GET['id']);
    if (!$adSet) {
        sp_ads_error_resp_json(__('Ad Set Not Found', 'sp-ads'));
    }
    ?>
    <form action="<?php echo sp_ads_url_ajax_ad_set_edit($adSet->ad_set_id) ?>" method="post" id="ad-set-edit-form" name="sfadsetsave">
        <?php echo sp_create_nonce('ad-set-edit'); ?>
        <?php
        spa_paint_options_init();

        spa_paint_open_tab(__('AD Sets', 'sp-ads') . ' - ' . __('Edit Ad Set', 'sp-ads'), true);
        spa_paint_open_panel();
        spa_paint_open_fieldset(__('Edit Ad Set', 'sp-ads'), true, 'ads-ad-set-edit');
        spa_paint_input(__('Name', 'sp-ads'), 'name', $adSet->name);

        spa_paint_checkbox(__('Is Active', 'sp-ads'), 'is_active', $adSet->is_active);
        spa_paint_checkbox(__('Combine Ad Set with others Ad Sets', 'sp-ads'), 'combine', $adSet->combine);
		
        sp_ads_save_date_ranges_form($adSet->ad_set_id);
        sp_ads_save_locations_form($adSet->ad_set_id);
        sp_ads_save_user_groups_form($adSet->ad_set_id);
        sp_ads_save_keywords_form($adSet->ad_set_id);

        sp_ads_save_selected_forums_form($adSet->ad_set_id);
        
        spa_paint_close_fieldset();
        spa_paint_close_panel();
        spa_paint_tab_right_cell();
        spa_paint_close_container();
        ?>
        <div class="sfform-submit-bar">
            <input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php echo __('Update', 'sp-ads') ?>" />
        </div>
    </form>
    <span id="ads-reload-ad-set-edit"
          data-ads-load-form="sp_ads_ad_set_edit"
          data-id="<?php echo $adSet->ad_set_id ?>"
          ></span>
    <?php
}
