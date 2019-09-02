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
function sp_ads_save_keywords_form($adSetId = null) {
    $keywords = SP_Ads_Database::getAdSetKeywords($adSetId);
    spa_paint_open_fieldset(__('Key Words', 'sp-ads'), true, 'ads-keywords');
    ?>
    <div class="sfform-submit-bar">
        <input type="button" class="button-primary" id="sf-ads-add-keyword" value="<?php echo __('Add keyword', 'sp-ads') ?>">
    </div>
    <div id="sf-ads-keywords-container" class="sp-form-row">
        <?php foreach ($keywords as $keyword): ?>
            <input type="text" class="wp-core-ui ads-keyword" name="keywords[]" value="<?php echo $keyword ?>">
        <?php endforeach ?>
    </div>
    <script id="sf-ad-keyword-tmpl" type="javascript/template">
        <input type="text" class="wp-core-ui ads-keyword" name="keywords[]" value="">
    </script>
    <?php
    spa_paint_close_fieldset();
    echo '<div class="sfform-panel-spacer"></div>';
}
