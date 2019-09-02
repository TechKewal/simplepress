<?php
/*
  Simple:Press
  Topic ADS plugin ajax routine for management functions
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Shows list ads table
 * 
 * @since 1.0
 * 
 * @param array $items
 */
function sp_ads_list_table($items) {
    if (!$items) {
        echo __('Not Found', 'sp-ads');
    } else {
        ?>
        <table class="ads ads-list wp-list-table widefat fixed striped spMobileTable1280">
            <thead><?php sp_ads_list_table_row_th() ?></thead>
            <tbody id="the-list">
                <?php foreach ((array) $items as $item): ?>
                    <tr class="spMobileTableData">
                        <td data-label="ID">
                            <?php echo $item->ad_id ?>
                            <div class="row-actions">
                                <span class="edit">
                                    <a class="spAdsLoadForm" href="javascript:void(0);"
                                       data-ads-load-form="sp_ads_ad_edit"
                                       data-id="<?php echo $item->ad_id ?>"
                                       ><?php echo __('Edit', 'sp-ads') ?></a>
                                </span>|
                                <span class="delete">
                                    <a href="<?php echo sp_ads_url_ajax_ad_delete($item->ad_id) ?>"
                                       data-form-id="ad-delete-form"
                                       ><?php echo __('Delete', 'sp-ads') ?></a>
                                </span>
                            </div>
                        </td>
                        <td data-label="Name">
                            <?php echo $item->name ?>
                        </td>
                        <td data-label="Hits">
                            <?php echo $item->hits ?>
                        </td>
                        <td data-label="Max Views">
                            <?php echo $item->max_views ?: '--' ?>
                        </td>
                        <td data-label="Is Active">
                            <?php echo $item->is_active ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot><?php sp_ads_list_table_row_th() ?></tfoot>
        </table>
        <?php
    }
}

/**
 * Shows list ads table row th
 * 
 * @since 1.0
 * 
 */
function sp_ads_list_table_row_th() {
    ?>
    <tr>
        <th><span><?php echo __('ID', 'sp-ads') ?></span></th>
        <th><span><?php echo __('Name', 'sp-ads') ?></span></th>
        <th><span><?php echo __('Hits', 'sp-ads') ?></span></th>
        <th><span><?php echo __('Max Views', 'sp-ads') ?></span></th>
        <th><span><?php echo __('Is Active', 'sp-ads') ?></span></th>
    </tr>
    <?php
}
