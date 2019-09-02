<?php

/*
  Simple:Press
  ADS plugin database routines (array hooks)
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

return array(
    'sph_AfterDisplayStart' => 'Add before all forum display',
    'sph_BeforeSectionStart_forumHead' => 'Add before SP Header',
    'sph_AfterSectionEnd_forumHead' => 'Add after SP Header',
    'sph_BeforeSectionStart_forumFoot' => 'Add before SP Footer',
    'sph_AfterSectionEnd_forumFoot' => 'Add after SP Footer',
    # dynamic actions >
    # all of this need added like: 'hook' . '_' . (int) $n
    # For example: 'sph_BeforeSectionStart_eachTopic_1'
    'sph_BeforeSectionStart_eachForum' => 'Add before %d Forum',
    'sph_AfterSectionEnd_eachForum' => 'Add after %d Forum',
    'sph_BeforeSectionStart_eachTopic' => 'Add before %d Topic',
    'sph_AfterSectionEnd_eachTopic' => 'Add after %d Topic',
    'sph_BeforeSectionStart_eachPost' => 'Add before %d Post',
    'sph_AfterSectionEnd_eachPost' => 'Add after %d Post',
        # < dynamic actions
);
