<?php
/*
  Simple:Press
  Topic ADS plugin ajax routine for management functions
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-date-ranges-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-locations-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-user-groups-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-keywords-form.php';

/**
 * Shows form edit ad
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_edit_form() {
    if (empty($_GET['id'])) {
        sp_ads_error_resp_json(__('Empty ad id', 'sp-ads'));
    }
    $ad = SP_Ads_Database::getAdById($_GET['id']);
    if (!$ad) {
        sp_ads_error_resp_json(__('Ad Not Found', 'sp-ads'));
    }
    ?>
    <form action="<?php echo sp_ads_url_ajax_ad_edit($ad->ad_id) ?>" method="post" id="ad-edit-form" name="sfadsave">
        <?php echo sp_create_nonce('edit-ad'); ?>
        <?php
        spa_paint_options_init();

        spa_paint_open_tab(__('AD Sets', 'sp-ads') . ' - ' . __('Edit ad', 'sp-ads'), true);
        spa_paint_open_panel();
        spa_paint_open_fieldset(__('Edit Ad of Set', 'sp-ads') . sprintf(': "%s"', $ad->set_name), true, 'ads-edit-ad');
        spa_paint_input(__('Name', 'sp-ads'), 'name', $ad->name);
        spa_paint_wide_editor(__('Ad Content', 'sp-ads'), 'content', $ad->content, '', 4, true);
        spa_paint_checkbox(__('Script Allowed', 'sp-ads') . '?', 'script_allowed', $ad->script_allowed);
        spa_paint_number(__('Display Quantity', 'sp-ads'), 'max_views', $ad->max_views ?: '');
        spa_paint_input(__('Size') . ' (inline style, example: width: 50%; height: 100px;)', 'size', $ad->size);

        spa_paint_checkbox(__('Is Active', 'sp-ads'), 'is_active', $ad->is_active);

        spa_paint_close_fieldset();
        spa_paint_close_panel();
        spa_paint_tab_right_cell();
        spa_paint_close_container();
        ?>
        <div class="sfform-submit-bar">
            <button class="button-primary"
                    data-ads-load-form="sp_ads_list"
                    data-id="<?php echo $ad->ad_set_id ?>"
                    ><?php echo __('Ads List', 'sp-ads') ?></button>
            <input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php echo __('Update', 'sp-ads') ?>" />
        </div>
    </form>
    <a id="ads-reload-ad-edit" class="spAdsLoadForm" href="javascript:void(0);"
       data-ads-load-form="sp_ads_ad_edit"
       data-id="<?php echo $ad->ad_id ?>"
       ></a>
    <?php
    spa_print_ajax_editor_settings();
}
