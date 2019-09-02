<?php
/*
  Simple:Press
  ADS plugin ajax routine for management functions
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-date-ranges-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-locations-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-user-groups-form.php';
require_once SPPLUGINDIR . '/ads/admin/sp-ads-save-keywords-form.php';

/**
 * Shows form add ad
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_add_form() {
    $adSetId = !empty($_GET['id']) ? SP()->filters->integer($_GET['id']) : null;
    if (!$adSetId) {
        die(__('Empty ad set id', 'sp-ads'));
    }
    $adSet = SP_Ads_Database::getAdSetById($adSetId);
    if (!$adSet) {
        die(__('Ad Set not found', 'sp-ads'));
    }
    ?>
    <form action="<?php echo sp_ads_url_ajax_ad_add($adSetId) ?>" method="post" id="ad-add-form" name="sfadsave">
        <?php echo sp_create_nonce('ad-add'); ?>
        <?php
        spa_paint_options_init();

        spa_paint_open_tab(__('AD Sets', 'sp-ads') . ' - ' . __('Add New', 'sp-ads'), true);
        spa_paint_open_panel();
        spa_paint_open_fieldset(__('Add New Ad to Set', 'sp-ads') . sprintf(': "%s"', $adSet->name), true, 'ads-add-ad');
        spa_paint_input(__('Name', 'sp-ads'), 'name', '');
        spa_paint_wide_editor(__('Ad Content', 'sp-ads'), 'content', '', '', 4, true);
        spa_paint_checkbox(__('Script Allowed', 'sp-ads') . '?', 'script_allowed', 0);
        spa_paint_number(__('Display Quantity', 'sp-ads'), 'max_views', '');
        spa_paint_input(__('Size') . ' (inline style, example: width: 50%; height: 100px;)', 'size', '');

        spa_paint_checkbox(__('Is Active', 'sp-ads'), 'is_active', 1);

        spa_paint_close_fieldset();
        spa_paint_close_panel();
        spa_paint_tab_right_cell();
        spa_paint_close_container();
        ?>
        <div class="sfform-submit-bar">
            <button class="button-primary"
                    data-ads-load-form="sp_ads_list"
                    data-id="<?php echo $adSetId ?>"
                    ><?php echo __('Ads List', 'sp-ads') ?></button>
            <input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php echo __('Add', 'sp-ads') ?>" />
        </div>
    </form>
    <span id="ads-load-ads-list"
          data-ads-load-form="sp_ads_list"
          data-id="<?php echo $adSetId ?>">
    </span>
    <?php
    spa_print_ajax_editor_settings();
}
