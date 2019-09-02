<?php

/*
  Simple:Press
  ADS Plugin Support Routines
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * Handling request
 * 
 * Trying to save ad set for selected forums 
 * 
 * @since 1.0
 *
 * @param int $adSetId
 */
function sp_ads_save_selected_forums_form_ajax($adSetId) {
    $forums = (!empty($_POST['forums']) && is_array($_POST['forums'])) ? array_keys($_POST['forums']) : null;
    SP_Ads_Database::saveAdSetBelongs('forum', (int) $adSetId, $forums);

    $topics = array();
    if (!empty($_POST['topics']) && is_array($_POST['topics'])) {
        foreach ($_POST['topics'] as $_topics) {
            foreach ($_topics as $topic) {
                $topics [] = $topic;
            }
        }
    }
    SP_Ads_Database::saveAdSetBelongs('topic', (int) $adSetId, $topics);

    $posts = (!empty($_POST['posts']) && is_array($_POST['posts'])) ? array_keys($_POST['posts']) : null;
    SP_Ads_Database::saveAdSetBelongs('post', (int) $adSetId, $posts);
}
