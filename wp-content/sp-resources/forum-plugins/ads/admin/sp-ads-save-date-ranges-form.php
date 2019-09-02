<?php
/*
  Simple:Press
  Topic ADS plugin ajax routine for management functions
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
function sp_ads_save_date_ranges_form($adSetId = null) {
    spa_paint_open_fieldset(__('Date Limitations', 'sp-ads'), true, 'ads-date-limitations');
    ?>
    <div class="sfform-submit-bar">
        <input type="button" class="button-primary" id="add-range" name="add-range" value="<?php echo __('Add date range', 'sp-ads') ?>" />
    </div>
    <div class="clearboth"></div>
    <?php
    sp_ads_date_ranges_table($adSetId);
    spa_paint_close_fieldset();
    echo '<div class="sfform-panel-spacer"></div>';
}

/**
 * Shows date ranges table
 *
 * @since 1.0
 *
 * @param int $adSetId
 */
function sp_ads_date_ranges_table($adSetId) {
    $items = array();
    if ($adSetId) {
        $items = (array) SP_Ads_Database::getAdSetDateRanges($adSetId);
    }
    ?>
    <table class="ads wp-list-table widefat fixed striped spMobileTable1280 m-0" id="dl-range-list">
        <thead><?php sp_ads_date_ranges_table_row_th() ?></thead>
        <tbody id="the-list">
            <?php foreach ($items as $item): ?>
                <?php sp_ads_date_ranges_table_row($item->date_range_id, $item->dt_from, $item->dt_to) ?>
            <?php endforeach ?>
        </tbody>
        <tfoot><?php sp_ads_date_ranges_table_row_th() ?></tfoot>
    </table>
    <script id="sf-ad-range-row-tmpl" type="javascript/template">
    <?php sp_ads_date_ranges_table_row() ?>
    </script>
    <?php
}

/**
 * Shows date ranges table row
 *
 * @since 1.0
 *
 * @param int $dtRangeId [optional]
 * @param string $dtFrom [optional]
 * @param string $dtTo [optional]
 */
function sp_ads_date_ranges_table_row($dtRangeId = null, $dtFrom = null, $dtTo = null) {
    ?>
    <tr class="spMobileTableData">
        <td data-label="ID">
            <?php echo $dtRangeId ?: '--' ?>
            <div class="row-actions">
                <span class="delete">
                    <a href="javascript:void(0);" class="spDeleteDates">Delete</a>
                </span>
            </div>
        </td>
        <td data-label="From">
            <input type="date" class="wp-core-ui" name="dt_from[<?php echo $dtRangeId ?>]"
                   value="<?php echo date_format(date_create($dtFrom), "Y-m-d") ?>">
        </td>
        <td data-label="To">
            <input type="date" class="wp-core-ui" name="dt_to[<?php echo $dtRangeId ?>]"
                   value="<?php echo date_format(date_create($dtTo), "Y-m-d") ?>">
        </td>
    </tr>
    <?php
}

/**
 * Shows date ranges table row th
 *
 * @since 1.0
 *
 */
function sp_ads_date_ranges_table_row_th() {
    ?>
    <tr>
        <th><span><?php echo __('ID', 'sp-ads') ?></span></th>
        <th><span><?php echo __('From', 'sp-ads') ?></span></th>
        <th><span><?php echo __('To', 'sp-ads') ?></span></th>
    </tr>
    <?php
}
