<?php
/*
Simple:Press
Admin Bar plugin ajax routine for auto update
$LastChangedDate: 2017-02-11 15:38:34 -0600 (Sat, 11 Feb 2017) $
$Rev: 15188 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# get out of here if no target specified
$target = (isset($_GET['target'])) ? $_GET['target'] : '';
if (empty($target)) die();

# Update the New Post Counts
if ($target == 'newposts') {
    sp_forum_ajax_support();

    if (SP()->user->thisUser->admin || SP()->user->thisUser->moderator) {
        $item = (isset($_GET['item'])) ? $_GET['item'] : '';
        if (empty($item)) die();

        require_once SPABLIBDIR.'sp-admin-bar-components.php';
        $newposts = sp_GetAdminsQueuedPosts();
        $counts = sp_GetWaitingNumbers($newposts);

        if ($item == 'unread') {
            $adminClass = ($counts['read'] > 0) ? 'spUnreadUnread' : 'spUnreadRead';
            echo "<span class='$adminClass'>{$counts['read']} </span>";
        }

        if ($item == 'mod') {
            $adminClass = ($counts['mod'] > 0) ? 'spModUnread' : 'spModRead';
            echo "<span class='$adminClass'>{$counts['mod']} </span>";
        }

        if ($item == 'spam') {
            $adminClass = ($counts['spam'] > 0) ? 'spSpamUnread' : 'spSpamRead';
            echo "<span class='$adminClass'>{$counts['spam']} </span>";
        }
    }

	die();
}

die();
?>