<?php
/*
  Simple:Press
  Topic ADS plugin ajax routine for management functions
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

require_once SPPLUGINDIR . '/ads/admin/sp-ads-list-table.php';

/**
 * Shows list ads
 * 
 * @since 1.0
 * 
 */
function sp_ads_list_form() {
    spa_paint_options_init();
    spa_paint_open_tab(__('AD Sets', 'sp-ads') . ' - ' . __('List Ads', 'sp-ads'), true);
    $items = array();
    $adSetId = !empty($_GET['id']) ? SP()->filters->integer($_GET['id']) : null;
    $adSet = SP_Ads_Database::getAdSetById($adSetId);
    if ($adSet) {
        spa_paint_open_fieldset(__('Filters Reporting', 'sp-ads'), true, 'ads-filter');
        sp_ads_reporting_filters($adSetId);
        spa_paint_close_fieldset();

        $items = SP_Ads_Database::getAds($adSet->ad_set_id);
        spa_paint_open_panel();
        spa_paint_open_fieldset(__('Ads List of Set', 'sp-ads') . ' "' . $adSet->name . '"', true, 'ads-list');
        ?>
        <div class="ads sfform-submit-bar">
            <button class="button-primary"
                    data-ads-load-form="sp_ads_ad_add"
                    data-id="<?php echo $adSet->ad_set_id ?>"
                    ><?php echo __('Add Ad', 'sp-ads') ?></button>
        </div>
        <div class="clearboth"></div>
        <?php
        sp_ads_list_table($items);
        ?>
        <form id="ad-delete-form"><?php echo sp_create_nonce('ad-delete-form'); ?></form>
        <span id="ads-reload-ads-list"
              data-ads-load-form="sp_ads_list"
              data-id="<?php echo $adSet->ad_set_id ?>">
        </span>
        <?php
        spa_paint_close_fieldset();
        echo '<div class="sfform-panel-spacer"></div>';
        spa_paint_close_panel();
    }
    spa_paint_close_container();
    spa_paint_close_tab();
}

/**
 * Shows reporting filters
 * 
 * @since 1.0
 * 
 * @param int $adSetId
 */
function sp_ads_reporting_filters($adSetId) {
    ?>
    <div class="ads-filters-reporting" data-id="<?php echo $adSetId ?>">
        <button id="sp-today" class="sp-filters-button"><?php echo __('Today', 'sp-ads') ?></button>
        <button id="sp-yesterday" class="sp-filters-button"><?php echo __('Yesterday', 'sp-ads') ?></button>
        <button id="sp-week" class="sp-filters-button"><?php echo __('Week', 'sp-ads') ?></button>
        <button id="sp-month" class="sp-filters-button"><?php echo __('Month', 'sp-ads') ?></button>
        <button id="sp-year" class="sp-filters-button"><?php echo __('Year', 'sp-ads') ?></button>
        <span id="sp-freeday" class="sp-filters-label">
            <span id="sp-freeday1"><?= date('Y-m-d') ?></span>
            <span id="sp-freeday0">&nbsp;&nbsp;-&nbsp;&nbsp;</span>
            <span id="sp-freeday2"></span>
        </span>
    </div>
    <?php
}
