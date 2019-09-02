<?php
/*
Simple:Press
Event Logger Plugin Support Routines
$LastChangedDate: 2017-09-03 15:32:48 -0500 (Sun, 03 Sep 2017) $
$Rev: 15536 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_logger_set_up_logging() {
	$logger = SP()->options->get('logger');

    # check for core event logging
    if ($logger['postedited']) add_action('sph_post_edit_after_save', 'sp_logger_post_edit');
    if ($logger['topicedited']) add_action('sph_topic_title_edited',  'sp_logger_topic_edit', 10, 3);
    if ($logger['postdeleted']) add_action('sph_post_delete',         'sp_logger_post_delete', 10, 2);
    if ($logger['topicdeleted']) add_action('sph_topic_delete',       'sp_logger_topic_delete', 10, 3);
    if ($logger['postmoved']) add_action('sph_move_post',             'sp_logger_post_move', 10, 6);
    if ($logger['topicmoved']) add_action('sph_move_topic',           'sp_logger_topic_move', 10, 4);
    if ($logger['postapproved']) add_action('sph_post_approved',      'sp_logger_post_approve', 10, 2);
    if ($logger['postapproved']) add_action('sph_post_unapproved',    'sp_logger_post_unapprove', 10, 2);
    if ($logger['postreassigned']) add_action('sph_reassign_post',    'sp_logger_post_reassign', 10, 4);
    if ($logger['postcreated']) add_action('sph_post_create',         'sp_logger_post_create');

    # now check for hook logging
    if (!empty($logger['hooks'])) {
        foreach ($logger['hooks'] as $hook) {
            add_action($hook['name'], 'sp_logger_hook_processor',$hook['pri'], 10);
        }
    }
}

function sp_logger_post_edit($newpost) {
    $data = array();
    $data['newpost'] = $newpost;
    sp_logger_write_log('Post Edited', $data);
}

function sp_logger_topic_edit($topicid, $topicname, $userid) {
    $data = array();
    $data['topic_id'] = $topicid;
    $data['topic_name'] = $topicname;
    $data['edited_by'] = $userid;
    sp_logger_write_log('Topic Edited', $data);
}

function sp_logger_post_delete($post, $userid) {
    $data = array();
    $data['post_data'] = $post;
    $data['deleted_by'] = $userid;
    sp_logger_write_log('Post Deleted', $data);
}

function sp_logger_topic_delete($posts, $topicid, $userid) {
    $data = array();
    $data['topic_id'] = $topicid;
    $data['posts_deleted'] = $posts;
    $data['deleted_by'] = $userid;
    sp_logger_write_log('Topic Deleted', $data);
}

function sp_logger_post_move($oldtopicid, $newtopicid, $newforumid, $oldforumid, $postid, $userid) {
    $data = array();
    $data['post_id'] = $postid;
    $data['old_topic'] = $oldtopicid;
    $data['new_topic'] = $newtopicid;
    $data['old_forum'] = $oldforumid;
    $data['new_forum'] = $newforumid;
    $data['moved_by'] = $userid;
    sp_logger_write_log('Post Moved', $data);
}

function sp_logger_topic_move($currenttopicid, $currentforumid, $targetforumid, $userid) {
    $data = array();
    $data['topic_id'] = $currenttopicid;
    $data['old_forum'] = $currentforumid;
    $data['new_forum'] = $targetforumid;
    $data['moved_by'] = $userid;
    sp_logger_write_log('Topic Moved', $data);
}

function sp_logger_post_approve($approved_posts, $userid) {
    $data = array();
    $data['posts'] = $approved_posts;
    $data['approved_by'] = $userid;
    sp_logger_write_log('Post Approved', $data);
}

function sp_logger_post_unapprove($post_id, $userid) {
    $data = array();
    $data['post_id'] = $post_id;
    $data['unapproved_by'] = $userid;
    sp_logger_write_log('Post Unapproved', $data);
}

function sp_logger_post_reassign($postid, $olduserid, $newuserid, $userid) {
    $data = array();
    $data['postid'] = $postid;
    $data['old_user_id'] = $olduserid;
    $data['new_user_id'] = $newuserid;
    $data['reassigned_by'] = $userid;
    sp_logger_write_log('Post Reassigned', $data);
}

function sp_logger_post_create($newpost) {
    $data = array();
    $data['newpost'] = $newpost;
    sp_logger_write_log('Post Created', $data);
}

function sp_logger_hook_processor($arg1='', $arg2='', $arg3='', $arg4='', $arg5='', $arg6='', $arg7='', $arg8='', $arg9='', $arg10='') {
    $thisHook = current_filter();

    $data = array();
    $data['hook_args'] = compact('arg1', 'arg2', 'arg3', 'arg4', 'arg5', 'arg6', 'arg7', 'arg8', 'arg9', 'arg10');
    sp_logger_write_log($thisHook, $data);

	$logger = SP()->options->get('logger');
    foreach ($logger['hooks'] as $index => $hook) {
        if ($hook['name'] == $thisHook and function_exists($hook['callback'])) {
            call_user_func($hook['callback'], $thisHook, $arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7, $arg8, $arg9, $arg10);
            break;
        }
    }
}

function sp_logger_do_admin_cap_list($user) {
	$manage_log = user_can($user, 'SPF Manage Logger');
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Event Log', 'sp-logger'), "manage-log[$user->ID]", $manage_log, $user->ID);
	echo "<input type='hidden' name='old-log[$user->ID]' value='$manage_log' />";
	echo '</li>';
}

function sp_logger_do_admin_cap_form($user) {
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Logger', 'sp-logger'), 'add-log', 0);
	echo '</li>';
}

function sp_logger_do_admin_caps_update($still_admin, $remove_admin, $user) {
    $manage_log = (isset($_POST['manage-log'])) ? $_POST['manage-log'] : '';
    $old_log = (isset($_POST['old-log'])) ? $_POST['old-log'] : '';

    # was this admin removed?
    if (isset($remove_admin[$user->ID])) $manage_log = '';

	if (isset($manage_log[$user->ID])) {
		$user->add_cap('SPF Manage Logger');
	} else {
		$user->remove_cap('SPF Manage Logger');
	}
	$still_admin = $still_admin || isset($manage_log[$user->ID]);
	return $still_admin;
}

function sp_logger_do_admin_caps_new($newadmin, $user) {
    $log = (isset($_POST['add-log'])) ? $_POST['add-log'] : '';
	if ($log == 'on') $user->add_cap('SPF Manage Logger');
	$newadmin = $newadmin || $log == 'on';
	return $newadmin;
}
