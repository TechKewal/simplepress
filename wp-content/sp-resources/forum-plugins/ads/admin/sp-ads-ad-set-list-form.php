<?php
/*
  Simple:Press
  Topic ADS plugin ajax routine for management functions
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Shows ad set list form
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_set_list_form() {
    spa_paint_options_init();
    spa_paint_open_tab(__('AD Sets', 'sp-ads') . ' - ' . __('List Ad Sets', 'sp-ads'), true);
    spa_paint_open_panel();
    spa_paint_open_fieldset(__('Ad Sets', 'sp-ads'), true, 'ads-ad-set-list');
    ?>
    <div class="ads sfform-submit-bar">
        <button class="button-primary" data-ads-load-form="sp_ads_ad_set_add"><?php echo __('Add Ad Set', 'sp-ads') ?></button>
    </div>
    <div class="clearboth"></div>
    <?php
    sp_ads_ad_set_list_table();
    spa_paint_close_fieldset();
    echo '<div class="sfform-panel-spacer"></div>';
    spa_paint_close_panel();
    spa_paint_close_container();
    spa_paint_close_tab();
}

/**
 * Shows ad set list table
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_set_list_table() {
    $adSets = SP_Ads_Database::getAdSets();
    ?>
    <?php if (!$adSets): ?>
        <div>
            <span><?php echo __('Not Found', 'sp-ads') ?></span>
        </div>
    <?php else: ?>
        <table class="ads ads-ad-set-list wp-list-table widefat fixed striped spMobileTable1280">
            <thead><?php sp_ads_ad_sets_list_table_row_th() ?></thead>
            <tbody id="the-list">
                <?php foreach ($adSets as $adSet): ?>
                    <tr class="spMobileTableData">
                        <td data-label="ID">
                            <?php echo $adSet->ad_set_id ?>
                            <div class="row-actions">
                                <span class="ads-list">
                                    <a href="javascript:void(0);"
                                       data-ads-load-form="sp_ads_list"
                                       data-id="<?php echo $adSet->ad_set_id ?>"
                                       ><?php echo __('Ads list', 'sp-ads') ?></a>
                                </span>|
                                <span class="edit">
                                    <a href="javascript:void(0);"
                                       data-ads-load-form="sp_ads_ad_set_edit"
                                       data-id="<?php echo $adSet->ad_set_id ?>"
                                       ><?php echo __('Edit', 'sp-ads') ?></a>
                                </span>|
                                <span class="delete">
                                    <a href="<?php echo sp_ads_url_ajax_ad_set_delete($adSet->ad_set_id) ?>"
                                       data-form-id="ad-set-delete-form"
                                       ><?php echo __('Delete', 'sp-ads') ?></a>
                                </span>
                            </div>
                        </td>
                        <td data-label="Name">
                            <?php echo $adSet->name ?>
                        </td>
                         <td data-label="Count Ads">
                            <?php echo $adSet->count_ads ?>
                        </td>
                        <td data-label="Sum Hits">
                            <?php echo $adSet->hits ?>
                        </td>
                        <td data-label="Is Active">
                            <?php echo $adSet->is_active ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot><?php sp_ads_ad_sets_list_table_row_th() ?></tfoot>
        </table>
        <form id="ad-set-delete-form"><?php echo sp_create_nonce('ad-set-delete-form'); ?></form>
    <?php endif ?>
    <?php
}

/**
 * Shows list ads table row th
 * 
 * @since 1.0
 * 
 */
function sp_ads_ad_sets_list_table_row_th() {
    ?>
    <tr>
        <th><span><?php echo __('ID', 'sp-ads') ?></span></th>
        <th><span><?php echo __('Name', 'sp-ads') ?></span></th>
        <th><span><?php echo __('Count Ads', 'sp-ads') ?></span></th>
        <th><span><?php echo __('Sum Hits', 'sp-ads') ?></span></th>
        <th><span><?php echo __('Is Active', 'sp-ads') ?></span></th>
    </tr>
    <?php
}
