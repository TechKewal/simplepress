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
function sp_ads_save_locations_form($adSetId = null) {
    $adHooks = array();
    if ($adSetId) {
        foreach (SP_Ads_Database::getAdSetHooks((int) $adSetId) as $item) {
            if (preg_match('/(\w+)_(\d+)$/', $item->hook, $m)) {
                if (!isset($adHooks[$m[1]])) {
                    $adHooks[$m[1]] = array();
                }
                $adHooks[$m[1]][] = $m[2];
            } else {
                $adHooks[$item->hook] = true;
            }
        }
    }
    spa_paint_open_fieldset(__('Locations', 'sp-ads'), true, 'ads-locations');
    ?>
    <table class="wp-list-table widefat fixed striped">
        <?php foreach (SP_Ads_Showing_Ad::getLocationsHooks() as $hook => $description): ?>
            <?php if (!SP_Ads_Showing_Ad::isDynamicHook($hook)): ?>
                <tr>
                    <td colspan="3"><?php spa_paint_checkbox(__($description, 'sp-ads'), $hook, isset($adHooks[$hook])) ?></td>
                </tr>
            <?php else: ?>
                <?php if ($description == 'Add before %d Forum'): ?>
                    <tr>
                        <td colspan="3">• Between forums in the main forum page (One or more forums: 1, 2, ...)</td>
                    </tr>
                <?php elseif ($description == 'Add before %d Topic'): ?>
                    <tr>
                        <td colspan="3">• Between topics after clicking on a FORUM (One or more topics: 1, 2, ...)</td>
                    </tr>
                <?php elseif ($description == 'Add before %d Post'): ?>
                    <tr>
                        <td colspan="3">• Top of a post • Bottom of a post (One or more posts: 1, 2, ...)</td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td><?php spa_paint_checkbox(__(substr($description, 0, strpos($description, '%d')), 'sp-ads'), $hook, isset($adHooks[$hook])); ?></td>
                    <td><input type="text" class="wp-core-ui sp-input-60" name="<?php echo $hook ?>_num"
                               value="<?php echo!empty($adHooks[$hook]) ? implode(', ', $adHooks[$hook]) : '' ?>">              
                    </td>
                    <td><?php echo __(substr($description, strpos($description, '%d') + 2), 'sp-ads') ?></td>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
    </table>
    <?php
    spa_paint_close_fieldset();
    echo '<div class="sfform-panel-spacer"></div>';
}
