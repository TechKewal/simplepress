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
function sp_ads_save_selected_forums_form($adSetId = null) {
    spa_paint_open_fieldset(__('Forums and topics', 'sp-ads'), true, 'ads-select-forums-and-topics');
    sp_ads_save_selected_teble_forums($adSetId);
    spa_paint_close_fieldset();
}

/**
 * Shows a table of selected forums.
 * 
 * @since 1.0
 *
 * @param int $adSetId
 */
function sp_ads_save_selected_teble_forums($adSetId) {
    $forums = SP_Ads_Database::getForums();
    $adSetForumsIds = $adSetId ? SP_Ads_Database::getAdSetBelongs('forum', (int) $adSetId) : array();
    ?>
    <table id="ads-selected-table" class="ads wp-list-table widefat fixed">
        <tr>
            <td><?php spa_paint_checkbox(__('Main forum page', 'sp-ads'), "forums[]", in_array(0, $adSetForumsIds)) ?></td>
        </tr>
        <?php if ($forums): ?>
            <?php foreach ($forums as $forum): ?>
                <tr>
                    <td><?php spa_paint_checkbox(sprintf('#%d %s', $forum->forum_id, $forum->forum_name), "forums[{$forum->forum_id}]", in_array($forum->forum_id, $adSetForumsIds)) ?></td>
                </tr>
                <?php if (SP_Ads_Database::getCountForumTopics($forum->forum_id)): ?>
                    <tr class="topics-list">
                        <td><?php sp_ads_save_select_topics($forum->forum_id, $adSetId) ?></td>
                    </tr>
                <?php endif ?>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td><?php echo __('Forums Not found', 'sp-ads') ?></td>
            </tr>
        <?php endif ?>
    </table>
    <?php
}

/**
 * Shows selected topics
 * 
 * @since 1.0
 *
 * @param int $forumId
 * @param int $adSetId
 */
function sp_ads_save_select_topics($forumId, $adSetId) {
    $topics = $adSetId ? SP_Ads_Database::getTopics($forumId, $adSetId) : array();
    ?>
    <div class="ml-10 select-topics-root">
        <select class="js-select2-data-ajax"
                data-url="<?php echo sp_ads_url_ajax_select2_topics($forumId) ?>"
                name="topics[<?php echo $forumId ?>][]" multiple="multiple">
                    <?php foreach ($topics as $topic): ?>
                <option value="<?php echo $topic->topic_id ?>" selected>
                    <?php echo sprintf('#%d %s', $topic->topic_id, $topic->topic_name) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
    <?php
}
