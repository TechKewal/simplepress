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
 * Shows ad set add form
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_set_add_form() {
    ?>
    <form action="<?php echo sp_ads_url_ajax_ad_set_add() ?>" method="post" id="ad-set-add-form" name="sfadsetsave">
        <?php echo sp_create_nonce('ad-set-add'); ?>
        <?php
        spa_paint_options_init();

        spa_paint_open_tab(__('AD Sets', 'sp-ads') . ' - ' . __('Add New Ad Set', 'sp-ads'), true);
        spa_paint_open_panel();
        spa_paint_open_fieldset(__('Add New Ad Set', 'sp-ads'), true, 'ads-ad-set-add');
        spa_paint_input(__('Name', 'sp-ads'), 'name', '');

        spa_paint_checkbox(__('Is Active', 'sp-ads'), 'is_active', 1);
        spa_paint_checkbox(__('Combine Ad Set with others Ad Sets', 'sp-ads'), 'combine', 0);
		
        sp_ads_save_date_ranges_form();
        sp_ads_save_locations_form();
        sp_ads_save_user_groups_form();
        sp_ads_save_keywords_form();

        sp_ads_save_selected_forums_form();

        spa_paint_close_fieldset();
        spa_paint_close_panel();
        spa_paint_tab_right_cell();
        spa_paint_close_container();
        ?>
        <div class="sfform-submit-bar">
            <input type="submit" class="button-primary" id="saveit" name="saveit" value="<?php echo __('Add', 'sp-ads') ?>" />
        </div>
    </form>
    <span id="ads-reload-ad-set-add"
          data-ads-load-form="sp_ads_ad_set_add"
          ></span>
    <?php
}
