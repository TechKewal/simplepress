<?php
/*
Simple:Press
Admin Bar plugin ajax routine for deleting spam and spam member
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();
global $wpdb;

if(isset($_GET['postid']) && isset($_GET['userid'])) {
	$postid = intval($_GET['postid']);
	$userid = intval($_GET['userid']);

	$spamPosts = SP()->DB->table(SPPOSTS, "user_id=$userid");

	# double check the post that sent us here IS in the list just in case...
	$found = false;
	if($spamPosts) {
		foreach($spamPosts as $post) {
			if($post->post_id == $postid) $found=true;
		}
	}
	if(!$spamPosts || $found==false) {
		_e('Spam posts not found', 'spab');
		die();
	}

	# remove posts bu this user
	reset($spamPosts);
	$removed = count($spamPosts);
	foreach($spamPosts as $post) {
		sp_delete_post($post->post_id, $post->topic_id, $post->forum_id, false);
		# we always need to remove from users new posts list
		sp_remove_users_newposts($post->topic_id, SP()->user->thisUser->ID, true);
	}

	# remove this user first SP and then WP
	SP()->user->delete_data($userid);
	if ( !is_multisite() ) {
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d", $userid) );
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->users WHERE ID = %d", $userid) );
	} else {
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; # wpmu site admins don't have user_levels
		$wpdb->query("DELETE FROM $wpdb->usermeta WHERE user_id = $userid AND meta_key = '{$level_key}'");
	}
	do_action('deleted_user', $userid);

	echo sprintf(__('%s Posts and User Removed', 'spab'), $removed);

} else {
	_e('Invalid Request', 'spab');
}

die();
